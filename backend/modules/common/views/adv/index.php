<?php

use yii\grid\GridView;
use common\helpers\Html;
use addons\TinyShop\common\enums\AdvLocalEnum;
use addons\TinyShop\common\enums\AdvJumpTypeEnum;

$this->title = '广告管理';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= $this->title; ?></h3>
                <div class="box-tools">
                    <?= Html::create(['edit']); ?>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    //重新定义分页样式
                    'tableOptions' => ['class' => 'table table-hover'],
                    'columns' => [
                        [
                            'class' => 'yii\grid\SerialColumn',
                        ],
                        'title',
                        [
                            'label'=> '广告位',
                            'filter' => Html::activeDropDownList($searchModel, 'location', AdvLocalEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control'
                                ]
                            ),
                            'value' => function ($model) {
                                return AdvLocalEnum::getValue($model->location);
                            },
                            'format' => 'raw',
                        ],
                        [
                            'label'=> '跳转类型',
                            'filter' => Html::activeDropDownList($searchModel, 'jump_type', AdvJumpTypeEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control'
                                ]
                            ),
                            'value' => function ($model) {
                                return AdvJumpTypeEnum::getValue($model->jump_type);
                            },
                            'format' => 'raw',
                        ],
                        [
                            'attribute' => 'view',
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute' => '有效时间',
                            'filter' => false, //不显示搜索框
                            'value' => function ($model) {
                                $str = [];
                                $str[] = '开始时间：' . Yii::$app->formatter->asDatetime($model->start_time);
                                $str[] = '结束时间：' . Yii::$app->formatter->asDatetime($model->end_time);

                                return implode('<br>', $str);
                            },
                            'format' => 'raw',
                        ],
                        [
                            'attribute' => '状态',
                            'filter' => false, //不显示搜索框
                            'value' => function ($model) {
                                return Html::timeStatus($model->start_time, $model->end_time);
                            },
                            'format' => 'raw',
                        ],
                        [
                            'attribute' => 'sort',
                            'filter' => false, //不显示搜索框
                            'value' => function ($model) {
                                return Html::sort($model->sort);
                            },
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'header' => "操作",
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{edit} {status} {delete}',
                            'buttons' => [
                                'edit' => function ($url, $model, $key) {
                                    return Html::edit(['edit', 'id' => $model->id]);
                                },
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