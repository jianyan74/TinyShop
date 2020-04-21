<?php

use common\helpers\Url;

$this->title = '销售排行';
$this->params['breadcrumbs'][] = ['label' => '数据统计'];
$this->params['breadcrumbs'][] = ['label' => $this->title];

$rank = 0;
?>
<div class="row">
    <div class="col-md-6 col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">商品售出分析</h3>
            </div>
            <div class="box-body">
                <?= \common\widgets\echarts\Echarts::widget([
                    'config' => [
                        'server' => Url::to(['sus-res']),
                        'height' => '315px'
                    ],
                    'themeConfig' => [
                        'thisWeek' => '本周',
                        'thisMonth' => '本月',
                        'thisYear' => '本年',
                        'customData' => '自定义区间'
                    ],
                ])?>
            </div>
        </div>
        <!-- /.box -->
    </div>
    <div class="col-md-6 col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">商品构成比率</h3>
            </div>
            <div class="box-body">
                <?= \common\widgets\echarts\Echarts::widget([
                    'config' => [
                        'server' => Url::to(['product-type']),
                        'height' => '315px',
                    ],
                    'theme' => 'pie',
                    'themeConfig' => [
                        'all' => '全部',
                    ],
                ]) ?>
            </div>
        </div>
        <!-- /.box -->
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= $this->title; ?></h3>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>排行</th>
                        <th>商品名称</th>
                        <th>销售量</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($models as $model){ ?>
                        <tr>
                            <td>
                                <?php
                                $rank++;
                                echo $rank;
                                ?>
                            </td>
                            <td><?= $model['name']; ?></td>
                            <td><?= $model['real_sales']; ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>