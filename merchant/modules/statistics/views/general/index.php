<?php

use common\helpers\Url;

$this->title = '销售分析';
$this->params['breadcrumbs'][] = ['label' => '数据统计'];
$this->params['breadcrumbs'][] = ['label' => $this->title];
?>


<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= $this->title; ?></h3>
            </div>
            <div class="box-bod">
                <div class="row p-m">
                    <div class="col-md-12 col-xs-12 m-l">
                        近30天下单金额： <?= $orderStat['pay_money'] ?? 0; ?> 元<br>
                        近30天下单会员数： <?= $orderStat['member_count'] ?? 0; ?><br>
                        近30天下单量： <?= $orderStat['count'] ?? 0; ?><br>
                        近30天下单商品数： <?= $orderStat['product_count'] ?? 0; ?><br>
                        近30天平均客单价： <?= $orderStat['customer_money'] ?? 0; ?> 元<br>
                        近30天平均价格： <?= $orderStat['average_money'] ?? 0; ?> 元
                    </div>
                    <div class="col-md-12 col-xs-12">
                        <?= \common\widgets\echarts\Echarts::widget([
                            'config' => [
                                'server' => Url::to(['data']),
                                'height' => '400px',
                            ],
                            'themeConfig' => [
                                'this30Day' => '近30天',
                                'thisMonth' => '本月',
                                'thisYear' => '本年',
                                'lastYear' => '去年',
                                'customData' => '自定义区间'
                            ],
                        ]) ?>
                        <!-- /.box -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

