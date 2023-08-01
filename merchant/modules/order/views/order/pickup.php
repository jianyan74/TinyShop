<?php

use yii\widgets\ActiveForm;
use common\helpers\Url;

$form = ActiveForm::begin([
    'id' => $model->formName(),
    'enableAjaxValidation' => true,
    'validationUrl' => Url::to(['pickup','id' => $model['order_id']]),
    'fieldConfig' => [
        'template' => "<div class='row'><div class='col-sm-3 text-right'>{label}</div><div class='col-sm-9'>{input}\n{hint}\n{error}</div></div>",
    ]
]);
?>

<div class="modal-header">
    <h4 class="modal-title">基本信息</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
</div>
<div class="modal-body">
    <?= $form->field($model, 'buyer_name')->textInput(); ?>
    <?= $form->field($model, 'buyer_mobile')->textInput(); ?>
    <?= $form->field($model, 'remark')->textarea(); ?>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
    <button class="btn btn-primary" type="submit">确认提货</button>
</div>

<?php ActiveForm::end(); ?>
