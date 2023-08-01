<?php

use yii\widgets\ActiveForm;
use addons\TinyShop\common\enums\product\PosteCoverTypeEnum;
use addons\TinyShop\common\enums\product\PosteQrTypeEnum;

$this->title = '商品海报';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= $this->title; ?></h3>
            </div>
            <?php $form = ActiveForm::begin([
                'fieldConfig' => [
                    'template' => "<div class='row'><div class='col-sm-2 text-right'>{label}</div><div class='col-sm-10'>{input}\n{hint}\n{error}</div></div>",
                ]
            ]); ?>
            <div class="box-body">
                <div class="col-lg-12">
                    <?= $form->field($model, 'product_poster_title')->textInput(); ?>
                    <?= $form->field($model, 'product_poster_cover_type')->radioList(PosteCoverTypeEnum::getMap()); ?>
                    <?= $form->field($model, 'product_poster_qr_type')->radioList(PosteQrTypeEnum::getMap()); ?>
                </div>
            </div>
            <div class="box-footer text-center">
                <button class="btn btn-primary" type="submit">保存</button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
