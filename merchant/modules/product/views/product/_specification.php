<?php

use common\enums\WhetherEnum;
use common\helpers\AddonHelper;
use common\helpers\Url;
use common\helpers\Html;
use yii\helpers\BaseUrl;

?>

<div class="row">
    <div class="col-sm-6"><?= $form->field($model, 'marque')->textInput(); ?></div>
    <div class="col-sm-6"><?= $form->field($model, 'barcode')->textInput(); ?></div>
</div>
<div class="row">
    <div class="col-sm-6"><?= $form->field($model, 'stock')->textInput()->hint('商品的剩余数量, 如启用多规格，则此处设置无效.'); ?></div>
    <div class="col-sm-6"><?= $form->field($model, 'warning_stock')->textInput(); ?></div>
</div>
<?= $form->field($model, 'is_stock_visible')->radioList(WhetherEnum::getMap()) ?>
<?= $form->field($model, 'is_attribute')->radioList([0 => '统一规格', 1 => '多规格'])->hint('启用商品规格后，商品的价格及库存以商品规格为准,库存设置为0则会到”已售罄“中，不会显示'); ?>
<div class="row base-attribute <?php if ($model->is_attribute == 1){ ?>hide<?php } ?>">
    <div class="col-sm-4"><?= $form->field($model, 'price')->textInput(); ?></div>
    <div class="col-sm-4"><?= $form->field($model, 'market_price')->textInput(); ?></div>
    <div class="col-sm-4"><?= $form->field($model, 'cost_price')->textInput(); ?></div>
