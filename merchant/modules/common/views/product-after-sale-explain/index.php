<?php

use yii\widgets\ActiveForm;

$this->title = '售后保障';
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
                <div class="col-12 col-xs-12">
                    <?= $form->field($model, 'product_after_sale_explain')->widget(\common\widgets\ueditor\UEditor::class, [])->hint('售后保障会在商品详情页面，售后保障切换卡下方展示，内容不超过1500个字符'); ?>
                    <div class="col-sm-12 text-center">
                        <button class="btn btn-primary m-t-lg" type="submit">保存</button>
                    </div>
                </div>
                <div class="col-md-7 col-xs-12 img text-center">

                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
