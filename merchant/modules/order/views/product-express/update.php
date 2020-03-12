<?php
use yii\widgets\ActiveForm;
use common\helpers\Url;

$form = ActiveForm::begin([
    'id' => $model->formName(),
    'enableAjaxValidation' => true,
    'validationUrl' => Url::to(['update','id' => $model['id']]),
]);
?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">关闭</span></button>
    <h4 class="modal-title">基本信息</h4>
</div>
<div class="modal-body">
    <?= $form->field($model, 'shipping_type')->radioList($shippingTypeExplain); ?>
    <div class="company">
        <?= $form->field($model, 'express_company_id')->dropDownList($company); ?>
        <?= $form->field($model, 'express_no')->textInput(); ?>
    </div>
    <div class="form-group">
        <label class="control-label">收货信息：</label>
        <?= $order->receiver_region_name ?> <?= $order->receiver_address ?>
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
        if (val == 0) {
            $('.company').addClass('hide');
        } else {
            $('.company').removeClass('hide');
        }
    })
</script>
