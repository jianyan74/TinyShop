<?php

use common\helpers\Url;
use common\enums\UseStateEnum;

$this->title = '优惠券分析';
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
</style>

<div class="row">
    <?php foreach ($groupCount as $item) { ?>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <div class="info-box-content p-md">
                    <span class="info-box-number"><?= $item['count'] ?? 0 ?></span>
                    <span class="info-box-text"><?= UseStateEnum::getValue($item['state']) ?? '' ?></span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
    <?php } ?>
</div>

<div class="row">
    <div class="col-12">
        <div class="box box-solid">
            <div class="box-header">
                <i class="fa fa-circle blue" style="font-size: 8px"></i>
                <h3 class="box-title">优惠券领取统计</h3>
            </div>
            <!-- /.box-header -->
            <?= \common\widgets\echarts\Echarts::widget([
                'config' => [
                    'server' => Url::to(['get-count']),
                    'height' => '220px'
                ]
            ])?>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="box box-solid">
            <div class="box-header">
                <i class="fa fa-circle blue" style="font-size: 8px"></i>
                <h3 class="box-title">优惠券使用统计</h3>
            </div>
            <!-- /.box-header -->
            <?= \common\widgets\echarts\Echarts::widget([
                'config' => [
                    'server' => Url::to(['unsed-count']),
                    'height' => '220px'
                ]
            ])?>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="box box-solid">
            <div class="box-header">
                <i class="fa fa-circle blue" style="font-size: 8px"></i>
                <h3 class="box-title">优惠券过期统计</h3>
            </div>
            <!-- /.box-header -->
            <?= \common\widgets\echarts\Echarts::widget([
                'config' => [
                    'server' => Url::to(['past-due-count']),
                    'height' => '220px'
                ]
            ])?>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </div>
</div>
