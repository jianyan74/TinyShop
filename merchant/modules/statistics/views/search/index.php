<?php

use common\helpers\Url;
use yii\grid\GridView;

$this->title = '搜索分析';
$this->params['breadcrumbs'][] = ['label' => '搜索分析'];
$this->params['breadcrumbs'][] = ['label' => $this->title];
?>

<div class="row">
    <div class="col-md-6 col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">每日搜索记录</h3>
            </div>
            <div class="box-body">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    //重新定义分页样式
                    'tableOptions' => ['class' => 'table table-hover'],
                    'columns' => [
                        [
                            'class' => 'yii\grid\SerialColumn',
                        ],
                        'keyword',
                        'num',
                        'search_date',
                    ],
                ]); ?>
            </div>
        </div>
        <!-- /.box -->
    </div>
    <div class="col-md-6 col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">综合搜索统计</h3>
            </div>
            <div class="box-body">
                <?= \common\widgets\echarts\Echarts::widget([
                    'theme' => 'wordcloud',
                    'config' => [
                        'server' => Url::to(['data']),
                        'height' => '458px'
                    ]
                ]) ?>
            </div>
        </div>
        <!-- /.box -->
    </div>
</div>