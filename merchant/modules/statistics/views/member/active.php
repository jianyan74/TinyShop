<?php

use yii\widgets\LinkPager;
use common\helpers\Url;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;
use yii\widgets\ActiveForm;

$addon = <<< HTML
<span class="input-group-addon">
    <i class="glyphicon glyphicon-calendar"></i>
</span>
HTML;

$memberActive = \addons\TinyShop\common\enums\MemberActiveEnum::getMap();

$this->title = '用户分析';
$this->params['breadcrumbs'][] = ['label' => $this->title];
?>

<div class="row">
    <div class="col-sm-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li><a href="<?= Url::to(['index']) ?>"> 用户分析</a></li>
                <?php foreach ($memberActive as $key => $value) { ?>
                    <li class="<?= $type == $key ? 'active' : ''; ?>"><a
                                href="<?= Url::to(['active', 'type' => $key]) ?>"> <?= $value; ?></a></li>
                <?php } ?>
            </ul>
            <div class="tab-content">
                <div class="active tab-pane">
                    <div class="row">
                        <span class="pb-2 pl-2 help">筛选条件: <?= $time['explain']; ?></span>
                        <div class="col-lg-12">
                            <?= GridView::widget([
                                'dataProvider' => $dataProvider,
                                'filterModel' => $searchModel,
                                //重新定义分页样式
                                'tableOptions' => ['class' => 'table table-hover'],
                                'columns' => [
                                    [
                                        'class' => 'yii\grid\SerialColumn',
                                        'visible' => false, // 不显示#
                                    ],
                                    [
                                        'attribute' => 'buyer_id',
                                        'label' => '用户ID',
                                        'headerOptions' => ['class' => 'col-md-1'],
                                    ],
                                    [
                                        'attribute' => 'member.nickname',
                                        'format' => 'raw',
                                        'value' => function ($model) {
                                            return '<span class="member-view pointer blue"
                                                    data-href='. Url::to([
                                                    '/member/view',
                                                    'member_id' => $model['buyer_id'],
                                                ]). '>
                                                ' . $model['member']['nickname'] . '
                                            </span>';
                                        },
                                    ],
                                    'member.mobile',
                                    [
                                        'attribute' => 'count',
                                        'label' => '订单笔数',
                                        'value' => function ($model) {
                                            return $model->count;
                                        }
                                    ],
                                    [
                                        'attribute' => 'product_count',
                                        'label' => '订单量',
                                    ],
                                    [
                                        'attribute' => 'pay_money',
                                        'label' => '支付金额',
                                    ],
                                    [
                                        'attribute' => 'refund_money',
                                        'label' => '退款金额',
                                    ],
                                    [
                                        'attribute' => 'order.order_sn',
                                        'label' => '最后一次订单',
                                        'format' => 'raw',
                                        'value' => function ($model) {
                                            return '<span class="order-view blue pointer"
                                        data-href=' . Url::to(['/order/order/detail', 'id' => $model['id']]) . '>
                                                                                '. $model['order']['order_sn'] .'
                                                                           </span>';
                                        }
                                    ],
                                    [
                                        'attribute' => 'created_at',
                                        'label' => '最后一次消费时间',
                                        'filter' => false, //不显示搜索框
                                        'format' => ['date', 'php:Y-m-d H:i:s'],
                                    ],
                                ],
                            ]); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
