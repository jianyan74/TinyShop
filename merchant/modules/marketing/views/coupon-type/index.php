<?php

use common\helpers\Html;
use yii\grid\GridView;
use addons\TinyShop\common\enums\RangeTypeEnum;
use addons\TinyShop\common\enums\PreferentialTypeEnum;

$this->title = '优惠券';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= $this->title; ?></h3>
                <div class="box-tools">
                    <?= Html::create(['edit']) ?>
                </div>
            </div>
            <div class="box-body table-responsive">
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
                            'label' => '类型',
                            'attribute' => 'type',
                            'format' => 'raw',
                            'filter' => Html::activeDropDownList($searchModel, 'type', PreferentialTypeEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                ]
                            ),
                            'value' => function ($model) {
                                return "<span class='label label-primary'>" . PreferentialTypeEnum::getValue($model->type) . "</span>";
                            },
                        ],
                        [
                            'attribute' => 'range_type',
                            'format' => 'raw',
                            'filter' => Html::activeDropDownList($searchModel, 'range_type', RangeTypeEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                ]
                            ),
                            'value' => function ($model) {
                                return RangeTypeEnum::getValue($model->range_type);
                            },
                        ],
                        [
                            'label' => '面额/折扣',
                            'format' => 'raw',
                            'value' => function ($model) {
                                if ($model->type == 1) {
                                    return $model->money . '元';
                                } else {
                                    return $model->discount / 10 . '折';
                                }
                            },
                        ],
                        [
                            'attribute' => 'count',
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'label' => '剩余数量',
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
                                $html .= '开始时间：' . Yii::$app->formatter->asDatetime($model->get_start_time) . "<br>";
                                $html .= '结束时间：' . Yii::$app->formatter->asDatetime($model->get_end_time) . "<br>";
                                $html .= '有效状态：' . Html::timeStatus($model->get_start_time, $model->get_end_time);

                                return $html;
                            },
                        ],
                        [
                            'label' => '生效时间',
                            'format' => 'raw',
                            'value' => function ($model) {
                                if ($model->term_of_validity_type != \common\enums\StatusEnum::ENABLED) {
                                    $html = '';
                                    $html .= '开始时间：' . Yii::$app->formatter->asDatetime($model->start_time) . "<br>";
                                    $html .= '结束时间：' . Yii::$app->formatter->asDatetime($model->end_time) . "<br>";
                                    $html .= '有效状态：' . Html::timeStatus($model->start_time, $model->end_time);

                                    return $html;
                                } else {
                                    $day = $model->fixed_term;

                                    return "领取之日起{$day}天内有效";
                                }
                            },
                        ],
                        [
                            'attribute' => 'is_show',
                            'filter' => Html::activeDropDownList($searchModel, 'is_show',
                                \common\enums\WhetherEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                ]
                            ),
                            'value' => function ($model) {
                                return Html::whether($model->is_show);
                            },
                            'format' => 'raw',
                        ],
                        [
                            'header' => "操作",
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{coupon} {give} {edit} {status} {delete}',
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
                                'status' => function ($url, $model, $key) {
                                    return Html::status($model->status);
                                },
                                'edit' => function ($url, $model, $key) {
                                    return Html::edit(['edit', 'id' => $model['id']]);
                                },
                                'delete' => function ($url, $model, $key) {
                                    return Html::delete(['delete', 'id' => $model->id]);
                                },
                            ],
                        ],
                    ],
                ]); ?>
            </div>
        </div>
    </div>
</div>

