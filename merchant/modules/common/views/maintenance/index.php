<?php

use yii\widgets\ActiveForm;
use common\enums\WhetherEnum;

$this->title = '站点维护';
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
                    <?= $form->field($model, 'site_status')->radioList(WhetherEnum::getOpenMap()); ?>
                    <?= $form->field($model, 'site_close_explain')->textarea(); ?>
                    <div class="col-sm-12 text-center">
                        <button class="btn btn-primary m-t-lg" type="submit">保存</button>
                    </div>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