</div>
<div class="attribute <?php if ($model->is_attribute == 0){ ?>hide<?php } ?>">
    <?= $form->field($model, 'base_attribute_id')->dropDownList(\common\helpers\ArrayHelper::merge(['0' => '请选择'], $baseAttribute)); ?>
    <dl class="control-group js-goods-attribute-block <?php if ($model->is_attribute == 0){ ?>hide<?php } ?>">
        <dt>商品属性</dt>
        <dd>
            <div class="controls">
                <table class="table goods-sku-attribute js-goods-sku-attribute">
                    <?php foreach ($attributeValue as $value){ ?>
                        <tr>
                            <td><?= $value['title']; ?></td>
                            <td>
                                <?php if($value['type'] == 1){ ?>
                                    <?= Html::textInput("attributeValue[" . $value['id'] . "]", $value['value'], [
                                        'class' => 'form-control'
                                    ])?>
                                <?php }elseif($value['type'] == 2){ ?>
                                    <?= Html::radioList("attributeValue[" . $value['id'] . "]", $value['value'], $value['config'])?>
                                <?php }elseif($value['type'] == 3){ ?>
                                    <?= Html::checkboxList("attributeValue[" . $value['id'] . "]", explode(',', $value['value']), $value['config'])?>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        </dd>
    </dl>
    <dl class="control-group <?php if ($model->is_attribute == 0){ ?>hide<?php } ?>">
        <dt>商品规格</dt>
        <dd>
            <table class="table goods-sku js-goods-sku">
                <tbody>
                <?php foreach ($specValue as $spec){ ?>
                    <tr>
                        <td><?= $spec['title']; ?></td>
                        <td id="spec-<?= $spec['id']; ?>">
                            <?php foreach ($spec['value'] as $value){ ?>
                                <span id="option-<?= $value['id']; ?>" data-type="<?= $spec['show_type']; ?>" class="btn btn-white btn-sm" data-id="<?= $value['id']; ?>" data-title="<?= $value['title']; ?>" data-pid="<?= $spec['id']; ?>" data-ptitle="<?= $spec['title']; ?>" data-sort="<?= $value['sort']; ?>"><?= $value['title']; ?></span>
                                <?php if($spec['show_type'] == 2){ ?>
                                    <span class="btn btn-sm selectColor" style="background:<?= !empty($value['data']) ? '#' . $value['data'] : '#000000'; ?>;padding: 10px" data-href="<?= Url::to(['select-color', 'value' => $value['data']])?>"></span>
                                    <?= Html::hiddenInput('specValueFieldData[' . $value['id'] .']', '#' . $value['data'])?>
                                <?php }elseif($spec['show_type'] == 3){ ?>
                                    <img src="<?= !empty($value['data']) ? $value['data'] : AddonHelper::file('img/sku-add.png'); ?>" class="selectImage" href="<?= BaseUrl::to(['/file/selector', 'boxId' => 'tinyshop', 'upload_type' => 'images'])?>" data-toggle='modal' data-target='#ajaxModalMax'>
                                    <?= Html::hiddenInput('specValueFieldData[' . $value['id'] .']', $value['data'])?>
                                <?php } ?>
                            <?php } ?>
                            <a href="#" class="specValue blue" data-show_type="<?= $spec['show_type']; ?>" data-pid="<?= $spec['id']; ?>" data-ptitle="<?= $spec['title']; ?>" data-toggle="modal" data-target="#specValue">+ 规格值</a>
                        </td>
                    </tr>
                <?php } ?>
                <tr>
                    <td colspan="2" class="spec blue pointer" data-toggle="modal" data-target="#specCreate">+ 规格</td>
                </tr>
                </tbody>
            </table>
            <div class="hint-block">点击按钮进行规格值设置, 选择按钮的情况下颜色/图片选项规格值才会被保存</div>
        </dd>
    </dl>
    <dl class="js-spec-table hide">
        <dt class="m-b-sm">商品库存</dt>
        <dd>
            <div class="controls">
                <div class="js-goods-stock control-group">
                    <div id="stock-region" class="sku-group">
                        <table class="table table-bordered table-sku-stock table-hover">
                            <thead></thead>
                            <tbody></tbody>
                            <tfoot></tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </dd>
    </dl>
</div>

<!-- 属性模板 -->
<script id="attributeValue" type="text/html">
    <tbody>
    {{each attributeValue as value i}}
    <tr>
        <td>{{value.title}}</td>
        <td>
            {{if value.type == 1}}
                <input type="text" value="{{value.value}}" class="form-control" name="attributeValue[{{value.id}}]">
            {{else if value.type == 2}}
                <div role="radiogroup">
                    {{each value.config as item i}}
                    <label><input type="radio" name="attributeValue[{{value.id}}]" value="{{item}}" {{if i == 0}}checked="checked"{{/if}}> {{item}}</label>
                    {{/each}}
                </div>
            {{else}}
                {{each value.config as item i}}
                    <label><input type="checkbox" name="attributeValue[{{value.id}}][]" value="{{item}}"> {{item}}</label>
                {{/each}}
            {{/if}}
        </td>
    </tr>
    {{/each}}
    </tbody>
</script>

<!-- 规格模板 -->
<script id="spec" type="text/html">
    <tbody>
    {{each spec as val i}}
    <tr>
        <td>{{val.title}}</td>
        <td>
            {{each val.value as item i}}
                <span id="option-{{item.id}}" data-type="{{val.show_type}}" class="btn btn-white btn-sm" data-id="{{item.id}}" data-title="{{item.title}}" data-pid="{{val.id}}" data-ptitle="{{val.title}}" data-sort="{{item.sort}}">{{item.title}}</span>
            {{if val.show_type == 2}}
                <span class="btn btn-sm selectColor" style="background:#000000;padding: 10px" data-href="<?= Url::to(['select-color'])?>"></span>
                <?= Html::hiddenInput('specValueFieldData[{{item.id}}]', '')?>
            {{else if val.show_type == 3}}
                <img src="<?= AddonHelper::file('img/sku-add.png'); ?>" class="selectImage" href="<?= BaseUrl::to(['/file/selector', 'boxId' => 'tinyshop', 'upload_type' => 'images'])?>" data-toggle='modal' data-target='#ajaxModalMax'>
                <?= Html::hiddenInput('specValueFieldData[{{item.id}}]', '')?>
            {{/if}}
            {{/each}}
            <a href="#" class="specValue blue" data-show_type="{{val.show_type}}" data-pid="{{val.id}}" data-ptitle="{{val.title}}" data-toggle="modal" data-target="#specValue">+ 规格值</a>
        </td>
    </tr>
    {{/each}}
    <tr>
        <td colspan="2" class="spec blue pointer" data-toggle="modal" data-target="#specCreate">+ 规格</td>
    </tr>
    </tbody>
</script>

<!-- 表格头 -->
<script id="header" type="text/html">
    <tr>
        {{each data as value i}}
        <th>{{value}}</th>
        {{/each}}
        <th class="th-picture">sku图片</th>
        <th class="th-price">销售价（元）</th>
        <th class="th-price">市场价（元）</th>
        <th class="th-price">成本价（元）</th>
        <th class="th-stock">库存</th>
        <th class="th-code">商家编码</th>
        <th class="th-status">启用状态</th>
    </tr>
</script>

<!-- 表格内容 -->
<script id="body" type="text/html">
    {{each data as value i}}
    <tr id="{{value.sku}}">
        {{each value.child as item j}}
        <td data-id="{{item.id}}">{{item.title}}</td>
        {{/each}}
        <td>
            <img src="" class="selectImage" href="<?= BaseUrl::to(['/file/selector', 'boxId' => 'tinyshop', 'upload_type' => 'images'])?>" data-toggle="modal" data-target="#ajaxModalMax">
            <input type="hidden" name="skus[{{value.sku}}][picture]" class="js-picture">
        </td>
        <td><input type="text" name="skus[{{value.sku}}][price]" class="js-price form-control" maxlength="10" value="0"></td>
        <td><input type="text" name="skus[{{value.sku}}][market_price]" maxlength="10"  class="js-market-price form-control" value="0"></td>
        <td><input type="text" name="skus[{{value.sku}}][cost_price]" maxlength="10" class="js-cost-price form-control" value="0"></td>
        <td><input type="text" name="skus[{{value.sku}}][stock]" maxlength="10" class="js-stock-num form-control" value="0"></td>
        <td><input type="text" name="skus[{{value.sku}}][code]" maxlength="10" class="js-code form-control" value="0"></td>
        <td>
            <select class="js-status form-control" name="skus[{{value.sku}}][status]" aria-invalid="false">
                <option value="0">禁用</option>
                <option value="1" selected="selected">启用</option>
            </select>
        </td>
    </tr>
    {{/each}}
</script>

<!-- 表格底部 -->
<script id="foot" type="text/html">
    <tr>
        <td>
            批量设置：
        </td>
        <td colspan="{{colspan}}" style="text-align:left;">
            <div class="batch-opts">
                <span class="js-batch-type">
                    <a class="js-batch-price blue" href="javascript:void (0);" onclick="batch(1)">销售价</a>
                    <a class="js-batch-market_price blue" href="javascript:void (0);" onclick="batch(2)">市场价</a>
                    <a class="js-batch-cost_price blue" href="javascript:void (0);" onclick="batch(3)">成本价</a>
                    <a class="js-batch-stock blue" href="javascript:void (0);" onclick="batch(4)">库存</a>
                    <a class="js-batch-merchant-code blue" href="javascript:void (0);" onclick="batch(5)">商家编码</a>
                </span>
                <span class="js-batch-form input-group hide">
                    <input type="text" maxlength="11" class="js-batch-txt form-control input-sm" style="width:130px;">
                    <a class="js-batch-save btn btn-primary btn-sm m-l-xs" href="javascript:void (0);">保存</a>
                    <a class="js-batch-cancel btn btn-white btn-sm" href="javascript:void (0);">取消</a>
                </span>
            </div>
        </td>
    </tr>
</script>

<div class="modal fade" id="specValue" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close"><span aria-hidden="true">×</span><span class="sr-only">关闭</span></button>
                <h4 class="modal-title">规格值</h4>
            </div>
            <div class="modal-body">
                <input type="text" class="form-control" id="specVal" placeholder="请填写规格值" />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
                <button class="btn btn-primary submit-spec-val" data-dismiss="modal">确定</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="specCreate" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close"><span aria-hidden="true">×</span><span class="sr-only">关闭</span></button>
                <h4 class="modal-title">规格</h4>
            </div>
            <div class="modal-body">
                <input type="text" class="form-control m-b" id="specTitle" placeholder="请填写规格" />
                <?= Html::radioList('show_type', 1, \addons\TinyShop\common\models\base\Spec::$showTypeExplain, [
                        'class' => 'specShowType'
                ])?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
                <button class="btn btn-primary submit-spec" data-dismiss="modal">确定</button>
            </div>
        </div>
    </div>
</div>

<script>
    var thatSpec;
    var thatSpecValue;

    $(document).on("click", ".specValue",function(){
        thatSpecValue = $(this);
    })

    $(document).on("click", ".submit-spec-val",function(){
        var spec_id = thatSpecValue.data('pid');
        var spec_title = thatSpecValue.data('ptitle');
        var show_type = thatSpecValue.data('show_type');
        var value = $('#specVal').val();

        if (value) {
            $.ajax({
                type: "post",
                url: '<?= Url::to(['/base/spec-value/create'])?>',
                dataType: "json",
                data: {spec_id: spec_id, value: value},
                success: function (data) {
                    if (parseInt(data.code) === 200) {
                        var html = '<span id="option-' + data.data.id + '" data-type="' + show_type + '" class="btn btn-white btn-sm" data-id="' + data.data.id + '" data-title="' + data.data.title + '" data-pid="' + data.data.spec_id + '" data-ptitle="' + spec_title + '" data-sort="' + data.data.sort + '">' + data.data.title + '</span> ';
                        switch (parseInt(show_type)) {
                            case 2 :
                                html += '<span class="btn btn-sm selectColor" style="background:#000000;padding: 10px" data-href="<?= Url::to(["select-color"])?>"></span> ';
                                html += '<input type="hidden" name="specValueFieldData[' + data.data.id + ']" value="#">';
                                break;
                            case 3 :
                                html += '<img src="<?= AddonHelper::file("img/sku-add.png"); ?>" class="selectImage" href="<?= BaseUrl::to(["/file/selector", "boxId" => "tinyshop", "upload_type" => "images"])?>" data-toggle="modal" data-target="#ajaxModalMax"> '
                                html += '<input type="hidden" name="specValueFieldData[' + data.data.id + ']" value="#">';
                                break;
                        }

                        $('#specVal').val('');
                        $(thatSpecValue).before(html)
                    } else {
                        rfWarning(data.message);
                    }
                }
            });
        }
    })

    $(document).on("click", ".spec",function(){
        thatSpec = $(this);
    })

    $(document).on("click", ".submit-spec",function(){
        var title = $('#specTitle').val();
        var show_type = $("input[name='show_type']:checked").val();
        var base_attribute_id = $("#productform-base_attribute_id").val();

        if (title) {
            $.ajax({
                type: "post",
                url: '<?= Url::to(['/base/spec/create'])?>',
                dataType: "json",
                data: {title: title, show_type: show_type, base_attribute_id: base_attribute_id},
                success: function (data) {
                    if (parseInt(data.code) === 200) {
                        var html = "<tr><td>" + data.data.title + "</td><td>";
                        html += '<a href="#" class="specValue blue" data-show_type="' + show_type + '" data-pid="' + data.data.id + '" data-ptitle="' + data.data.title + '" data-toggle="modal" data-target="#specValue">+ 规格值</a>';
                        html += "</td>";

                        $('#specTitle').val('');
                        $(thatSpec).parent().before(html)
                    } else {
                        rfWarning(data.message);
                    }
                }
            });
        }
    })
</script>