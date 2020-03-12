<?php

use yii\widgets\ActiveForm;
use common\enums\StatusEnum;
use common\helpers\Url;
use yii\web\JsExpression;

$this->title = $model->isNewRecord ? '创建' : '编辑';
$this->params['breadcrumbs'][] = ['label' => '物流配送'];
$this->params['breadcrumbs'][] = ['label' => '门店审核人员', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">基本信息</h3>
            </div>
            <?php $form = ActiveForm::begin([
                'fieldConfig' => [
                    'template' => "<div class='col-sm-2 text-right'>{label}</div><div class='col-sm-10'>{input}\n{hint}\n{error}</div>",
                ],
            ]); ?>
            <div class="box-body">
                <div class="col-lg-12">
                    <?= $form->field($model, 'pickup_point_id')->dropDownList(Yii::$app->tinyShopService->pickupPoint->getMap()); ?>
                    <?= $form->field($model, 'member_id')->widget(\kartik\select2\Select2::class, [
                        'initValueText' => $model->member->mobile ?? '', // set the initial display text
                        'options' => ['placeholder' => '手机号码查询'],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 3,
                            'language' => [
                                'errorLoading' => new JsExpression("function () { return '等待结果...'; }"),
                            ],
                            'ajax' => [
                                'url' => Url::to(['/member/select2']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(params) { 
                                        return {q:params.term}; 
                                }'),
                            ],
                            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                            'templateResult' => new JsExpression('function(city) { return city.text; }'),
                            'templateSelection' => new JsExpression('function (city) { return city.text; }'),
                        ],
                    ]); ?>
                    <?= $form->field($model, 'status')->radioList(StatusEnum::getMap()); ?>
                </div>
            </div>
            <div class="box-footer text-center">
                <button class="btn btn-primary" type="submit">保存</button>
                <span class="btn btn-white" onclick="history.go(-1)">返回</span>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>