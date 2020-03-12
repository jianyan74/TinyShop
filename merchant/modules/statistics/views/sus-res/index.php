<?php

use common\helpers\Url;

$this->title = '运营报告';
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
                                    'server' => Url::to(['data']),
                                    'height' => '480px'
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