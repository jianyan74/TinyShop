<?php

use yii\widgets\ActiveForm;
use common\helpers\Url;
use common\enums\WhetherEnum;

$this->title = '门店自提';
$this->params['breadcrumbs'][] = ['label' => $this->title];
?>

<div class="row">
    <div class="col-sm-12">
        <div class="nav-tabs-custom">
            <?= $this->render('../common/_express_nav', [
                'type' => 'point',
            ]) ?>
            <div class="tab-content">
                <div class="tab-pane active">
                    <div class="tab-pane active">
                        <nav class="goods-nav">
                            <ul>
                                <li><a href="<?= Url::to(['pickup-point/index']) ?>">门店管理</a></li>
                                <li class="selected"><a href="<?= Url::to(['pickup-point/config']) ?>">门店运费</a></li>
                                <li><a href="<?= Url::to(['pickup-auditor/index']) ?>">门店审核人员</a></li>
                            </ul>
                        </nav>
                        <div class="box">
                            <?php $form = ActiveForm::begin([
                                'fieldConfig' => [
                                    'template' => "<div class='col-sm-2 text-right'>{label}</div><div class='col-sm-10'>{input}\n{hint}\n{error}</div>",
                                ],
                            ]); ?>
                            <!-- /.box-header -->
                            <div class="box-body table-responsive">
                                <?= $form->field($model, 'pickup_point_fee')->textInput(); ?>
                                <?= $form->field($model, 'pickup_point_freight')->textInput(); ?>
                                <?= $form->field($model,
                                    'pickup_point_is_open')->radioList(WhetherEnum::getMap()); ?>
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