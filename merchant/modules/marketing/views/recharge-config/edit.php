<?php

use yii\widgets\ActiveForm;
use common\enums\StatusEnum;
use common\helpers\StringHelper;
use kartik\datetime\DateTimePicker;

$this->title = $model->isNewRecord ? '创建' : '编辑';
$this->params['breadcrumbs'][] = ['label' => '新人礼(注册奖励)', 'url' => ['index']];
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
                    'template' => "<div class='row'><div class='col-sm-1 text-right'>{label}</div><div class='col-sm-11'>{input}\n{hint}\n{error}</div></div>",
                ],
            ]); ?>
            <div class="box-body">
                <div class="col-lg-12">
                    <?= $form->field($model, 'price')->textInput(); ?>
                    <?= $form->field($model, 'give_price')->textInput(); ?>
                    <div class="row">
                        <div class="col-sm-1"></div>
                        <div class="col-sm-5">
                            <?= $form->field($model, 'give_point', ['template' => "{label}{input}\n{hint}\n{error}"])->textInput(); ?>
                        </div>
                        <div class="col-sm-5">
                            <?= $form->field($model, 'give_growth', ['template' => "{label}{input}\n{hint}\n{error}"])->textInput(); ?>
                        </div>
                    </div>

                    <?= $form->field($marketingCouponType, 'couponTypes')->widget(\addons\TinyShop\common\widgets\coupon\CouponSelect::class, [
                        'min' => 1,
                        'columns' => [
                            [
                                'label' => '数量',
                                'name' => 'number',
                                'value' => 1,
                                'rule' => [
                                    'min' => 1,
                                    'max' => 5,
                                ],
                            ],
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
