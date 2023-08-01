<?php

use yii\widgets\ActiveForm;
use kartik\select2\Select2;

$this->title = '热门搜索';
$this->params['breadcrumbs'][] = ['label' => $this->title];

?>



<div class="row">
    <div class="col-12 col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= $this->title; ?></h3>
            </div>
            <?php $form = ActiveForm::begin([]); ?>
            <div class="box-body">
                <?= $form->field($model, 'hot_search_default')->textInput(); ?>
                <?= $form->field($model, 'hot_search_list')->widget(Select2::class, [
                    'options' => [
                        'placeholder' => '请填写',
                        'multiple' => true,
                    ],
                    'pluginOptions' => [
                        'tags' => true,
                        'tokenSeparators' => [',', ' '],
                        'maximumInputLength' => 20,
                    ],
                ]); ?>

            </div>
            <div class="box-footer">
                <div class="col-sm-12 text-center">
                    <button class="btn btn-primary" type="submit">保存</button>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
