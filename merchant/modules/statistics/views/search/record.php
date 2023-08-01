<?php

use common\helpers\Url;
use yii\grid\GridView;

$this->title = '每日搜索记录';
$this->params['breadcrumbs'][] = ['label' => $this->title];
?>

<div class="row">
    <div class="col-sm-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li><a href="<?= Url::to(['index']) ?>"> 综合搜索统计</a></li>
                <li class="active"><a href="<?= Url::to(['record']) ?>"> 每日搜索记录</a></li>
            </ul>
            <div class="tab-content">
                <div class="active tab-pane">
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
        </div>
    </div>
</div>