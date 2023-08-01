<?php

use common\helpers\Url;
use common\helpers\Html;
use common\helpers\MemberHelper;
use yii\grid\GridView;
use common\enums\StatusEnum;
use addons\TinyShop\common\helpers\OrderHelper;
use addons\TinyShop\common\enums\RefundTypeEnum;
use addons\TinyShop\common\enums\OrderAfterSaleTypeEnum;

$this->title = '售后维权';
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-sm-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="<?= $afterSale == StatusEnum::ENABLED ? '' : 'active'; ?>"><a href="<?= Url::to(['index', 'after_sale' => StatusEnum::DISABLED]) ?>">售后中</a></li>
                <li class="<?= $afterSale == StatusEnum::ENABLED ? 'active' : ''; ?>"><a href="<?= Url::to(['index', 'after_sale' => StatusEnum::ENABLED]) ?>">售后完成</a></li>
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
                            'fixedNumber' => 2,
                            'fixedRightNumber' => 1,
                        ],
                        'columns' => [
                            [
                                'class' => 'yii\grid\SerialColumn',
                            ],
                            MemberHelper::gridView($searchModel, '维权人', 'buyer_id'),
                            'orderProduct.order_sn',
                            [
                                'attribute' => 'orderProduct.product_name',
                                'headerOptions' => ['class' => 'col-md-2'],
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return Html::textNewLine($model->orderProduct->product_name, 28) . ' - ' . $model->orderProduct->sku_name;
                                },
                            ],
                            [
                                'attribute' => 'type',
                                'filter' => Html::activeDropDownList($searchModel, 'type', OrderAfterSaleTypeEnum::getMap(), [
                                        'prompt' => '全部',
                                        'class' => 'form-control',
                                    ]
                                ),
                                'value' => function ($model) {
                                    return OrderAfterSaleTypeEnum::html($model->type);
                                },
                                'format' => 'raw',
                            ],
                            'refund_apply_money',
                            [
                                'attribute' => 'refund_type',
                                'filter' => Html::activeDropDownList($searchModel, 'refund_type', RefundTypeEnum::getMap(), [
                                        'prompt' => '全部',
                                        'class' => 'form-control',
                                    ]
                                ),
                                'value' => function ($model) {
                                    return RefundTypeEnum::html($model->refund_type);
                                },
                                'format' => 'raw',
                            ],
                            'refund_reason',
                            'refund_explain',
                            [
                                'label' => '创建时间',
                                'attribute' => 'created_at',
                                'filter' => false, //不显示搜索框
                                'format' => ['date', 'php:Y-m-d H:i:s'],
                            ],
                            [
                                'label' => '操作',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return OrderHelper::refundOperation($model['id'], $model['refund_status'], $model['refund_type']);
                                },
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

<script>
    var orderProductAgreeUrl = "<?= Url::to(['pass']); ?>";
    var orderProductRefuseUrl = "<?= Url::to(['refuse']); ?>";
    var orderProductTakeDeliveryUrl = "<?= Url::to(['take-delivery']); ?>"; // 确认收货
    var orderProductDeliveryUrl = "<?= Url::to(['delivery']); ?>"; // 发货(换货)
</script>
