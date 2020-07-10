<?php

use yii\widgets\ActiveForm;
use common\helpers\Url;
use yii\web\JsExpression;

$form = ActiveForm::begin([
    'id' => $model->formName(),
    'enableAjaxValidation' => false,
    'validationUrl' => Url::to(['give', 'coupon_type_id' => $model->coupon_type_id]),
]);

?>
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">关闭</span></button>
        <h4 class="modal-title">基本信息</h4>
    </div>
    <div class="modal-body">
        <?= $form->field($model, 'title')->textInput([
            'readonly' => true
        ]); ?>
        <?= $form->field($model, 'num')->textInput(); ?>
        <?= $form->field($model, 'member_id')->widget(\kartik\select2\Select2::class, [
            'initValueText' => [], // set the initial display text
            'options' => ['placeholder' => '用户手机号码查询'],
            'pluginOptions' => [
                'allowClear' => true,
                'minimumInputLength' => 3,
                'language' => [
                    'errorLoading' => new JsExpression("function () { return '等待结果中...'; }"),
                ],
                'ajax' => [
                    'url' => Url::to(['/member/select2']),
                    'dataType' => 'json',
                    'data' => new JsExpression('function(params) { 
                                        return {q:params.term}; 
                                }')
                ],
                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                'templateResult' => new JsExpression('function(city) { return city.text; }'),
                'templateSelection' => new JsExpression('function (city) { return city.text; }'),
            ],
        ]); ?>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
        <button class="btn btn-primary" type="submit">保存</button>
    </div>
<?= \common\helpers\Html::modelBaseCss(); ?>
<?php ActiveForm::end(); ?>