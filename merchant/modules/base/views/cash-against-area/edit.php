<?php

use yii\widgets\ActiveForm;
use common\helpers\Url;

$this->title = '货到付款区域';
$this->params['breadcrumbs'][] = ['label' => $this->title];
?>

<div class="row">
    <div class="col-sm-12">
        <div class="nav-tabs-custom">
            <?= $this->render('../common/_express_nav', [
                'type' => 'company',
            ]) ?>
            <div class="tab-content">
                <div class="tab-pane active">
                    <div class="tab-pane active">
                        <nav class="goods-nav">
                            <ul>
                                <li><a href="<?= Url::to(['express-company/index']) ?>">物流配送</a></li>
                                <li class="selected"><a href="<?= Url::to(['cash-against-area/edit']) ?>">货到付款地区</a></li>
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
                                <div class="form-group">
                                    <div class="col-sm-2 text-right">
                                        <label class="control-label">支持地区</label>
                                    </div>
                                    <div class="col-sm-10">
                                        <a class="js-select-city btn btn-primary btn-sm" data-toggle="modal" data-target="#ajaxModalLgForExpress">指定地区城市</a>
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-2 text-right">
                                        <label class="control-label">已选择地区</label>
                                    </div>
                                    <div class="col-sm-10">
                                        <span class="js-region-info region-info"></span>
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                            </div>
                            <div class="box-footer text-center">
                                <button class="btn btn-primary" type="submit">保存</button>
                            </div>

                            <!-- 地区选择工具 -->
                            <?= \common\widgets\area\Area::widget([
                                'model' => $model,
                                'form' => $form,
                                'provincesName' => 'province_ids',
                                'cityName' => 'city_ids',
                                'areaName' => 'area_ids',
                                'level' => 3
                            ]) ?>

                            <?php ActiveForm::end(); ?>
                            <!-- /.box -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

