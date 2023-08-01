<?php

use common\helpers\Url;
use common\helpers\Html;
use yii\grid\GridView;
use common\enums\WhetherEnum;
use common\enums\StatusEnum;
use addons\TinyShop\common\enums\RangeTypeEnum;
use addons\TinyShop\common\enums\DiscountTypeEnum;

$this->title = '优惠券';
$this->params['breadcrumbs'][] = $this->title;

?>


<div class="row">
    <div class="col-sm-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="<?= Url::to(['index']) ?>"><?= $this->title; ?></a></li>
                <li><a href="<?= Url::to(['coupon/index']) ?>">发放记录</a></li>
                <li class="pull-right"><?= Html::create(['edit']) ?></li>
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
                            'title',
                            [
                                'attribute' => 'range_type',
                                'format' => 'raw',
                                'filter' => Html::activeDropDownList($searchModel, 'range_type', RangeTypeEnum::getFullMap(), [
                                        'prompt' => '全部',
                                        'class' => 'form-control',
                                    ]
                                ),
                                'value' => function ($model) {
                                    return RangeTypeEnum::getFullValue($model->range_type);
                                },
                            ],
                            [
                                'label' => '优惠内容',
                                'format' => 'raw',
                                'filter' => Html::activeDropDownList($searchModel, 'discount_type', DiscountTypeEnum::getMap(), [
                                        'prompt' => '全部',
                                        'class' => 'form-control',
                                    ]
                                ),
                                'value' => function ($model) {
                                    if ($model->at_least == 0) {
                                        $str = '无门槛, ';
                                    } else {
                                        $str = '满 ' . floatval($model->at_least) . ' 元, ';
                                    }

                                    if ($model->discount_type == DiscountTypeEnum::MONEY) {
                                        $str .= '减 ' . floatval($model->discount) . ' 元';
                                    } else {
                                        $str .= '打 ' . floatval($model->discount) . ' 折';
                                    }

                                    return $str;
                                },
                            ],
                            [
                                'label' => '库存',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'value' => function ($model) {
                                    return $model->count - $model->get_count;
                                },
                            ],
                            [
                                'label' => '可领取时间',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    $html = '';
                                    $html .= '开始：' . Yii::$app->formatter->asDatetime($model->get_start_time) . "<br>";
                                    $html .= '结束：' . Yii::$app->formatter->asDatetime($model->get_end_time) . "<br>";
                                    $html .= '状态：' . Html::timeStatus($model->get_start_time, $model->get_end_time);

                                    return $html;
                                },
                            ],
                            [
                                'label' => '生效时间',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    if ($model->term_of_validity_type != StatusEnum::ENABLED) {
                                        $html = '';
                                        $html .= '开始：' . Yii::$app->formatter->asDatetime($model->start_time) . "<br>";
                                        $html .= '结束：' . Yii::$app->formatter->asDatetime($model->end_time) . "<br>";
                                        $html .= '状态：' . Html::timeStatus($model->start_time, $model->end_time);

                                        return $html;
                                    } else {
                                        $day = $model->fixed_term;

                                        return "领取之日起{$day}天内有效";
                                    }
                                },
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
                                'attribute' => 'is_list_visible',
                                'filter' => Html::activeDropDownList($searchModel, 'is_list_visible', WhetherEnum::getMap(), [
                                        'prompt' => '全部',
                                        'class' => 'form-control',
                                    ]
                                ),
                                'value' => function ($model) {
                                    return WhetherEnum::html($model->is_list_visible);
                                },
                                'format' => 'raw',
                            ],
                            'remark',
                            [
                                'header' => "操作",
                                'class' => 'yii\grid\ActionColumn',
                                'template' => '{coupon} {give} {edit} {delete}',
                                'buttons' => [
                                    'coupon' => function ($url, $model, $key) {
                                        return Html::linkButton([
                                            'coupon/index',
                                            'coupon_type_id' => $model['id'],
                                        ], '发放记录');
                                    },
                                    'give' => function ($url, $model, $key) {
                                        return Html::linkButton([
                                            'give',
                                            'coupon_type_id' => $model['id'],
                                        ], '赠送', [
                                            'data-toggle' => 'modal',
                                            'data-target' => '#ajaxModal',
                                        ]);
                                    },
                                    'edit' => function ($url, $model, $key) {
                                        return Html::edit(['edit', 'id' => $model['id']]);
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

