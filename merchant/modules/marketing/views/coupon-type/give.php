<?php

use yii\widgets\ActiveForm;
use common\helpers\Url;
use common\helpers\Html;
use yii\web\JsExpression;

$form = ActiveForm::begin([
    'id' => $model->formName(),
    'enableAjaxValidation' => false,
    'validationUrl' => Url::to(['give', 'coupon_type_id' => $model->coupon_type_id]),
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
                    'url' => Url::to(['/member/mobile-select']),
                    'dataType' => 'json',
                    'data' => new JsExpression('function(params) { 
                                        return {q:params.term}; 
                                }')
                ],
                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                'templateResult' => new JsExpression('function(city) { return city.text; }'),
                'templateSelection' => new JsExpression('function (city) { return city.text; }'),
            ],
        ])->hint('赠送优惠券会增加已创建优惠券的总数'); ?>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
        <button class="btn btn-primary" type="submit">保存</button>
    </div>
    <?= Html::modelBaseCss(); ?>
<?php ActiveForm::end(); ?>
