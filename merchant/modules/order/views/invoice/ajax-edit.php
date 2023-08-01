<?php

use yii\widgets\ActiveForm;
use common\helpers\Url;
use common\enums\InvoiceTypeEnum;
use addons\TinyShop\common\enums\OrderInvoiceAuditStatusEnum;

$form = ActiveForm::begin([
    'id' => $model->formName(),
    'enableAjaxValidation' => true,
    'validationUrl' => Url::to(['ajax-edit', 'id' => $model['id']]),
    'fieldConfig' => [
        'template' => "<div class='row'><div class='col-sm-3 text-right'>{label}</div><div class='col-sm-9'>{input}\n{hint}\n{error}</div></div>",
    ],
]);

?>

<div class="modal-header">
    <h4 class="modal-title">基本信息</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
</div>
<div class="modal-body">
    <?= $form->field($model, 'type')->radioList(InvoiceTypeEnum::getMap()); ?>
    <?= $form->field($model, 'title')->textInput()->label('抬头'); ?>
    <?= $form->field($model, 'duty_paragraph')->textInput(); ?>
    <?= $form->field($model, 'opening_bank')->textInput(); ?>
    <?= $form->field($model, 'opening_bank_account')->textInput(); ?>
    <?= $form->field($model, 'address')->textInput(); ?>
    <?= $form->field($model, 'phone')->textInput(); ?>
    <?= $form->field($model, 'explain')->textarea(); ?>
    <?= $form->field($model, 'audit_status')->radioList(OrderInvoiceAuditStatusEnum::getMap()); ?>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
    <button class="btn btn-primary" type="submit">保存</button>
</div>
<?php ActiveForm::end(); ?>
