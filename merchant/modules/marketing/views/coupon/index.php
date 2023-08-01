<?php

use yii\grid\GridView;
use common\helpers\Url;
use common\helpers\Html;
use common\enums\UseStateEnum;
use common\enums\WhetherEnum;
use addons\TinyShop\common\enums\CouponGetTypeEnum;

$this->title = '发放记录';
$this->params['breadcrumbs'][] = ['label' => '优惠劵', 'url' => ['coupon-type/index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-sm-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li><a href="<?= Url::to(['coupon-type/index']) ?>">优惠券</a></li>
                <li class="active"><a href="<?= Url::to(['coupon/index']) ?>">发放记录</a></li>
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
                                'visible' => true, // 不显示#
                            ],
                            [
                                'attribute' => 'couponType.title',
                                'label'=> '优惠券名称',
                            ],
                            [
                                'attribute' => 'member.nickname',
                                'label'=> '领用人',
                                'filter' => Html::activeTextInput($searchModel, 'member_id', [
                                        'class' => 'form-control'
                                    ]
                                ),
                            ],
                            'code',
                            [
                                'attribute' => 'discount',
                                'filter' => false, //不显示搜索框
                                'value' => function ($model) {
                                    if (in_array($model->state, [UseStateEnum::USE, UseStateEnum::GET])) {
                                        return $model->discount;
                                    } else {
                                        return $model->couponType->discount;
                                    }
                                },
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'state',
                                'value' => function ($model) {
                                    return UseStateEnum::getValue($model->state);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'state', UseStateEnum::getMap(), [
                                        'prompt' => '全部',
                                        'class' => 'form-control'
                                    ]
                                ),
                            ],
                            [
                                'attribute' => 'get_type',
                                'value' => function ($model) {
                                    return CouponGetTypeEnum::getValue($model->get_type);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'get_type', CouponGetTypeEnum::getMap(), [
                                        'prompt' => '全部',
                                        'class' => 'form-control'
                                    ]
                                ),
                            ],
                            [
                                'attribute' => 'single_type',
                                'filter' => Html::activeDropDownList($searchModel, 'single_type', WhetherEnum::getMap(), [
                                        'prompt' => '全部',
                                        'class' => 'form-control',
                                    ]
                                ),
                                'value' => function ($model) {
                                    return WhetherEnum::html($model->single_type);
                                },
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'fetch_time',
                                'filter' => false, //不显示搜索框
                                'value' => function ($model) {
                                    return $model->fetch_time ? Yii::$app->formatter->asDatetime($model->fetch_time) : '---';
                                },
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'use_time',
                                'filter' => false, //不显示搜索框
                                'value' => function ($model) {
                                    return $model->use_time ? Yii::$app->formatter->asDatetime($model->use_time) : '---';
                                },
                                'format' => 'raw',
                            ],
                            [
                                'label' => '过期时间',
                                'attribute' => 'end_time',
                                'filter' => false, //不显示搜索框
                                'value' => function ($model) {
                                    return Yii::$app->formatter->asDatetime($model->end_time);
                                },
                                'format' => 'raw',
                            ],
                            [
                                'header' => "操作",
                                'class' => 'yii\grid\ActionColumn',
                                'template' => '{revocation}',
                                'buttons' => [
                                    'revocation' => function ($url, $model, $key) {
                                        if ($model->state == UseStateEnum::GET) {
                                            return Html::linkButton(['revocation', 'id' => $model['id']], '撤回', [
                                                'onclick' => "rfTwiceAffirm(this, '确定撤回优惠券吗？', '请谨慎操作');return false;",
                                            ]);
                                        }
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

