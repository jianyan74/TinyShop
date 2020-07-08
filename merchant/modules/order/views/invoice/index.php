<?php

use yii\grid\GridView;
use common\helpers\Html;
use common\helpers\Url;
use common\enums\InvoiceTypeEnum;
use addons\TinyShop\common\enums\OrderStatusEnum;

$this->title = '发票管理';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= $this->title; ?></h3>
                <div class="box-tools">

                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    //重新定义分页样式
                    'tableOptions' => [
                        'class' => 'table table-hover rf-table',
                        'fixedNumber' => 3,
                        'fixedRightNumber' => 1,
                    ],
                    'columns' => [
                        [
                            'class' => 'yii\grid\SerialColumn',
                        ],
                        [
                            'attribute' => 'order_sn',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return '<span class="order-view pointer" data-href="' . Url::to(['/order/order/detail', 'id' => $model->order_id]) . '">' . $model->order_sn . '</span>';
                            },
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute' => 'member.nickname',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return '<span class="member-view pointer" data-href="' . Url::to(['/member/view', 'member_id' => $model->member->id]) . '">' . $model->member->nickname . '</span>';
                            },
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute' => 'type',
                            'filter' => Html::activeDropDownList($searchModel, 'type', InvoiceTypeEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                ]
                            ),
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                            'value' => function ($model) {
                                return InvoiceTypeEnum::getValue($model->type);
                            },
                        ],
                        'tax_money',
                        'title',
                        'duty_paragraph',
                        'content',
                        [
                            'attribute' => 'order.order_status',
                            'filter' => false, //不显示搜索框
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                            'value' => function ($model) {
                                return "<span class='label label-primary'>" . OrderStatusEnum::getValue($model->order->order_status) . "</span>";
                            },
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
                            'template' => '{edit} {status} {delete}',
                            'buttons' => [
                                'edit' => function ($url, $model, $key) {
                                    return Html::edit(['ajax-edit', 'id' => $model['id']], '编辑', [
                                        'data-toggle' => 'modal',
                                        'data-target' => '#ajaxModal',
                                    ]);
                                },
                                'delete' => function ($url, $model, $key) {
                                    return Html::delete(['destroy', 'id' => $model->id]);
                                },
                            ],
                        ],
                    ],
                ]); ?>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
    </div>
</div>