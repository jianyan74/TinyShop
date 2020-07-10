<?php

use yii\widgets\ActiveForm;
use common\widgets\webuploader\Files;

$this->title = '版权信息';
$this->params['breadcrumbs'][] = ['label' => $this->title];
?>

<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"><?= $this->title ?></h3>
            </div>
            <?php $form = ActiveForm::begin(); ?>
            <div class="box-body">
                <div class="col-md-12 col-xs-12">
                    <?= $form->field($model, 'copyright_logo')->widget(Files::class, [
                        'type' => 'images',
                        'theme' => 'default',
                        'themeConfig' => [],
                        'config' => [
                            'pick' => [
                                'multiple' => false,
                            ],
                        ],
                    ]); ?>
                    <?= $form->field($model, 'copyright_companyname')->textInput(); ?>
                    <?= $form->field($model, 'copyright_url')->textInput(); ?>
                    <?= $form->field($model, 'copyright_desc')->textarea(); ?>
                    <div class="col-sm-12 text-center">
                        <button class="btn btn-primary m-t-lg" type="submit">保存</button>
                    </div>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>