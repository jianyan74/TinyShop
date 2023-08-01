<?php

use yii\widgets\ActiveForm;
use common\enums\StatusEnum;
use addons\TinyShop\common\enums\MerchantAddressTypeEnum;

$this->title = '退款地址';
$this->params['breadcrumbs'][] = ['label' => $this->title];

?>

<div class="row">
    <div class="col-sm-12">
        <div class="nav-tabs-custom">
            <?= $this->render('../common/_express_nav', [
                'type' => 'address',
            ]) ?>
            <div class="tab-content">
                <?php $form = ActiveForm::begin([
                    'fieldConfig' => [
                        'template' => "<div class='row'><div class='col-sm-1 text-right'>{label}</div><div class='col-sm-11'>{input}\n{hint}\n{error}</div></div>",
                    ],
                ]); ?>
                <!-- /.box-header -->
                <div class="box-body table-responsive">
                    <?= $form->field($model, 'contacts')->textInput(); ?>
                    <?= $form->field($model, 'mobile')->textInput(); ?>
                    <?= $form->field($model, 'tel_no')->textInput(); ?>
                    <?= \common\widgets\linkage\Linkage::widget([
                        'form' => $form,
                        'model' => $model,
                        // 'template' => 'short',
                    ]); ?>
                    <?= $form->field($model, 'address_details')->textarea(); ?>
                    <?= $form->field($model, 'longitude_latitude')->widget(\common\widgets\map\Map::class); ?>
                    <?= $form->field($model, 'sort')->textInput(); ?>
                    <?= $form->field($model, 'type')->radioList(MerchantAddressTypeEnum::getMap()); ?>
                    <?= $form->field($model, 'status')->radioList(StatusEnum::getMap()); ?>
                </div>
                <div class="box-footer text-center">
                    <button class="btn btn-primary" type="submit">保存</button>
                    <span class="btn btn-white" onclick="history.go(-1)">返回</span>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

