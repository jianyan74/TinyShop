<?php

use common\helpers\Url;
use yii\grid\GridView;
use common\helpers\ImageHelper;

?>

<div class="row p-m">
    <div class="col-sm-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li><a href="<?= Url::to(['view', 'member_id' => $member_id]) ?>">用户信息</a></li>
                <li class="active"><a href="<?= Url::to(['footprint', 'member_id' => $member_id]) ?>">足迹</a></li>
            </ul>
            <div class="tab-content">
                <div class="active tab-pane">
                    <div class="box-body">
                        <?= GridView::widget([
                            'dataProvider' => $dataProvider,
                            //重新定义分页样式
                            'tableOptions' => [
                                'class' => 'table table-hover rf-table',
                                'fixedNumber' => 2,
                                'fixedRightNumber' => 2,
                            ],
                            'columns' => [
                                [
                                    'class' => 'yii\grid\SerialColumn',
                                ],
                                [
                                    'label' => '商品封面',
                                    'format' => 'raw',
                                    'filter' => false, //不显示搜索框
                                    'value' => function ($model) {
                                        return ImageHelper::fancyBox($model['product']['picture']);
                                    },
                                ],
                                'product.name',
                                'num',
                                [
                                    'label' => '访问日期',
                                    'attribute' => 'updated_at',
                                    'filter' => false, //不显示搜索框
                                    'format' => ['date', 'php:Y-m-d'],
                                ],
                            ],
                        ]); ?>
                    </div>
                </div>
            </div>
            <!-- /.tab-content -->
        </div>
        <!-- /.nav-tabs-custom -->
    </div>
</div>
