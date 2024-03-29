<?php

use yii\grid\GridView;
use common\helpers\Html;
use common\helpers\Url;
use common\helpers\ImageHelper;
use common\helpers\MemberHelper;
use addons\TinyShop\common\enums\ExplainTypeEnum;

$this->title = '订单商品评价';
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= $this->title; ?></h3>
            </div>
            <!-- /.box-header -->
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
                    'columns' => [
                        'id',
                        [
                            'label' => '商品封面',
                            'filter' => false, //不显示搜索框
                            'value' => function ($model) {
                                if (!empty($model->product_picture)) {
                                    return ImageHelper::fancyBox($model->product_picture);
                                }
                            },
                            'format' => 'raw',
                        ],
                        [
                            'attribute' => 'product_name',
                            'headerOptions' => ['class' => 'col-md-2'],
                            'format' => 'raw',
                            'value' => function ($model) {
                                return Html::textNewLine($model->product_name, 20);
                            },
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
                            'attribute' => 'content',
                            'label' => '内容',
                            'format' => 'raw',
                            'value' => function ($model) {

                                $str = [];
                                $str[] = ImageHelper::fancyBoxs($model->covers);
                                $str[] = Html::encode($model->content);
                                !empty($model->explain_first) && $str[] = '回复：' . Html::encode($model->explain_first);
                                $str[] = Yii::$app->formatter->asDatetime($model->created_at);

                                if (Yii::$app->services->devPattern->isMerchantLocation()) {
                                    $str[] = Html::a('回复', ['ajax-edit', 'id' => $model['id']], [
                                        'data-toggle' => 'modal',
                                        'data-target' => '#ajaxModal',
                                        'class' => 'blue',
                                    ]);
                                }

                                return implode('<br>', $str);
                            },
                            'headerOptions' => ['class' => 'col-md-2'],
                            'contentOptions' => ['style' => 'max-width:300px;word-break: break-all']
                        ],
                        [
                            'attribute' => 'again_content',
                            'label' => '追加内容',
                            'format' => 'raw',
                            'value' => function ($model) {
                                $str = [];
                                if ($model->again_content) {
                                    $str[] = ImageHelper::fancyBoxs($model->again_covers);
                                    $str[] = Html::encode($model->again_content);
                                    !empty($model->again_explain) && $str[] = '回复：' . Html::encode($model->again_explain);
                                    $str[] = Yii::$app->formatter->asDatetime($model->again_addtime);
                                    if (Yii::$app->services->devPattern->isMerchantLocation()) {
                                        $str[] = Html::a('回复', ['ajax-edit', 'id' => $model['id'], 'type' => 'again'], [
                                            'data-toggle' => 'modal',
                                            'data-target' => '#ajaxModal',
                                            'class' => 'blue',
                                        ]);
                                    }
                                } else {
                                    $str[] = '---';
                                }

                                return implode('<br>', $str);
                            },
                            'headerOptions' => ['class' => 'col-md-2'],
                            'contentOptions' => ['style' => 'max-width:300px;word-break: break-all']
                        ],
                        [
                            'attribute' => 'scores',
                            'filter' => Html::activeDropDownList($searchModel, 'scores', [
                                1 => 1,
                                2 => 2,
                                3 => 3,
                                4 => 4,
                                5 => 5
                            ], [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                ]
                            ),
                            'format' => 'raw',
                            'value' => function ($model) {
                                $str = [];
                                for ($i = 0; $i < $model->scores; $i++) {
                                    $str[] = "<span class='fa fa-star orange'></span> ";
                                }

                                return implode('', $str);
                            },
                        ],
                        [
                            'attribute' => 'explain_type',
                            'label' => '评价级别',
                            'filter' => Html::activeDropDownList($searchModel, 'explain_type',ExplainTypeEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                ]
                            ),
                            'format' => 'raw',
                            'value' => function ($model) {
                                return ExplainTypeEnum::getValue($model->explain_type);
                            },
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'header' => "操作",
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{edit} {status} {delete}',
                            'buttons' => [
                                'status' => function ($url, $model, $key) {
                                    return Html::status($model->status);
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
