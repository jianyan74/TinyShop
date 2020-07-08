<?php

use yii\widgets\ActiveForm;
use common\helpers\Url;
use common\widgets\webuploader\Files;
use kartik\select2\Select2;
use common\helpers\AddonHelper;
use common\enums\StatusEnum;

$this->title = $model->isNewRecord ? '创建' : '编辑';
$this->params['breadcrumbs'][] = ['label' => '商品管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-md-12">
        <!-- Custom Tabs -->
        <?php $form = ActiveForm::begin([
            'id' => 'productForm',
        ]); ?>
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab_1" data-toggle="tab">基本</a></li>
                <li><a href="#tab_2" data-toggle="tab">库存规格</a></li>
                <li><a href="#tab_3" data-toggle="tab">封面详情</a></li>
                <li><a href="#tab_5" data-toggle="tab">积分设置</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active p-xs" id="tab_1">
                    <?= $form->field($model, 'name')->textInput(); ?>
                    <div class="row">
                        <div class="col-sm-4">
                            <?php  if ($setting->product_cate_type == 1) { ?>
                                <?= $form->field($model, 'cate_id')->widget(\common\widgets\selectlinkage\Linkage::class, [
                                    'url' => Url::to(['cate/select']),
                                    'item' => $cate,
                                    'allItem' => $cates,
                                ]); ?>
                            <?php } else { ?>
                                <?= $form->field($model, 'cate_id')->widget(Select2::class, [
                                    'data' => \common\helpers\ArrayHelper::map($cates, 'id', 'title'),
                                    'options' => [
                                        'placeholder' => '请选择分类',
                                        'multiple' => true
                                    ],
                                    'maintainOrder' => true,
                                    'pluginOptions' => [
                                        'tags' => true,
                                        'tokenSeparators' => [',', ' '],
                                        'maximumInputLength' => 20
                                    ],
                                ])->hint('输入后请回车'); ?>
                            <?php } ?>
                        </div>
                        <div class="col-sm-4"><?= $form->field($model, 'brand_id')->dropDownList($brands, ['prompt' => '请选择']) ?></div>
                        <div class="col-sm-4"><?= $form->field($model, 'supplier_id')->dropDownList($supplier, ['prompt' => '请选择']) ?></div>
                    </div>
                    <?= $form->field($model, 'sketch')->textInput(); ?>
                    <?= $form->field($model, 'keywords')->textInput()->hint('商品关键字,能准确搜到商品的,比如 : 海尔电视,电视 之类的.用于 SEO 搜索'); ?>
                    <?= $form->field($model, 'tags')->widget(Select2::class, [
                        'data' => $tags,
                        'options' => [
                            'placeholder' => '请选择标签',
                            'multiple' => true
                        ],
                        'pluginOptions' => [
                            'tags' => true,
                            'tokenSeparators' => [',', ' '],
                            'maximumInputLength' => 20
                        ],
                    ])->hint('输入后请回车'); ?>
                    <?= $form->field($model, 'unit')->textInput(); ?>
                    <?= \common\widgets\provinces\Provinces::widget([
                        'form' => $form,
                        'model' => $model,
                        'provincesName' => 'province_id',// 省字段名
                        'cityName' => 'city_id',// 市字段名
                        'areaName' => 'area_id',// 区字段名
                        'template' => 'short', //合并为一行显示
                        'level' => 3,
                    ]); ?>
                    <?= $form->field($model, 'shipping_type')->radioList(['1' => '免邮','2' => '买家承担运费']); ?>
                    <div class="shipping <?php if ($model->shipping_type == 1){ echo 'hide'; } ?>">
                        <div class="row">
                            <div class="col-sm-3"><?= $form->field($model, 'shipping_fee_id')->dropDownList(\common\helpers\ArrayHelper::merge(['0' => '请选择'], $companys)) ?></div>
                            <div class="col-sm-3"><?= $form->field($model, 'product_weight')->textInput()->hint('公斤'); ?></div>
                            <div class="col-sm-3"><?= $form->field($model, 'product_volume')->textInput()->hint('立方米'); ?></div>
                            <div class="col-sm-3"><?= $form->field($model, 'shipping_fee_type')->radioList(['1' => '计件','2' => '体积','3' => '重量']); ?></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6"><?= $form->field($model, 'max_buy')->textInput()->hint('<span class="orange">输入0表示不限购</span>'); ?></div>
                        <div class="col-sm-6"><?= $form->field($model, 'min_buy')->textInput(); ?></div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4"><?= $form->field($model, 'sales')->textInput(); ?></div>
                        <div class="col-sm-4"><?= $form->field($model, 'view')->textInput(); ?></div>
                        <div class="col-sm-4"><?= $form->field($model, 'transmit_num')->textInput(); ?></div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'production_date')->widget(kartik\date\DatePicker::class, [
                                'language' => 'zh-CN',
                                'layout'=>'{picker}{input}',
                                'pluginOptions' => [
                                    'format' => 'yyyy-mm-dd',
                                    'todayHighlight' => true, // 今日高亮
                                    'autoclose' => true, // 选择后自动关闭
                                    'todayBtn' => true, // 今日按钮显示
                                ],
                                'options'=>[
                                    'class' => 'form-control no_bor',
                                    'value' => $model->isNewRecord ? date('Y-m-d') : date('Y-m-d',$model->production_date),
                                ]
                            ]);?>
                        </div>
                        <div class="col-sm-6"><?= $form->field($model, 'shelf_life')->textInput(); ?></div>
                    </div>
                    <?= $form->field($model, 'is_hot')->checkbox(); ?>
                    <?= $form->field($model, 'is_recommend')->checkbox(); ?>
                    <?= $form->field($model, 'is_new')->checkbox(); ?>
                    <?= $form->field($model, 'product_status')->radioList($productStatusExplain); ?>
                </div>
                <!-- /.tab-pane -->
                <div class="tab-pane p-xs" id="tab_2">
                    <?= $this->render('_specification', [
                        'model' => $model,
                        'form' => $form,
                        'specValue' => $specValue,
                        'attributeValue' => $attributeValue,
                        'baseAttribute' => $baseAttribute,
                    ]) ?>

                </div>
                <!-- /.tab-pane -->
                <div class="tab-pane p-xs" id="tab_3">
                    <?= $form->field($model, 'covers')->widget(Files::class, [
                        'config' => [
                            // 可设置自己的上传地址, 不设置则默认地址
                            // 'server' => '',
                            'pick' => [
                                'multiple' => true,
                            ],
                        ]
                    ])->hint('第一张图片将作为商品主图,支持同时上传多张图片,多张图片之间可拖动调整位置'); ?>
                    <?= $form->field($model, 'video_url')->widget(Files::class, [
                        'type' => 'videos',
                        'config' => [
                            // 可设置自己的上传地址, 不设置则默认地址
                            // 'server' => '',
                            'pick' => [
                                'multiple' => false,
                            ],
                            'accept' => [
                                'extensions' => ['rm', 'rmvb', 'wmv', 'avi', 'mpg', 'mpeg', 'mp4'],
                                'mimeTypes' => 'video/*',
                            ],
                        ]
                    ]); ?>
                    <?= $form->field($model, 'intro')->widget(\common\widgets\ueditor\UEditor::class) ?>
                </div>
                <!-- /.tab-pane -->
                <div class="tab-pane p-xs" id="tab_5">
                    <?= $form->field($model, 'point_exchange_type')->radioList(\addons\TinyShop\common\enums\PointExchangeTypeEnum::getMap()); ?>
                    <div class="shipping-point-for-now <?php if ($model->point_exchange_type != 1){ echo 'hide'; } ?>">
                        <?= $form->field($model, 'max_use_point')->textInput()->hint('设置购买时积分抵现最大可使用积分数，0为不可使用 '); ?>
                    </div>
                    <div class="shipping-point <?php if ($model->point_exchange_type == 1){ echo 'hide'; } ?>">
                        <?= $form->field($model, 'point_exchange')->textInput(); ?>
                    </div>
                    <?= $form->field($model, 'integral_give_type')->radioList([0 => '赠送固定积分', 1 => '按照当前价格百分比赠送积分']); ?>
                    <?= $form->field($model, 'give_point')->textInput()->hint('最低为0，如果是百分比赠送积分上限为100 '); ?>
                </div>
                <div class="box-footer text-center">
                    <?= $form->field($model, 'id')->hiddenInput()->label(false); ?>
                    <div class="hide" id="specValue"></div>
                    <button class="btn btn-primary" type="button" onclick="beforSubmit()">保存</button>
                    <span class="btn btn-white" onclick="rfTwiceAffirmBack(this, '确定返回吗？', '未保存的内容可能丢失');return false;">返回</span>
                </div>
            </div>
            <!-- /.tab-content -->
        </div>
        <?php ActiveForm::end(); ?>
        <!-- nav-tabs-custom -->
    </div>
