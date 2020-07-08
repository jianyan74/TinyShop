<?php

use common\helpers\Url;
use common\helpers\Html;
use addons\TinyShop\common\enums\OrderStatusEnum;

$this->title = '运营中心';
$this->params['breadcrumbs'][] = $this->title;

$rank = 0;
?>

<style>
    .info-box-number {
        font-size: 22px;
    }

    .info-box-content {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .rf-table tbody tr td{
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        word-wrap: normal;
    }
</style>

<div class="row">
    <div class="col-md-2 col-sm-6 col-xs-12">
        <div class="info-box">
            <div class="info-box-content p-md">
                <span class="info-box-number"><i class="ion ion-stats-bars red"></i> <?= $todayData['pay_money'] ?? 0 ?></span>
                <span class="info-box-text">今日订单总金额(元)</span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-md-2 col-sm-6 col-xs-12">
        <div class="info-box">
            <div class="info-box-content p-md">
                <span class="info-box-number"><i class="icon ion-bag purple"></i> <?= $productCountStat['allCount'] ?></span>
                <span class="info-box-text">商品发布(个)</span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-md-2 col-sm-6 col-xs-12">
        <div class="info-box">
            <div class="info-box-content p-md">
                <span class="info-box-number"><i class="icon ion-arrow-graph-up-right green"></i> <?= $orderCount ?></span>
                <span class="info-box-text">订单总数(笔)</span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-md-2 col-sm-6 col-xs-12">
        <div class="info-box">
            <div class="info-box-content p-md">
                <span class="info-box-number"><i class="icon ion-ios-paper-outline cyan"></i> <?= $orderThisMouthStat['count'] ?></span>
                <span class="info-box-text">本月销量(笔)</span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-md-2 col-sm-6 col-xs-12">
        <div class="info-box">
            <div class="info-box-content p-md">
                <span class="info-box-number"><i class="icon ion-ios-list blue"></i> <?= isset($orderTotalList[4]) ? $orderTotalList[4]['count'] : 0; ?></span>
                <span class="info-box-text">已完成交易(笔)</span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <div class="col-md-2 col-sm-6 col-xs-12">
        <div class="info-box">
            <div class="info-box-content p-md">
                <span class="info-box-number"><i class="icon ion-ios-star-half orange"></i> <?= $productEvaluateCount ?></span>
                <span class="info-box-text">累计评价(个)</span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
</div>

<div class="row shop-index">
    <div class="col-md-9 col-xs-12">
        <div class="box box-solid">
            <div class="box-header">
                <i class="fa fa-circle blue" style="font-size: 8px"></i>
                <h3 class="box-title">交易提示</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-6 col-md-2 text-center">
                        <span class="info-box-number blue pointer openContab" data-title="待付款" href="<?= Url::to(['order/order/index', 'order_status' => OrderStatusEnum::NOT_PAY])?>"><?= isset($orderTotalList[0]) ? $orderTotalList[0]['count'] : 0; ?></span>
                        <div class="knob-label">待付款</div>
                    </div>
                    <!-- ./col -->
                    <div class="col-xs-6 col-md-2 text-center">
                        <span class="info-box-number blue pointer openContab" data-title="待发货" href="<?= Url::to(['order/order/index', 'order_status' => OrderStatusEnum::PAY])?>"><?= isset($orderTotalList[1]) ? $orderTotalList[1]['count'] : 0; ?></span>
                        <div class="knob-label">待发货</div>
                    </div>
                    <!-- ./col -->
                    <div class="col-xs-6 col-md-2 text-center">
                        <span class="info-box-number blue pointer openContab" data-title="已发货" href="<?= Url::to(['order/order/index', 'order_status' => OrderStatusEnum::SHIPMENTS])?>"><?= isset($orderTotalList[2]) ? $orderTotalList[2]['count'] : 0; ?></span>
                        <div class="knob-label">已发货</div>
                    </div>
                    <!-- ./col -->
                    <div class="col-xs-6 col-md-2 text-center">
                        <span class="info-box-number blue pointer openContab" data-title="已收货" href="<?= Url::to(['order/order/index', 'order_status' => OrderStatusEnum::SING])?>"><?= isset($orderTotalList[3]) ? $orderTotalList[3]['count'] : 0; ?></span>
                        <div class="knob-label">已收货</div>
                    </div>
                    <div class="col-xs-6 col-md-2 text-center">
                        <span class="info-box-number blue pointer openContab" data-title="已关闭" href="<?= Url::to(['order/order/index', 'order_status' => OrderStatusEnum::REPEAL])?>"><?= isset($orderTotalList[-4]) ? $orderTotalList[-4]['count'] : 0; ?></span>
                        <div class="knob-label">已关闭</div>
                    </div>
                    <div class="col-xs-6 col-md-2 text-center">
                        <span class="info-box-number blue pointer openContab" data-title="售后申请" href="<?= Url::to(['order/order/index', 'order_status' => OrderStatusEnum::RETUREN_ING])?>"><?= isset($orderTotalList[-1]) ? $orderTotalList[-1]['count'] : 0; ?></span>
                        <div class="knob-label">售后申请</div>
                    </div>
                    <!-- ./col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </div>
    <div class="col-md-3 col-xs-12">
        <div class="box box-solid">
            <div class="box-header">
                <i class="fa fa-circle blue" style="font-size: 8px"></i>
                <h3 class="box-title">商品提示</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-6 col-md-4 text-center">
                        <span class="info-box-number blue"><?= $productCountStat['sellCount'] ?></span>
                        <div class="knob-label">出售中</div>
                    </div>
                    <div class="col-xs-6 col-md-4 text-center">
                        <span class="info-box-number blue"><?= $productCountStat['warehouseCount'] ?></span>
                        <div class="knob-label">仓库中</div>
                    </div>
                    <div class="col-xs-6 col-md-4 text-center">
                        <span class="info-box-number blue"><?= $productWarningStockCount ?></span>
                        <div class="knob-label">库存预警</div>
                    </div>
                    <!-- ./col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </div>
</div>

<div class="row shop-index">
    <div class="col-md-9 col-xs-12">
        <div class="box box-solid">
            <div class="box-header">
                <i class="fa fa-circle blue" style="font-size: 8px"></i>
                <h3 class="box-title">订单总数统计</h3>
            </div>
            <!-- /.box-header -->
            <?= \common\widgets\echarts\Echarts::widget([
                'config' => [
                    'server' => Url::to(['order-between-count']),
                    'height' => '220px'
                ]
            ])?>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </div>

    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="box box-solid">
            <div class="box-header">
                <i class="fa fa-circle blue" style="font-size: 8px"></i>
                <h3 class="box-title">销售情况统计</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-12 col-md-12 pull-right" style="padding-top: 41px;padding-bottom: 41px">
                        <table class="table">
                            <tbody>
                            <tr>
                                <td rowspan="3" align="left">昨日销量</td>
                            </tr>
                            <tr>
                                <td><span>订单量(件)</span></td>
                                <td><strong><?= $yesterdayData['product_count'] ?? 0 ?></strong></td>
                            </tr>
                            <tr>
                                <td><span>订单金额(元)</span></td>
                                <td><strong><?= $yesterdayData['pay_money'] ?? 0 ?></strong></td>
                            </tr>
                            <tr style="height: 10px;"></tr>
                            <tr>
                                <td rowspan="3" align="left">本月销量</td>
                            </tr>
                            <tr>
                                <td><span>订单量(件)</span></td>
                                <td><strong><?= $orderThisMouthStat['product_count'] ?? 0 ?></strong></td>
                            </tr>
                            <tr>
                                <td><span>订单金额(元)</span></td>
                                <td><strong><?= $orderThisMouthStat['pay_money'] ?? 0 ?></strong></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- ./col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </div>
    <div class="col-md-9 col-xs-12">
        <div class="box box-solid">
            <div class="box-header">
                <i class="fa fa-circle blue" style="font-size: 8px"></i>
                <h3 class="box-title">下单金额</h3>
            </div>
            <?= \common\widgets\echarts\Echarts::widget([
                'config' => [
                    'server' => Url::to(['sus-res']),
                    'height' => '315px'
                ]
            ])?>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="box box-solid">
            <div class="box-header">
                <i class="fa fa-circle blue" style="font-size: 8px"></i>
                <h3 class="box-title">单品销售排名</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body" style="height: 395px">
                <div class="row">
                    <div class="col-xs-12 col-md-12 pull-right">
                        <table class="table rf-table" fixedNumber="1" fixedRightNumber="1">
                            <thead>
                            <tr>
                                <th>排名</th>
                                <th>商品信息</th>
                                <th>销量</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($productRank as $item){ ?>
                                <tr>
                                    <td>
                                        <?php
                                        $rank++;
                                        echo $rank;
                                        ?>
                                    </td>
                                    <td style="max-width: 100px"><?= $item['name']; ?></td>
                                    <td><?= $item['real_sales']; ?></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- ./col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </div>
</div>