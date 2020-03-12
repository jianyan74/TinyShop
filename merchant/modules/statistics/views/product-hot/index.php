<?php
use common\helpers\Html;
use yii\grid\GridView;
use common\helpers\Url;

$this->title = '商品热卖';
$this->params['breadcrumbs'][] = ['label' => '数据统计'];
$this->params['breadcrumbs'][] = ['label' => $this->title];
?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= $this->title; ?></h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        <div class="box box-solid">
                            <div class="box-header">
                                <h3 class="box-title">下单金额(热卖商品TOP30)</h3>
                            </div>
                            <!-- /.box-header -->
                            <?= \common\widgets\echarts\Echarts::widget([
                                'theme' => 'line-graphic',
                                'config' => [
                                    'server' => Url::to(['money-data']),
                                    'height' => '500px'
                                ],
                                'themeConfig' => [
                                    'thisWeek' => '本周',
                                    'thisMonth' => '本月',
                                    'thisYear' => '今年'
                                ]
                            ]) ?>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>
                    <div class="col-md-12 col-xs-12">
                        <div class="box box-solid">
                            <div class="box-header">
                                <h3 class="box-title">下单量(热卖商品TOP30)</h3>
                            </div>
                            <!-- /.box-header -->
                            <?= \common\widgets\echarts\Echarts::widget([
                                'theme' => 'line-graphic',
                                'config' => [
                                    'server' => Url::to(['count-data']),
                                    'height' => '500px'
                                ],
                                'themeConfig' => [
                                    'thisWeek' => '本周',
                                    'thisMonth' => '本月',
                                    'thisYear' => '今年'
                                ]
                            ]) ?>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>