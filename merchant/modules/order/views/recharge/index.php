<?php

use yii\grid\GridView;
use common\helpers\Html;
use common\helpers\Url;
use common\helpers\MemberHelper;
use common\enums\StatusEnum;
use common\enums\PayTypeEnum;

$this->title = '充值订单';
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-sm-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="<?= $payStatus == StatusEnum::ENABLED ? 'active': '';?>"><a href="<?= Url::to(['index', 'pay_status' => StatusEnum::ENABLED]) ?>">已付款</a></li>
                <li class="<?= $payStatus == StatusEnum::DISABLED ? 'active': '';?>"><a href="<?= Url::to(['index', 'pay_status' => StatusEnum::DISABLED]) ?>">未付款</a></li>
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
                        'options' => [
                            'id' => 'grid',
                        ],
                        'columns' => [
                            [
                                'class' => 'yii\grid\SerialColumn',
                                'visible' => true, // 不显示#
                            ],
                            MemberHelper::gridView($searchModel),
                            'order_sn',
                            'out_trade_no',
                            'price',
                            [
                                'label' => '赠送',
                                'value' => function ($model) {
                                    $str = [];
                                    $str[] = '金额: ' . $model->give_price;
                                    $str[] = '成长值: ' . $model->give_growth;
                                    $str[] = '积分: ' . $model->give_point;
                                    return implode('</br>', $str);
                                },
                                'format' => 'raw',
                            ],
                            [
                                'label' => '支付类型',
                                'value' => function ($model) {
                                    return PayTypeEnum::getValue($model->pay_type);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'pay_type', PayTypeEnum::thirdParty(), [
                                        'prompt' => '全部',
                                        'class' => 'form-control'
                                    ]
                                ),
                                'format' => 'raw',
                            ],
                            [
                                'label' => '订单时间',
                                'attribute' => 'created_at',
                                'filter' => false, //不显示搜索框
                                'format' => 'raw',
                                'value' => function ($model) {
                                    $str = [];
                                    $str[] = '创建: ' . Yii::$app->formatter->asDatetime($model->created_at);
                                    $str[] = '支付: ' . (!empty($model->pay_time) ? Yii::$app->formatter->asDatetime($model->created_at) : '---');
                                    return implode('</br>', $str);
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

