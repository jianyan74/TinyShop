<?php

use yii\widgets\ActiveForm;
use common\enums\StatusEnum;

$this->title = '编辑';
$this->params['breadcrumbs'][] = ['label' => '站点帮助', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title];
?>

<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">基本信息</h3>
            </div>
            <?php $form = ActiveForm::begin([
                'fieldConfig' => [
                    'template' => "<div class='row'><div class='col-sm-2 text-right'>{label}</div><div class='col-sm-10'>{input}\n{hint}\n{error}</div></div>",
                ]
            ]); ?>
            <div class="box-body">
                <?= $form->field($model, 'pid')->dropDownList($dropDownList, [
                        'disabled' => true
                ]) ?>
                <?= $form->field($model, 'title')->textInput(); ?>
                <?= $form->field($model, 'sort')->textInput(); ?>
                <?php if ($model->pid > 0) { ?>
                    <?= $form->field($model, 'content')->widget(\common\widgets\ueditor\UEditor::class) ?>
                <?php } ?>
                <?= $form->field($model, 'status')->radioList(StatusEnum::getMap()); ?>
            </div>
            <!-- /.box-body -->
            <div class="box-footer">
                <div class="col-sm-12 text-center">
                    <button class="btn btn-primary" type="submit">保存</button>
                    <span class="btn btn-white" onclick="history.go(-1)">返回</span>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
