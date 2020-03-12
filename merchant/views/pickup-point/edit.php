<?php

use yii\widgets\ActiveForm;
use common\enums\StatusEnum;

$this->title = $model->isNewRecord ? '创建' : '编辑';
$this->params['breadcrumbs'][] = ['label' => '物流配送'];
$this->params['breadcrumbs'][] = ['label' => '门店自提', 'url' => ['index']];
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
                    <?= $form->field($model, 'name')->textInput(); ?>
                    <?= $form->field($model, 'sort')->textInput(); ?>
                    <?= \common\widgets\provinces\Provinces::widget([
                        'form' => $form,
                        'model' => $model,
                        'provincesName' => 'province_id',// 省字段名
                        'cityName' => 'city_id',// 市字段名
                        'areaName' => 'area_id',// 区字段名
                    ]); ?>
                    <?= $form->field($model, 'address')->textarea(); ?>
                    <?= $form->field($model, 'longitude_latitude')->widget(\common\widgets\selectmap\Map::class, [
                        'type' => 'amap', // amap高德;tencent:腾讯;baidu:百度
                    ]); ?>
                    <?= $form->field($model, 'contact')->textInput(); ?>
                    <?= $form->field($model, 'mobile')->textInput(); ?>
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