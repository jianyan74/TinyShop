<?php

use yii\widgets\ActiveForm;
use common\enums\StatusEnum;
use common\enums\WhetherEnum;
use common\widgets\webuploader\Files;

$this->title = $model->isNewRecord ? '创建' : '编辑';
$this->params['breadcrumbs'][] = ['label' => '物流公司', 'url' => ['index']];
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
                    <?= $form->field($model, 'title')->textInput(); ?>
                    <?= $form->field($model, 'express_no')->textInput(); ?>
                    <?= $form->field($model, 'sort')->textInput(); ?>
                    <?= $form->field($model, 'cover')->widget(Files::class, [
                        'config' => [
                            'pick' => [
                                'multiple' => false,
                            ],
                        ],
                    ]); ?>
                    <?= $form->field($model, 'mobile')->textInput(); ?>
                    <?= $form->field($model, 'is_default')->radioList(WhetherEnum::getMap()); ?>
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