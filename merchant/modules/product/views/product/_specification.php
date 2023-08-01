<?php

use yii\helpers\BaseUrl;
use yii\helpers\Json;
use common\helpers\AddonHelper;
use common\helpers\Url;
use common\helpers\Html;
use addons\TinyShop\common\enums\SpecTypeEnum;
use addons\TinyShop\common\enums\ProductStockDeductionTypeEnum;

?>

<style>
    .spec-value:hover .del-spec-value{
        display: inline;
    }

    .spec-value-title {
        min-width: 55px;
    }

    .del-spec-value {
        margin-left: -15px;
        margin-top: -7px;
        position: absolute;
        display: none;
    }
</style>

<div id="spec">
    <div class="row">
        <div class="col-sm-6"><?= $form->field($model, 'stock')->textInput([
                'readonly' => !empty($model->is_spec),
            ])->hint('商品的剩余数量, 如启用多规格，则此处设置无效.'); ?></div>
        <div class="col-sm-6"><?= $form->field($model, 'stock_warning_num')->textInput(); ?></div>
    </div>
    <?= $form->field($model, 'stock_deduction_type')->radioList(ProductStockDeductionTypeEnum::getMap())
        ->hint('付款减库存: 买家提交订单，扣减库存数量，可能存在恶意占用库存风险。商品参加“拼团”活动时，默认为付款减库存。<br>拍下减库存: 买家支付成功扣减库存数量，可能存在超卖风险。可以设置人工处理超卖订单。商品参加“砍价”活动时，默认为拍下减库存。')
    ?>
    <?= $form->field($model, 'is_stock_visible')->checkbox() ?>
    <?= $form->field($model, 'is_spec')->radioList([0 => '统一规格', 1 => '多规格'], [
            '@change' => "isSpec()",
        ])->hint('启用商品规格后，商品的价格及库存以商品规格为准,库存设置为0则会到”已售罄“中，不会显示');
    ?>
    <div class="row" v-if="isSpecVal == 0">
        <div class="col-sm-4">
            <?= $form->field($model, 'price')->textInput([
                'value' => $model->isNewRecord ? '' : $model->value
            ])->hint('商品没有相关优惠活动的实际卖价'); ?>
        </div>
        <div class="col-sm-4"><?= $form->field($model, 'market_price')->textInput()->hint('商品没有优惠的情况下，在商品详情会以划线形式显示。'); ?></div>
        <div class="col-sm-4"><?= $form->field($model, 'cost_price')->textInput()->hint('成本价将不会对前台会员展示，用于商家统计使用'); ?></div>
        <div class="col-sm-3"><?= $form->field($model, 'weight')->textInput()->hint('公斤(kg)'); ?></div>
        <div class="col-sm-3"><?= $form->field($model, 'volume')->textInput()->hint('立方米(m³)'); ?></div>
        <div class="col-sm-3"><?= $form->field($model, 'sku_no')->textInput(); ?></div>
        <div class="col-sm-3"><?= $form->field($model, 'barcode')->textInput()->hint('用于快速识别商品所标记的唯一编码，比如：69开头的13位标准码。'); ?></div>
    </div>
    <div v-else>
        <?= $form->field($model, 'spec_template_id')->dropDownList($specTemplate, [
            'prompt' => '请选择',
            '@change' => "initParams()",
            'v-model' => "specTemplateId"
        ])->hint('商品可以添加自定义规格，也可以通过规格模板批量设置规格');
        ?>
        <table class="table">
            <tbody>
            <tr v-for="(list, index) in specList">
                <td style="min-width: 80px;">{{list.title}}</td>
                <td>
                    <span v-for="(item, key) in list.value" class="spec-value">
                        <span :class="item.pitch_on > 0 ? 'btn btn-primary btn-sm spec-value-title' : 'btn btn-white btn-sm spec-value-title'" @click="addSku(item.id, item.title, list.id, list.title, index, key)">{{item.title}}</span>
                        <span :class="item.pitch_on > 0 ? 'hide' : 'del-spec-value'" @click="delSpecValue(index, key)"><i class="fa fa-times-circle"></i></span>
                        <span v-if="list.type && list.type == 2" @click="selectColor(index, key)" class="btn btn-sm selectColor" style="background:#000000 ;padding: 10px" data-href="<?= Url::to(['select-color', 'value' => ''])?>"></span>
                        <img :src="item.data.length > 0 ? item.data : selectImageSrc" v-if="list.type && list.type == 3" @click="selectImage(index, key)" class="selectImage openUploadIframe" style="margin-right: 10px" href="<?= BaseUrl::to(['/files/selector', 'box_id' => 'TinyShop', 'upload_type' => 'images'])?>">
                    </span>
                    <a href="javascript:void(0)" class="specValue blue" @click="addSpecValue(index)" data-toggle="modal" data-target="#specValue">+ 规格值</a>
                </td>
                <td style="min-width: 200px">
                    <a href="javascript:void(0)" class="blue" v-if="list.type == 1" @click="setSpecType(index, 3)">添加图片</a>
                    <a href="javascript:void(0)" class="blue" v-if="list.type == 3" @click="setSpecType(index, 1)">取消图片</a>
                    <a href="javascript:void(0)" class="blue" v-if="list.pitch_on_count == 0" @click="delSpec(index)">删除</a>
                </td>
            </tr>
            <tr>
                <td colspan="3" class="spec blue pointer" @click="addSpec()" data-toggle="modal" data-target="#specCreate">+ 添加规格</td>
            </tr>
            </tbody>
        </table>
        <div class="hint-block">点击按钮进行规格值设置, 按钮选择的情况下规格值才会被保存</div>
    </div>
    <div class="row sku" v-if="skuHeaders.length > 0 && isSpecVal == 1">
        <div class="col-lg-12">
            <hr>
            <div class="form-group">
                <label class="control-label">商品规格库存</label>
                <div class="help-block"></div>
            </div>
           <table class="table table-bordered">
               <tr style="height: 51px">
                   <td>
                       批量设置：
                   </td>
                   <td :colspan="skuColspan" style="text-align:left;">
                       <div class="batch-opts">
                            <span class="js-batch-type">
                                <a v-for="(title, index) in batchTypeList" class="blue" href="javascript:void (0);" @click="batch(index)">{{title}}</a>
                            </span>
                            <span class="js-batch-form input-group hide">
                                <input type="text" maxlength="11" class="form-control js-batch-txt input-sm m-r-xs" v-model="batchVal" style="max-width:200px;">
                                <a class="btn btn-primary m-r-xs" href="javascript:void (0);" @click="batchSave()">保存</a>
                                <a class="btn btn-white" href="javascript:void (0);" @click="batchCancel()">取消</a>
                            </span>
                       </div>
                   </td>
               </tr>
               <tr>
                   <th v-for="(title, index) in skuHeaders">
                       {{title}}
                   </th>
                   <th>Sku 图片</th>
                   <th>销售价(元)</th>
                   <th>划线价(元)</th>
                   <th>成本价(元)</th>
                   <th>库存</th>
                   <th>重量(kg)</th>
                   <th>体积(m³)</th>
                   <th>商家编码</th>
                   <th>商品条码</th>
               </tr>
               <tr v-for="(list, index) in skuList">
                   <td v-for="(item, key) in list.child">
                       {{item.title}}
                   </td>
                   <td>
                       <img :src="list.picture.length > 0 ? list.picture : selectImageSrc" class="selectImage openUploadIframe" @click="selectImageSku(index)" href="<?= BaseUrl::to(['/files/selector', 'box_id' => 'TinyShopSku', 'upload_type' => 'images'])?>">
                   </td>
                   <td style="width:8%"><input type="text" name="price" v-model="list.price" class="form-control js-price" maxlength="10" value="0"></td>
                   <td style="width:8%"><input type="text" name="market_price" v-model="list.market_price" maxlength="10"  class="form-control" value="0"></td>
                   <td style="width:8%"><input type="text" name="cost_price" v-model="list.cost_price" maxlength="10" class="form-control" value="0"></td>
                   <td style="width:8%"><input type="text" name="stock" v-model="list.stock" maxlength="10" class="form-control" value="0"></td>
                   <td style="width:8%"><input type="text" name="weight" v-model="list.weight" maxlength="10" class="form-control" value="0"></td>
                   <td style="width:8%"><input type="text" name="volume" v-model="list.volume" maxlength="10" class="form-control" value="0"></td>
                   <td style="width:8%"><input type="text" name="sku_no" v-model="list.sku_no" maxlength="10" class="form-control" value="0"></td>
                   <td style="width:8%"><input type="text" name="barcode" v-model="list.barcode" maxlength="10" class="form-control" value="0"></td>
               </tr>
           </table>
        </div>
    </div>

    <div class="modal fade" id="specValue" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">规格值</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <input type="text" class="form-control" v-model="tmpSpecVal" ref="specValueInput" placeholder="请填写规格值" />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
                    <button class="btn btn-primary"  @click="createSpecValue()" data-dismiss="modal">确定</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="specCreate" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">规格</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <input type="text" class="form-control m-b" v-model="tmpSpecTitle" ref="specInput" placeholder="请填写规格" />
                    <?= Html::radioList('type', SpecTypeEnum::TEXT, SpecTypeEnum::getMap(), [
                        'id' => 'specType',
                        '@change' => "changeSpecType()",
                    ])?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
                    <button class="btn btn-primary" @click="createSpec()" data-dismiss="modal">确定</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var spec = new Vue({
        el: '#spec',
        data: {
            isSpecVal: '<?= $model->is_spec; ?>', // 是否启用多规格
            specTemplateId: '<?= $model->spec_template_id; ?>', // 选择的规格ID
            specList: JSON.parse('<?= Json::encode($spec)?>'), // 规格
            specListIndex: 0, // 当前操作的规格索引
            specListValueIndex: 0, // 当前操作的规格值索引
            tmpSpecType: 1, // 规格值类型
            tmpSpecTitle: '',
            tmpSpecVal: '',
            batchVal: '', // 批量设置值
            batchType: '', // 批量设置类型
            batchTypeList: {
                1: '销售价 ',
                2: '划线价 ',
                3: '成本价 ',
                4: '库存 ',
                5: '重量 ',
                6: '体积 ',
                7: '商家编码 ',
            },
            batchTypeField: {
                1: 'price',
                2: 'market_price',
                3: 'cost_price',
                4: 'stock',
                5: 'weight',
                6: 'volume',
                7: 'sku_no',
            },
            selectImageSrc: '<?= AddonHelper::file('img/sku-add.png'); ?>',
            skuAllData: [], // sku基础数据
            skuOldData: JSON.parse('<?= Json::encode($sku)?>'), // 之前存储的数据
            skuHeaders: [], // sku 头
            skuList: [], // sku 内容
            skuListIndex: 0, // sku 索引
            skuColspan: 10, // 合并单元格长度
        },
        methods: {
            // 添加规格
            addSku: function (id, title, parentId, parentTitle, specListIndex, specListValueIndex, isCreate = true) {
                if (isCreate) {
                    var pitch_on = this.specList[specListIndex].value[specListValueIndex].pitch_on;
                    if (parseInt(pitch_on) === 1) {
                        this.specList[specListIndex].value[specListValueIndex].pitch_on = 0;
                        this.specList[specListIndex].pitch_on_count -= 1; // 选中总数量
                        this.delSku(id, title, parentId, parentTitle);
                        return;
                    }

                    this.specList[specListIndex].value[specListValueIndex].pitch_on = 1;
                    this.specList[specListIndex].pitch_on_count += 1; // 选中总数量
                }

                var set = false;
                // 判断是否已经存在父类
                for (let i = 0; i < this.skuAllData.length; i++) {
                    // 存在
                    if (parseInt(this.skuAllData[i]['id']) === parseInt(parentId)) {
                        set = true;
                        // 写入子集
                        this.skuAllData[i]['child'].push({
                            'id': id,
                            'title': title,
                        })
                    }
                }

                // 设置父类
                if (set === false) {
                    this.skuAllData.push({
                        'id': parentId,
                        'title': parentTitle,
                        'child': []
                    });

                    var len = this.skuAllData.length - 1;
                    // 写入子集
                    this.skuAllData[len]['child'].push({
                        'id': id,
                        'title': title,
                    })
                }

                // 渲染规格
                this.createTable(isCreate);
            },
            delSku: function (id, title, parentId, parentTitle) {
                // 查找父级
                for (let i = 0; i < this.skuAllData.length; i++) {
                    if (parseInt(this.skuAllData[i]['id']) === parseInt(parentId)) {
                        // 查找子级
                        for (let j = 0; j < this.skuAllData[i]['child'].length; j++) {
                            if (parseInt(this.skuAllData[i]['child'][j]['id']) === parseInt(id)) {
                                this.skuAllData[i]['child'].splice(j, 1);
                            }
                        }

                        // 判断是否所有子级为空则全删除
                        if (this.skuAllData[i]['child'].length === 0) {
                            this.skuAllData.splice(i, 1);
                        }
                    }
                }

                // 渲染规格
                this.createTable();
            },
            createTable: function (isCreate = true) {
                if (isCreate === true) {
                    this.skuOldData = [];
                    this.storageAllSku();
                }

                this.skuHeaders = [];
                this.skuList = [];
                this.createTableHeader();
                this.createTableBody();
                this.setTableBody();
                this.createTableFoot();
            },
            // 存储原数据
            storageAllSku: function () {
                for (let i = 0; i < this.skuList.length; i++) {
                    sku = this.skuList[i]['data'];
                    this.skuOldData[sku] = this.skuList[i];
                }
            },
            // 创建表格头
            createTableHeader: function () {
                for (let i = 0; i < this.skuAllData.length; i++) {
                    this.skuHeaders.push(this.skuAllData[i]['title']);
                }
            },
            // 创建表格内容
            createTableBody: function () {
                var allNum = 1;
                var skuAllDataCount = this.skuAllData.length;
                if (skuAllDataCount === 0) {
                    return false;
                }

                for (let i = 0; i < skuAllDataCount; i++) {
                    allNum *= this.skuAllData[i]['child'].length
                }

                // 总sku
                for (let i = 0; i < allNum; i++) {
                    this.skuList.push({
                        'data': '',
                        'child': []
                    })
                }

                // 重新排序sku
                var allLen = 1;
                for (let i = 0; i < this.skuAllData.length; i++) {
                    var nowLen = 0;
                    var child = this.skuAllData[i]['child'];
                    // 每个循环次数
                    var childCirculationNum = (allNum / allLen) / child.length;

                    for (let j = 0; j < allLen; j++) {
                        // 子级每次循环
                        for (let k = 0; k < child.length; k++) {
                            for (let z = 0; z < childCirculationNum; z++) {
                                // 设置sku
                                let str = this.skuList[nowLen]['data'].length > 0 ? '-' : '';
                                this.skuList[nowLen]['data'] = this.skuList[nowLen]['data'] + str + child[k]['id'];
                                // 初始化数据
                                this.skuList[nowLen]['status'] = 1;
                                this.skuList[nowLen]['price'] = 0;
                                this.skuList[nowLen]['market_price'] = 0;
                                this.skuList[nowLen]['cost_price'] = 0;
                                this.skuList[nowLen]['stock'] = 0;
                                this.skuList[nowLen]['weight'] = 0;
                                this.skuList[nowLen]['volume'] = 0;
                                this.skuList[nowLen]['picture'] = '';
                                // 设置属性名称
                                this.skuList[nowLen]['child'].push(child[k]);

                                nowLen++;
                            }
                        }
                    }

                    allLen *= child.length;
                }
            },
            // 循环写入数据
            setTableBody: function () {
                for (let i = 0; i < this.skuList.length; i++) {
                    sku = this.skuList[i]['data'];
                    // 循环写入
                    if (this.skuOldData.hasOwnProperty(sku)) {
                        this.skuList[i] = this.skuOldData[sku];
                    }
                }
            },
            createTableFoot: function () {
                this.skuColspan = this.skuHeaders.length + 10;
            },
            selectColor: function (specListIndex, specListValueIndex) {
                this.specListIndex = specListIndex;
                this.specListValueIndex = specListValueIndex;
            },
            selectImage: function (specListIndex, specListValueIndex) {
                this.specListIndex = specListIndex;
                this.specListValueIndex = specListValueIndex;
            },
            selectImageSku: function (index) {
                this.skuListIndex = index;
            },
            setImageSku: function (url) {
                this.skuListIndex = index;
            },
            addSpec: function () {
                this.specListIndex = 0;
                this.specListValueIndex = 0;
                this.tmpSpecType = 1;
                this.tmpSpecTitle = '';
                this.getFocusBySpecInput();
            },
            delSpec: function (specListIndex) {
                this.specList.splice(specListIndex, 1);
            },
            setSpecType: function (specListIndex, type) {
                this.specList[specListIndex].type = type;
            },
            addSpecValue: function (specListIndex) {
                this.specListIndex = specListIndex;
                this.specListValueIndex = 0;
                this.tmpSpecVal = '';
                this.getFocusBySpecValueInput();
            },
            delSpecValue: function (specListIndex, specListValueIndex) {
                this.specList[specListIndex].value.splice(specListValueIndex, 1);
            },
            changeSpecType: function () {
                this.tmpSpecType = $("input[name='type']:checked").val();
            },
            // 正式创建规格
            createSpec: function () {
                if (this.tmpSpecTitle.length === 0) {
                    return;
                }

                $.ajax({
                    type: "get",
                    url: "<?= Url::to(['/common/spec/create'])?>",
                    dataType: "json",
                    data: {title: this.tmpSpecTitle, type: this.tmpSpecType},
                    success: function (data) {
                        if (parseInt(data.code) !== 200) {
                            rfWarning(data.message);
                        } else {
                            var tmpSpec = data.data;
                            spec.specList.push({
                                'id': tmpSpec.id,
                                'title': tmpSpec.title,
                                'type': tmpSpec.type,
                                'merchant_id': tmpSpec.merchant_id,
                                'sort': tmpSpec.sort,
                                'is_tmp': tmpSpec.is_tmp,
                                'pitch_on_count': 0,
                                'value': [],
                            })
                        }
                    }
                });
            },
            // 正式创建规格值
            createSpecValue: function () {
                if (this.tmpSpecVal.length === 0) {
                    return;
                }

                $.ajax({
                    type: "get",
                    url: "<?= Url::to(['/common/spec-value/create'])?>",
                    dataType: "json",
                    data: {spec_id: this.specList[this.specListIndex].id, title: this.tmpSpecVal},
                    success: function (data) {
                        if (parseInt(data.code) !== 200) {
                            rfWarning(data.message);
                        } else {
                            var tmpSpecValue = data.data;
                            spec.specList[spec.specListIndex].value.push({
                                'id': tmpSpecValue.id,
                                'title': tmpSpecValue.title,
                                'spec_id': tmpSpecValue.spec_id,
                                'merchant_id': tmpSpecValue.merchant_id,
                                'data': tmpSpecValue.data,
                                'sort': tmpSpecValue.sort,
                            })
                        }
                    }
                });
            },
            batch: function (type) {
                $('.js-batch-form').removeClass('hide');
                $('.js-batch-type').addClass('hide');
                $('.js-batch-txt').attr('placeholder', '请输入' + this.batchTypeList[type]);
                $('.js-batch-txt').focus();
                this.batchType = type;
                this.batchVal = '';
            },
            batchSave: function () {
                let batchTxt = $('.js-batch-txt');
                this.batchVal = parseFloat(this.batchVal);
                this.batchType = parseInt(this.batchType);
                if (
                    this.batchType === 1 ||
                    this.batchType === 2 ||
                    this.batchType === 3 ||
                    this.batchType === 5 ||
                    this.batchType === 6
                ) {
                    if (this.batchVal > 9999999.99) {
                        rfWarning('最大为 9999999.99');
                        this.batchVal = 0;
                        batchTxt.focus();
                        return false;
                    } else if (!/^\d+(\.\d+)?$/.test(this.batchVal)) {
                        rfWarning('请输入合法的数字');
                        this.batchVal = 0;
                        batchTxt.focus();
                        return false;
                    } else {
                        this.batchVal = this.batchVal.toFixed(2);
                    }
                }

                if (
                    this.batchType === 4 ||
                    this.batchType === 7 ||
                    this.batchType === 8
                ) {
                    this.batchVal = parseInt(this.batchVal);
                    if (!/^\d+$/.test(this.batchVal)) {
                        rfWarning('请输入合法的数字');
                        this.batchVal = 0;
                        batchTxt.focus();
                        return false;
                    }
                }

                var field = this.batchTypeField[this.batchType];
                for (let i = 0; i < this.skuList.length; i++) {
                    this.skuList[i][field] = this.batchVal;
                }

                // 关闭
                this.batchCancel();
            },
            batchCancel: function () {
                $('.js-batch-txt').val('');
                $('.js-batch-form').addClass('hide');
                $('.js-batch-type').removeClass('hide');
            },
            // 获得焦点
            getFocusBySpecInput() {
                this.$refs.specInput.focus();
            },
            getFocusBySpecValueInput() {
                this.$refs.specValueInput.focus();
            },
            isSpec: function (index) {
                this.isSpecVal = $("input[name='ProductForm[is_spec]']:checked").val();
                // 多规格
                if (this.isSpecVal > 0) {
                    $("#productform-stock").val(0).attr("readonly","readonly");
                } else {
                    $("#productform-stock").val(0).removeAttr("readonly");
                }
            },
            initParams: function () {
                $.ajax({
                    type: "get",
                    url: "<?= Url::to(['/common/spec-template/details'])?>",
                    dataType: "json",
                    data: {id: this.specTemplateId},
                    success: function (data) {
                        if (parseInt(data.code) !== 200) {
                            rfWarning(data.message);
                        } else {
                            // 清空数组
                            spec.specList.splice(0, spec.specList.length);
                            spec.skuList.splice(0, spec.skuList.length);
                            spec.skuAllData.splice(0, spec.skuAllData.length);
                            spec.skuHeaders.splice(0, spec.skuHeaders.length);
                            spec.skuList.splice(0, spec.skuList.length);
                            spec.skuListIndex = 0;

                            for (let i = 0; i < data.data.length; i++) {
                                spec.specList.push(data.data[i]);
                            }
                        }
                    }
                });
            },
        },
        // 初始化
        mounted() {
            var pitchOn = JSON.parse('<?= Json::encode($pitchOn)?>');
            for (let i = 0; i < pitchOn.length; i++) {
                this.addSku(pitchOn[i].id, pitchOn[i].title, pitchOn[i].parentId, pitchOn[i].parentTitle, 0, 0, false);
            }
        },
    });

    // 选择图片回调
    $(document).on('select-file-TinyShop', function (e, boxId, data) {
        if (data.length > 0) {
            spec.specList[spec.specListIndex].value[spec.specListValueIndex].data = data[0].url;
        }
    });

    // 选择规格图片回调
    $(document).on('select-file-TinyShopSku', function (e, boxId, data) {
        if (data.length > 0) {
            spec.batchVal = 0;
            spec.skuList[spec.skuListIndex].picture = data[0].url;
            spec.batchVal = '';
        }
    });

    // 图片预览放大
    $(function() {
        //定义X初始坐标量
        var x = 10;
        //定义Y初始坐标量
        var y = 20;
        var isTooltip = false;
        var defaultImg = "<?= AddonHelper::file('img/sku-add.png'); ?>";
        $(document).on("mousemove",".selectImage",function(e){
            var img = $(this).attr('src');
            if (img !== defaultImg && img.length  > 0 && isTooltip === false) {
                var src = $(this).attr('src');
                var realHeight = 50;
                var realWidth = 50;
                getImageWidth(src, function(width, height){
                    if (width > 200) {
                        realHeight = realWidth = parseInt((200 / width) * 100);

                        console.log(realHeight);
                    }
                });

                //声明层对象
                var tooltip = "<div id='tooltip' style='position:absolute;'><img src='" + src + "' alt='预览' width='"+realWidth+"%' height='"+realHeight+"%'/></div>";
                //将层追加到文档中
                $("body").append(tooltip);
                //设置层样式
                $("#tooltip").css({
                    "top": (e.pageY + y) + "px",
                    "left": (e.pageX + x) + "px"
                });

                isTooltip = true;
            }
        }).mouseout(function() {
            //移除层
            $("#tooltip").remove();
            isTooltip = false;
        }).mousemove(function(e) {

        });

        // 获取图片真实高度
        function getImageWidth(url, callback){
            var img = new Image();
            img.src = url;
            // 如果图片被缓存，则直接返回缓存数据
            if(img.complete){
                callback(img.width, img.height);
            }else{
                img.onload = function(){
                    callback(img.width, img.height);
                }
            }
        }
    });
</script>
