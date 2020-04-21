<?php

use yii\widgets\ActiveForm;
use common\helpers\Url;

$this->title = '商家地址';
$this->params['breadcrumbs'][] = ['label' => $this->title];
?>

<div class="row">
    <div class="col-sm-12">
        <div class="nav-tabs-custom">
            <?= $this->render('../common/_express_nav', [
                'type' => 'address',
            ]) ?>
            <div class="tab-content">
                <div class="tab-pane active">
                    <div class="tab-pane active">
                        <div class="box">
                            <?php $form = ActiveForm::begin([
                                'fieldConfig' => [
                                    'template' => "<div class='col-sm-2 text-right'>{label}</div><div class='col-sm-10'>{input}\n{hint}\n{error}</div>",
                                ],
                            ]); ?>
                            <!-- /.box-header -->
                            <div class="box-body table-responsive">
                                <?= $form->field($model, 'merchant_address')->textInput(); ?>
                                <?= $form->field($model, 'merchant_name')->textInput(); ?>
                                <?= $form->field($model, 'merchant_mobile')->textInput(); ?>
                                <?= $form->field($model, 'merchant_zip_code')->textInput(); ?>
                                <?= $form->field($model, 'merchant_longitude_latitude')->widget(\common\widgets\selectmap\Map::class, [
                                    'type' => 'amap', // amap高德;tencent:腾讯;baidu:百度
                                ]); ?>
                                <!-- /.box-body -->
                            </div>
                            <div class="box-footer text-center">
                                <button class="btn btn-primary" type="submit">保存</button>
                                <span class="btn btn-white" onclick="history.go(-1)">返回</span>
                            </div>
                            <?php ActiveForm::end(); ?>
                            <!-- /.box -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

