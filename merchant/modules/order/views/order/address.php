<?php

use yii\widgets\ActiveForm;
use common\helpers\Url;

$form = ActiveForm::begin([
    'id' => $model->formName(),
    'enableAjaxValidation' => true,
    'validationUrl' => Url::to(['address','id' => $model['id']]),
]);

?>

    <div class="modal-header">
        <h4 class="modal-title">修改地址</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
    </div>
    <div class="modal-body">
        <?= $form->field($model, 'receiver_realname')->textInput(); ?>
        <?= $form->field($model, 'receiver_mobile')->textInput(); ?>
        <?= \common\widgets\linkage\Linkage::widget([
            'form' => $form,
            'model' => $model,
            'template' => 'short',
            'one' => [
                'name' => 'receiver_province_id', // 字段名称
                'title' => '请选择省', // 字段名称
                'list' => Yii::$app->services->provinces->getCityMapByPid(), // 字段名称
            ],
            'two' => [
                'name' => 'receiver_city_id', // 字段名称
                'title' => '请选择市', // 字段名称
                'list' => Yii::$app->services->provinces->getCityMapByPid($model->receiver_province_id, 2), // 字段名称
            ],
            'three' => [
                'name' => 'receiver_area_id', // 字段名称
                'title' => '请选择区', // 字段名称
                'list' => Yii::$app->services->provinces->getCityMapByPid($model->receiver_city_id, 3), // 字段名称
            ],
        ]); ?>
        <?= $form->field($model, 'receiver_details')->textarea(); ?>
        <?= $form->field($model, 'receiver_zip')->textInput(); ?>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
        <button class="btn btn-primary" type="submit">保存</button>
    </div>
<?php ActiveForm::end(); ?>
