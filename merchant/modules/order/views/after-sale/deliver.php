<?php

use yii\widgets\ActiveForm;
use common\helpers\Url;
use common\helpers\Html;
use addons\TinyShop\common\models\order\Order;
use addons\TinyShop\common\enums\ProductExpressShippingTypeEnum;

/** @var Order $order */

$form = ActiveForm::begin([
    'id' => $model->formName(),
    'enableAjaxValidation' => true,
    'validationUrl' => Url::to(['deliver', 'id' => $model['id']]),
]);

?>

<div class="modal-header">
    <h4 class="modal-title">用户换货 - 发货</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
</div>
<div class="modal-body">
    <?= $form->field($model, 'merchant_shipping_type')->radioList(ProductExpressShippingTypeEnum::getMap()); ?>
    <div class="company">
        <?= $form->field($model, 'merchant_express_company_id')->dropDownList($company)->hint(empty($order->company_name) ? '' : "提醒：订单中用户选择了 $order->company_name"); ?>
        <?= $form->field($model, 'merchant_express_no')->textInput(); ?>
        <?= $form->field($model, 'merchant_express_mobile')->textInput()->hint('顺丰快递必填'); ?>
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
    $("input[name='AfterSale[merchant_shipping_type]']").click(function () {
        var val = $(this).val();
        if (parseInt(val) === 0) {
            $('.company').addClass('hide');
        } else {
            $('.company').removeClass('hide');
        }
    })
</script>
