<?php

use yii\widgets\ActiveForm;
use common\helpers\Url;
use common\helpers\Html;
use addons\TinyShop\common\enums\ProductExpressShippingTypeEnum;

$form = ActiveForm::begin([
    'id' => $model->formName(),
    'enableAjaxValidation' => true,
    'validationUrl' => Url::to(['update','id' => $model['id']]),
]);

?>

<div class="modal-header">
    <h4 class="modal-title">修改运单号</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
</div>
<div class="modal-body">
    <?= $form->field($model, 'shipping_type')->radioList(ProductExpressShippingTypeEnum::getMap()); ?>
    <div class="company <?= $model->shipping_type == 0 ? 'hide' : ''; ?>">
        <?= $form->field($model, 'express_company_id')->dropDownList($company); ?>
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
    $("input[name='ProductExpress[shipping_type]']").click(function(){
        var val = $(this).val();
        if (parseInt(val) === 0) {
            $('.company').addClass('hide');
        } else {
            $('.company').removeClass('hide');
        }
    })
</script>
