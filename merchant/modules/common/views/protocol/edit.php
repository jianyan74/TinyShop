<?php

use yii\widgets\ActiveForm;

$addon = <<< HTML
<div class="input-group-append">
    <span class="input-group-text">
        <i class="fas fa-calendar-alt"></i>
    </span>
</div>
HTML;

$this->title = $model->isNewRecord ? '创建' : '编辑';
$this->params['breadcrumbs'][] = ['label' => '弹出广告', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-12 col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">基本信息</h3>
            </div>
            <?php $form = ActiveForm::begin([
                'fieldConfig' => [
                    'template' => "<div class='row'><div class='col-lg-1 col-sm-12 text-right'>{label}</div><div class='col-lg-11'>{input}{hint}{error}</div></div>",
                ]
            ]); ?>
            <div class="box-body">
                <div class="col-lg-12">
                    <?= $form->field($model, 'title')->textInput([
                            'readonly' => true
                    ]); ?>
                    <div class="hide">
                        <?= $form->field($model, 'name')->hiddenInput()->label(false); ?>
                    </div>
                    <?= $form->field($model, 'version')->textInput()->hint('格式要求(必须)：xxxx.xxxx.xxxx, 这个 x 必须为数字前面 0 可忽略，例如：1.0.1') ?>
                    <?= $form->field($model, 'content')->widget(\common\widgets\ueditor\UEditor::class) ?>
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
