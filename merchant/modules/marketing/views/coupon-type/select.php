<?php

use yii\grid\GridView;
use yii\helpers\Json;
use common\helpers\Html;
use common\enums\WhetherEnum;
use addons\TinyShop\common\enums\RangeTypeEnum;
use addons\TinyShop\common\enums\DiscountTypeEnum;

?>

<div class="col-lg-12" style="padding: 20px">
    <div class="box">
        <div class="box-body">
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
                    'id' => 'grid'
                ],
                'columns' => [
                    [
                        'class' => $gridSelectType['class'],
                        $gridSelectType['property'] => function ($model, $key, $index, $column) {
                            if ($model->at_least == 0) {
                                $discount = '无门槛, ';
                            } else {
                                $discount = '满 ' . floatval($model->at_least) . ' 元, ';
                            }

                            if ($model->discount_type == DiscountTypeEnum::MONEY) {
                                $discount .= '减 ' . floatval($model->discount) . ' 元';
                            } else {
                                $discount .= '打 ' . floatval($model->discount) . ' 折';
                            }

                            return [
                                'value' => $model->id,
                                'class' => 'coupon-type-id',
                                'data-data' => Json::encode([
                                        'id' => $model->id,
                                        'title' => $model->title,
                                        'range_type' => RangeTypeEnum::getValue($model->range_type),
                                        'discount' => $discount,
                                        'stock' => $model->count - $model->get_count,
                                ]),
                            ];
                        }
                    ],
                    'title',
                    [
                        'attribute' => 'range_type',
                        'format' => 'raw',
                        'filter' => Html::activeDropDownList($searchModel, 'range_type', RangeTypeEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control'
                            ]
                        ),
                        'value' => function ($model) {
                            return RangeTypeEnum::getValue($model->range_type);
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
                                $str = '满 ' . $model->at_least . ' 元, ';
                            }

                            if ($model->discount_type == DiscountTypeEnum::MONEY) {
                                $str .= '减 ' . $model->discount . ' 元';
                            } else {
                                $str .= '打 ' . $model->discount . ' 折';
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
                            if ($model->term_of_validity_type != \common\enums\StatusEnum::ENABLED) {
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
                ],
            ]); ?>
        </div>
    </div>
</div>
