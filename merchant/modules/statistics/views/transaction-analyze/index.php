<?php

use common\helpers\Html;
use common\helpers\Url;

$this->title = '交易分析';
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
                    <div class="col-md-12 col-xs-12 m-l">
                        总下单金额：<?= $total['pay_money'] ?? 0 ?> 元<br>
                        总下单量(件)： <?= $total['product_count'] ?? 0 ?>
                    </div>
                    <div class="col-md-12 col-xs-12">
                        <div class="row">
                            <?= \common\widgets\echarts\Echarts::widget([
                                'config' => [
                                    'server' => Url::to(['order-money']),
                                    'height' => '300px'
                                ]
                            ])?>
                        </div>
                        <!-- /.box -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">下单数量</h3>
            </div>
            <div class="box-bod">
                <div class="row p-m">
                    <div class="col-md-12 col-xs-12">
                        <?= \common\widgets\echarts\Echarts::widget([
                            'config' => [
                                'server' => Url::to(['order-create-count']),
                                'height' => '300px',
                            ],
                        ]) ?>
                        <!-- /.box -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">订单来源</h3>
            </div>
            <div class="box-body">
                <?= \common\widgets\echarts\Echarts::widget([
                    'config' => [
                        'server' => Url::to(['order-from']),
                        'height' => '300px',
                    ],
                    'theme' => 'pie',
                ]) ?>
            </div>
        </div>
        <!-- /.box -->
    </div>
    <div class="col-md-6 col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">订单类型</h3>
            </div>
            <div class="box-body">
                <?= \common\widgets\echarts\Echarts::widget([
                    'config' => [
                        'server' => Url::to(['order-type']),
                        'height' => '300px',
                    ],
                    'theme' => 'pie',
                ]) ?>
            </div>
        </div>
        <!-- /.box -->
    </div>
</div>
