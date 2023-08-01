<?php

use yii\grid\GridView;
use common\helpers\Html;
use common\helpers\Url;
use common\enums\InvoiceTypeEnum;
use common\helpers\MemberHelper;
use addons\TinyShop\common\enums\OrderStatusEnum;
use addons\TinyShop\common\enums\OrderInvoiceAuditStatusEnum;

$this->title = '发票管理';
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="<?= $auditStatus == OrderInvoiceAuditStatusEnum::DISABLED ? 'active' : ''; ?>"><a href="<?= Url::to(['index', 'audit_status' => OrderInvoiceAuditStatusEnum::DISABLED]) ?>">待开具</a></li>
                <li class="<?= $auditStatus == OrderInvoiceAuditStatusEnum::ENABLED ? 'active' : ''; ?>"><a href="<?= Url::to(['index', 'audit_status' => OrderInvoiceAuditStatusEnum::ENABLED]) ?>">已开具</a></li>
                <li class="<?= $auditStatus == OrderInvoiceAuditStatusEnum::DELETE ? 'active' : ''; ?>"><a href="<?= Url::to(['index', 'audit_status' => OrderInvoiceAuditStatusEnum::DELETE]) ?>">已关闭</a></li>
                <li class="pull-right"></li>
            </ul>
            <div class="tab-content">
                <div class="active tab-pane">
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
                            MemberHelper::gridView($searchModel),
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
                                    return InvoiceTypeEnum::html($model->type);
                                },
                            ],
                            [
                                'attribute' => 'order.pay_money',
                                'label' => '开票金额',
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            'tax_money',
                            'title',
                            'duty_paragraph',
                            'opening_bank',
                            'address',
                            'explain',
                            [
                                'attribute' => 'order.order_status',
                                'filter' => false, //不显示搜索框
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'value' => function ($model) {
                                    return "<span class='label label-outline-info'>" . OrderStatusEnum::getValue($model->order->order_status) . "</span>";
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
                </div>
            </div>
            <!-- /.tab-content -->
        </div>
        <!-- /.nav-tabs-custom -->
    </div>
</div>
