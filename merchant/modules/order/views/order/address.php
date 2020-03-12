<?php
use yii\widgets\ActiveForm;
use common\helpers\Url;
use common\enums\StatusEnum;

$form = ActiveForm::begin([
    'id' => $model->formName(),
    'enableAjaxValidation' => true,
    'validationUrl' => Url::to(['address','id' => $model['id']]),
]);
?>

    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">关闭</span></button>
        <h4 class="modal-title">基本信息</h4>
    </div>
    <div class="modal-body">
        <?= $form->field($model, 'receiver_name')->textInput(); ?>
        <?= $form->field($model, 'receiver_mobile')->textInput(); ?>
        <?= $form->field($model, 'receiver_zip')->textInput(); ?>
        <?= \common\widgets\provinces\Provinces::widget([
            'form' => $form,
            'model' => $model,
            'provincesName' => 'receiver_province',// 省字段名
            'cityName' => 'receiver_city',// 市字段名
            'areaName' => 'receiver_area',// 区字段名
            'template' => 'short' //合并为一行显示
        ]); ?>
        <?= $form->field($model, 'receiver_address')->textarea(); ?>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
        <button class="btn btn-primary" type="submit">保存</button>
    </div>
<?php ActiveForm::end(); ?>