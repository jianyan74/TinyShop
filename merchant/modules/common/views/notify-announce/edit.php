<?php

use yii\widgets\ActiveForm;
use common\widgets\webuploader\Files;

$this->title = '编辑';
$this->params['breadcrumbs'][] = ['label' => '公告管理', 'url' => ['index']];
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
                    'template' => "<div class='row'><div class='col-sm-1 text-right'>{label}</div><div class='col-sm-11'>{input}{hint}{error}</div></div>",
                ]
            ]); ?>
            <div class="box-body">
                <?= $form->field($model, 'title')->textInput() ?>
                <?= $form->field($model, 'cover')->widget(Files::class, [
                    'config' => [
                        'pick' => [
                            'multiple' => false,
                        ]
                    ]
                ]); ?>
                <?= $form->field($model, 'synopsis')->textarea() ?>
                <?= $form->field($model, 'content')->widget(\common\widgets\ueditor\UEditor::class) ?>
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
