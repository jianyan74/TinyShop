<?php

use yii\widgets\ActiveForm;
use common\helpers\Url;
use common\enums\StatusEnum;
use common\widgets\webuploader\Files;

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
    <?= $form->field($model, 'name')->textInput(); ?>
    <?= $form->field($model, 'cover')->widget(Files::class, [
        'config' => [
            'pick' => [
                'multiple' => false,
            ]
        ]
    ]); ?>
    <?= $form->field($model, 'explain')->textarea(); ?>
    <?= $form->field($model, 'sort')->textInput(); ?>
    <?= $form->field($model, 'status')->radioList(StatusEnum::getMap()); ?>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
    <button class="btn btn-primary" type="submit">保存</button>
</div>
<?php ActiveForm::end(); ?>