</div>

<script>
    // 默认sku
    var defaultSku = JSON.parse('<?= json_encode($skus); ?>');
    // 默认规格属性
    var defaultSpecValue = JSON.parse('<?= json_encode($specValuejsData); ?>');
    let batchType = 1;
    // 所有选中数据
    var allData = [];
    // 所有重组sku数据
    var allSku = [];
    // sku值存储的数据
    var skusDataArr = [];
    var defaultAddImg = "<?= AddonHelper::file('img/sku-add.png') ?>";
    var referrer = "<?= $referrer ?>";

    // 图片预览放大
    $(function() {
        //定义X初始坐标量
        var x = 10;
        //定义Y初始坐标量
        var y = 20;
        var isTooltip = false;
        $(document).on("mousemove",".selectImage",function(e){
            if ($(this).next().val().length  > 0 && isTooltip === false) {
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
    // 初始化渲染
    $(document).ready(function () {
        var is_attribute = $("input[name='ProductForm[is_attribute]']:checked").val();
        if (is_attribute > 0) {
            // 禁用输入
            if($("#productform-stock").attr("readonly") != "readonly"){
                $("#productform-stock").val(0).attr("readonly","readonly");
            }
            if($("#productform-price").attr("readonly") != "readonly"){
                $("#productform-price").val(0).attr("readonly","readonly");
            }
            if($("#productform-market_price").attr("readonly") != "readonly"){
                $("#productform-market_price").val(0).attr("readonly","readonly");
            }
            if($("#productform-cost_price").attr("readonly") != "readonly"){
                $("#productform-cost_price").val(0).attr("readonly","readonly");
            }

            // 写入sku
            for (let i = 0; i < defaultSku.length; i++) {
                let skuId = defaultSku[i]['data'];
                let data = [];
                data['sku'] = defaultSku[i]['data'];
                data['price'] = defaultSku[i]['price'];
                data['picture'] = defaultSku[i]['picture'];
                data['marketPrice'] = defaultSku[i]['market_price'];
                data['costPrice'] = defaultSku[i]['cost_price'];
                data['stock'] = defaultSku[i]['stock'];
                data['code'] = defaultSku[i]['code'];
                data['status'] = defaultSku[i]['status'];

                skusDataArr[skuId] = [];
                skusDataArr[skuId] = data;
            }

            // 写入规格属性
            for (let i = 0; i < defaultSpecValue.length; i++) {
                $("#option-" + defaultSpecValue[i]['id']).removeClass('btn-white');
                $("#option-" + defaultSpecValue[i]['id']).addClass('btn-primary');
                addAttributes (defaultSpecValue[i]['id'], defaultSpecValue[i]['title'], defaultSpecValue[i]['pid'], defaultSpecValue[i]['ptitle'], false)
            }

            if (allData.length > 0) {
                // 创建表头
                createTableHeader();
                // 创建内容
                createTableBody();
                // 创建表格底部
                createTableFoot();
                // 写入表格内的sku数据
                setSkusDataArr();
                $('.js-spec-table').removeClass('hide');
            }
        } else {
            $('#productform-base_attribute_id').val(0);
        }
    });

    // 防止重复提交
    var submitStatus = true;
    // 验证并提交表单
    function beforSubmit() {
        if (submitStatus === false) {
          // rfWarning('正在提交中...');
          // return;
        }

        // 启用
        if ($("input[name='ProductForm[is_attribute]']:checked").val() == '1' && allData.length == 0){
            rfWarning('请选择填写商品规格信息');
            return;
        }

        // 设置规格属性
        var html = '';

        for (let i = 0; i < allData.length; i++) {
            var spec_id = allData[i]['id'];

            var inputOptionStr = '';
            var child = allData[i]['child'];
            for (let j = 0; j < child.length; j++) {
                let str = inputOptionStr ? '-' : '';
                inputOptionStr += str + child[j]['id'];
            }

            html += '<input type="text" name="specValue[' + spec_id + ']" value="' + inputOptionStr + '">';
        }

        $('#specValue').html(html);

        // 序列化数据
        var data = $('#productForm').serializeArray();
        // console.log(data);

        submitStatus = false;
        $.ajax({
            type : "post",
            url : "<?= Url::to(['edit', 'id' => $model->id]); ?>",
            dataType : "json",
            data : data,
            success: function(data) {
                submitStatus = true;
                if (parseInt(data.code) === 200) {
                    var editId = '<?= $model->id?>';
                    if (editId) {
                        swal("操作成功", "小手一抖就打开了一个框", "success").then((value) => {
                            window.location = referrer;
                        });
                    } else {
                        swal('小手一抖打开一个窗', {
                            buttons: {
                                defeat: '继续创建商品',
                                catch: {
                                    text: "完成",
                                    value: "catch",
                                },
                            },
                            title: '操作成功',
                        }).then((value) => {
                            switch (value) {
                                case "defeat":
                                    location.reload();
                                    break;
                                case "catch":
                                    window.location = referrer;
                                    break;
                                default:
                            }
                        });
                    }
                } else {
                    rfWarning(data.message);
                }
            }
        });
    }

    // 包邮
    $("input[name='ProductForm[shipping_type]']").click(function () {
        var val = $(this).val();
        if (val == '2'){
            $('.shipping').removeClass('hide');
            // $('.shipping').find('.form-group').addClass('required');
            // $('.field-product-shipping_fee_type').removeClass('required');
        }else{
            $('.shipping').addClass('hide');
        }
    });

    // 积分
    $("input[name='ProductForm[point_exchange_type]']").click(function () {
        var val = $(this).val();
        if (val == '1'){
            $('.shipping-point-for-now').removeClass('hide');
            $('.shipping-point').addClass('hide');
        }else{
            $('.shipping-point').removeClass('hide');
            $('.shipping-point-for-now').addClass('hide');
        }
    });

    // 规格启用
    $("input[name='ProductForm[is_attribute]']").click(function () {
        var val = $(this).val();
        // 启用
        if (val == '1'){
            $('.attribute').removeClass('hide');
            $('.base-attribute').addClass('hide');
            if($("#productform-stock").attr("readonly") != "readonly"){
                $("#productform-stock").val(0).attr("readonly","readonly");
            }
            if($("#productform-price").attr("readonly") != "readonly"){
                $("#productform-price").val(0).attr("readonly","readonly");
            }
            if($("#productform-market_price").attr("readonly") != "readonly"){
                $("#productform-market_price").val(0).attr("readonly","readonly");
            }
            if($("#productform-cost_price").attr("readonly") != "readonly"){
                $("#productform-cost_price").val(0).attr("readonly","readonly");
            }

        }else{
            $('.base-attribute').removeClass('hide');
            $('.attribute').addClass('hide');

            $("#productform-stock").val(0).removeAttr("readonly");
            $("#productform-price").val(0).removeAttr("readonly");
            $("#productform-market_price").val(0).removeAttr("readonly");
            $("#productform-cost_price").val(0).removeAttr("readonly");

            $('.attribute').addClass('hide');
        }
    });

    // 选择商品模型
    $("select[name='ProductForm[base_attribute_id]']").change(function () {
        var base_attribute_id = $(this).val();

        // 所有选中数据
        allData = [];
        // 所有重组sku数据
        allSku = [];
        // sku值存储的数据
        skusDataArr = [];
        createTable();

        if (!base_attribute_id || parseInt(base_attribute_id) === 0) {
            $('.control-group').addClass('hide');
            return;
        }

        $.ajax({
            type : "get",
            url : "<?= Url::to(['base-spec-attribute']); ?>",
            dataType : "json",
            data : {base_attribute_id: base_attribute_id},
            success: function(data){
                if (parseInt(data.code) === 200) {
                    $('.control-group').removeClass('hide');
                    // 规格和规格值
                    var attributeHtml = template('spec', data.data);
                    $('.js-goods-sku').html(attributeHtml);
                    // 参数
                    var paramsHtml = template('attributeValue', data.data);
                    $('.js-goods-sku-attribute').html(paramsHtml);
                } else {
                    rfWarning(data.message);
                }
            }
        });
    });

    // 属性点击
    $(document).on("click",".js-goods-sku span",function(){
        var title = $(this).data('title');
        var id = $(this).data('id');
        var pid = $(this).data('pid');
        var ptitle = $(this).data('ptitle');
        if (parseInt($(this).data('type')) > 0) {
            if ($(this).hasClass('btn-white')) {
                $(this).removeClass('btn-white');
                $(this).addClass('btn-primary');

                // 加入规格总数组
                addAttributes (id, title, pid, ptitle);
            } else {
                $(this).removeClass('btn-primary');
                $(this).addClass('btn-white');

                // 删除规格总数组
                delAttributes (id, title, pid, ptitle);
            }
        }
    });

    // 批量设置
    function batch(type) {
        let batchText = [];
        batchText[1] = '销售价';
        batchText[2] = '市场价';
        batchText[3] = '成本价 ';
        batchText[4] = '库存 ';
        batchText[5] = '商家编码 ';

        $('.js-batch-form').removeClass('hide');
        $('.js-batch-type').addClass('hide');
        $('.js-batch-txt').attr('placeholder', '请输入' + batchText[type]);
        $('.js-batch-txt').focus();
        batchType = type;
    }

    // 报错批量设置
    $(document).on("click",".js-batch-save",function(){
        let batch_txt = $('.js-batch-txt');
        let val = parseFloat(batch_txt.val());
        if (batchType === 1 || batchType === 2 || batchType === 2) {
            if (val > 9999999.99) {
                rfWarning('价格最大为 9999999.99');
                batch_txt.focus();
                return false;
            } else if (!/^\d+(\.\d+)?$/.test(batch_txt.val())) {
                rfWarning('请输入合法的价格');
                batch_txt.focus();
                return false;
            } else {
                batch_txt.val(val.toFixed(2));
            }
        }

        if (batchType === 1) {
            $('.js-price').val(val)
        }

        if (batchType === 2) {
            $('.js-market-price').val(val)
        }

        if (batchType === 3) {
            $('.js-cost-price').val(val)
        }

        if (batchType === 4) {
            if (!/^\d+$/.test(batch_txt.val())) {
                rfWarning('请输入合法的数字');
                batch_txt.focus();
                return false;
            }

            $('.js-stock-num').val(val)
        }

        if (batchType === 5) {
            $('.js-code').val(val)
        }

        $('.js-batch-txt').val('');
        $('.js-batch-form').addClass('hide');
        $('.js-batch-type').removeClass('hide');
    });

    // 取消批量设置
    $(document).on("click",".js-batch-cancel",function(){
        $('.js-batch-txt').val('');
        $('.js-batch-form').addClass('hide');
        $('.js-batch-type').removeClass('hide');
    });

    // 增加规格属性
    function addAttributes (id, title, pid, ptitle, create = true) {

        var set = false;
        // 判断是否已经存在父类
        for (let i = 0; i < allData.length; i++) {
            if (parseInt(allData[i]['id']) === parseInt(pid)) {
                set = true;
            }
        }

        // 设置父类
        if (set === false) {
            var parent = [];
            parent['id'] = pid;
            parent['title'] = ptitle;
            parent['child'] = [];

            allData.push(parent);
        }

        // 写入子集
        for (let i = 0; i < allData.length; i++) {
            if (parseInt(allData[i]['id']) === parseInt(pid)) {
                var child = [];
                child['id'] = id;
                child['title'] = title;
                allData[i]['child'].push(child);
            }
        }

        if (create === true) {
            createTable();
        }
    }

    // 删除规格属性
    function delAttributes (id, title, pid, ptitle) {
        // 查找父级
        for (let i = 0; i < allData.length; i++) {
            if (parseInt(allData[i]['id']) === parseInt(pid)) {
                // 查找子级
                for (let j = 0; j < allData[i]['child'].length; j++) {
                    if (parseInt(allData[i]['child'][j]['id']) === parseInt(id)) {
                        allData[i]['child'].splice(j, 1);
                    }
                }

                // 判断是否所有子级为空则全删除
                if (allData[i]['child'].length === 0) {
                    allData.splice(i, 1);
                }
            }
        }

        console.log(allData)

        createTable();
    }

    // 创建表格
    function createTable() {
        skusDataArr = [];
        if (allData.length > 0) {
            // 获取表格内的sku数据
            getSkusDataArr();
            // 创建表头
            createTableHeader();
            // 创建内容
            createTableBody();
            // 创建表格底部
            createTableFoot();
            // 写入表格内的sku数据
            setSkusDataArr();
            $('.js-spec-table').removeClass('hide');
        } else {
            $('.js-spec-table').addClass('hide');
        }
    }

    // 创建表格头
    function createTableHeader() {
        let header = [];
        header["data"] = [];
        for (let i = 0; i < allData.length; i++) {
            header["data"][i] = allData[i]['title'];
        }

        let headerHtml = template('header', header);
        $(".js-spec-table table thead").html(headerHtml);
    }

    // 创建表格内容
    function createTableBody() {
        allSku = [];
        var allNum = 1;
        for (let i = 0; i < allData.length; i++) {
            allNum *= allData[i]['child'].length
        }

        // 总sku
        for (let i = 0; i < allNum; i++) {
            allSku[i] = [];
            allSku[i]['sku'] = '';
            allSku[i]['child'] = [];
        }

        // 重新排序sku
        var allLen = 1;
        for (let i = 0; i < allData.length; i++) {
            var nowLen = 0;
            var child = allData[i]['child'];
            // 每个循环次数
            var childCirculationNum = (allNum / allLen) / child.length;

            for (let j = 0; j < allLen; j++) {
                // 子级每次循环
                for (let k = 0; k < child.length; k++) {
                    for (let z = 0; z < childCirculationNum; z++) {
                        // 设置sku
                        let str = allSku[nowLen]['sku'].length > 0 ? '-' : '';
                        allSku[nowLen]['sku'] = allSku[nowLen]['sku'] + str + child[k]['id'];
                        // 设置属性名称
                        allSku[nowLen]['child'].push(child[k]);

                        nowLen++;
                    }
                }
            }

            allLen *= child.length;
        }

        // 渲染
        let body = [];
        body["data"] = allSku;
        $(".js-spec-table table tbody").html(template('body', body));
    }

    // 创建表格底部
    function createTableFoot() {
        let data = [];
        data['colspan']  = allData.length + 6;

        let html = template('foot', data);
        $(".js-spec-table table tfoot").html(html);
    }

    // 获取sku存储的数据
    function getSkusDataArr() {
        $(".js-spec-table table tbody tr").each(function () {
            let skuId = $(this).attr('id');
            let data = [];
            data['sku'] = skuId;
            data['picture'] = $(this).find('.js-picture').val();
            data['price'] = $(this).find('.js-price').val();
            data['marketPrice'] = $(this).find('.js-market-price').val();
            data['costPrice'] = $(this).find('.js-cost-price').val();
            data['stock'] = $(this).find('.js-stock-num').val();
            data['code'] = $(this).find('.js-code').val();
            data['status'] = $(this).find('.js-status').val();

            skusDataArr[skuId] = [];
            skusDataArr[skuId] = data;
        });

        return skusDataArr;
    }

    // 写入sku存储的数据
    function setSkusDataArr() {
        $(".js-spec-table table tbody tr").each(function () {
            let skuId = $(this).attr('id');
            if (skusDataArr.hasOwnProperty(skuId)) {
                $(this).find('.js-picture').val(skusDataArr[skuId]['picture']);
                if (skusDataArr[skuId]['picture'].length > 0) {
                    $(this).find('.selectImage').attr('src', skusDataArr[skuId]['picture']);
                } else {
                    $(this).find('.selectImage').attr('src', defaultAddImg);
                }

                $(this).find('.js-price').val(skusDataArr[skuId]['price']);
                $(this).find('.js-market-price').val(skusDataArr[skuId]['marketPrice']);
                $(this).find('.js-cost-price').val(skusDataArr[skuId]['costPrice']);
                $(this).find('.js-stock-num').val(skusDataArr[skuId]['stock']);
                $(this).find('.js-code').val(skusDataArr[skuId]['code']);
                $(this).find('.js-status').val(skusDataArr[skuId]['status']);
            } else {
                $(this).find('.selectImage').attr('src', defaultAddImg);
            }
        });
    }
</script>

<script>
    var colorUrl = "<?= Url::to(['select-color', 'value' => ''])?>";
    // 选择颜色
    $(document).on("click", ".selectColor",function(){
        colorObj = $(this);
        var thisColorUrl = $(this).data('href');

        openIframeSelectColor(thisColorUrl);
    });

    // 打一个新窗口
    function openIframeSelectColor(url, color) {
        layer.open({
            type: 2,
            title: '选择颜色',
            shade: 0.3,
            offset: "10%",
            shadeClose: true,
            btn: ['选择', '关闭'],
            yes: function (index, layero) {
                var body = layer.getChildFrame('body', index);

                let color = body.find('.spectrum-input').val();
                $(colorObj).attr('style', "background:" + color + ";padding: 10px");
                $(colorObj).next().val(color);
                color = color.substr(1);
                $(colorObj).data('href', colorUrl + color);

                layer.closeAll();
            },
            btn2: function () {
                layer.closeAll();
            },
            area: ['50%', '400px'],
            content: url
        });

        return false;
    }

    // 选择图片
    $(document).on("click", ".selectImage",function(){
        imageObj = $(this);
    });

    // 选择图片回调
    $(document).on('select-file-tinyshop', function(e, boxId, data){
        if (data.length > 0) {
            let url = data[0].url;
            $(imageObj).attr('src', url);
            $(imageObj).next().val(url);
        }
    });
</script>