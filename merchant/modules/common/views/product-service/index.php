<?php

use yii\grid\GridView;
use common\helpers\Url;
use common\helpers\Html;
use common\enums\AuditStatusEnum;
use common\helpers\ImageHelper;

$this->title = '商品服务';
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-sm-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <?php if(Yii::$app->services->devPattern->isB2B2C()) { ?>
                    <li><a href="<?= Url::to(['product-service-map/audit', 'auditStatus' => AuditStatusEnum::DISABLED]) ?>"> 待审核</a></li>
                    <li><a href="<?= Url::to(['product-service-map/audit', 'auditStatus' => AuditStatusEnum::ENABLED]) ?>"> 审核通过</a></li>
                    <li><a href="<?= Url::to(['product-service-map/audit', 'auditStatus' => AuditStatusEnum::DELETE]) ?>"> 审核拒绝</a></li>
                <?php } ?>
                <li class="active"><a href="<?= Url::to(['product-service/index']) ?>"> 商品服务</a></li>
                <li class="pull-right">
                    <?= Html::create(['ajax-edit'], '创建', [
                        'data-toggle' => 'modal',
                        'data-target' => '#ajaxModal',
                    ])?>
                </li>
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
                            [
                                'label' => '图标',
                                'filter' => false, //不显示搜索框
                                'value' => function ($model) {
                                    if (!empty($model->cover)) {
                                        return ImageHelper::fancyBox($model->cover);
                                    }
                                },
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            'name',
                            'explain',
                            [
                                'attribute' => 'sort',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'value' => function ($model, $key, $index, $column){
                                    return Html::sort($model->sort);
                                }
                            ],
                            [
                                'label' => '创建时间',
                                'attribute' => 'created_at',
                                'filter' => false, //不显示搜索框
                                'format' => ['date', 'php:Y-m-d H:i:s'],
                            ],
                            [
                                'header' => "操作",
                                'class' => 'yii\grid\ActionColumn',
                                'template'=> '{ajax-edit} {status} {delete}',
                                'buttons' => [
                                    'ajax-edit' => function ($url, $model, $key) {
                                        return Html::edit(['ajax-edit','id' => $model->id], '编辑', [
                                            'data-toggle' => 'modal',
                                            'data-target' => '#ajaxModal',
                                        ]);
                                    },
                                    'status' => function ($url, $model, $key) {
                                        return Html::status($model->status);
                                    },
                                    'delete' => function ($url, $model, $key) {
                                        return Html::delete(['delete','id' => $model->id]);
                                    },
                                ],
                            ],
                        ],
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>
