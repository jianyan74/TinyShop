<?php

use yii\widgets\ActiveForm;
use common\helpers\Url;
use common\enums\StatusEnum;
use common\helpers\Html;
use addons\TinyShop\common\models\order\Order;
use addons\TinyShop\common\enums\RefundStatusEnum;
use addons\TinyShop\common\enums\ProductExpressShippingTypeEnum;

/** @var Order $order */

$form = ActiveForm::begin([
    'id' => $model->formName(),
    'enableAjaxValidation' => true,
    'validationUrl' => Url::to(['create', 'id' => $order['id']]),
]);

?>

<div class="modal-header">
    <h4 class="modal-title">发货</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
</div>
<div class="modal-body">
    <table class="table table-bordered table-hover">
        <thead>
        <tr>
            <th><input type="checkbox" class="check-all"></th>
            <th>商品</th>
            <th>数量</th>
            <th>物流 | 单号</th>
            <th>状态</th>
        </tr>
        </thead>
        <tbody id='list'>
        <?php foreach ($product as $item) { ?>
            <?php if (in_array($item['refund_status'], RefundStatusEnum::deliver())) { ?>
                <tr id= <?= $item['id']; ?>>
                    <td>
                        <?= Html::checkbox(Html::getInputName($model, 'order_product_ids') . '[]', false, [
                            'value' => $item['id'],
                            'disabled' => $item['shipping_status'] == StatusEnum::ENABLED
                        ]) ?>
                    </td>
                    <td><?= $item['product_name']; ?></td>
                    <td><?= $item['num']; ?></td>
                    <td><?= $item['express']; ?></td>
                    <td><?= $item['shipping_status'] == StatusEnum::ENABLED ? '已发货' : '待发货'; ?></td>
                </tr>
            <?php } ?>
        <?php } ?>
        </tbody>
    </table>
    <?= $form->field($model, 'shipping_type')->radioList(ProductExpressShippingTypeEnum::getMap()); ?>
    <div class="company">
        <?= $form->field($model, 'express_company_id')->dropDownList($company)->hint(empty($order->company_name) ? '' : "提醒：订单中用户选择了 $order->company_name"); ?>
        <?= $form->field($model, 'express_no')->textInput(); ?>
    </div>
    <div class="form-group">
        <label class="control-label">收货信息：</label>
        <?= $order->receiver_realname; ?>，<?= $order->receiver_mobile; ?>
        ，<?= $order->receiver_name; ?> <?= Html::encode($order->receiver_details); ?>
        <div class="help-block"></div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
    <button class="btn btn-primary" type="submit">保存</button>
</div>
<?php ActiveForm::end(); ?>

<script>
    // 多选框选择
    $(".check-all").click(function () {
        if (this.checked) {
            $("#list :checkbox").prop("checked", true);
        } else {
            $("#list :checkbox").prop("checked", false);
        }
    });

    $("input[name='ProductExpressForm[shipping_type]']").click(function () {
        var val = $(this).val();
        if (parseInt(val) === 0) {
            $('.company').addClass('hide');
        } else {
            $('.company').removeClass('hide');
        }
    })
</script>
