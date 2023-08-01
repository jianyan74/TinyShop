<?php

use yii\grid\GridView;
use common\helpers\Html;
use common\enums\AppEnum;

$this->title = '品牌管理';
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-12 col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= $this->title; ?></h3>
                <div class="box-tools">
                    <?= Html::create(['ajax-edit'], '创建', [
                        'data-toggle' => 'modal',
                        'data-target' => '#ajaxModal',
                    ]); ?>
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
                            'attribute' => '排序',
                            'filter' => false, //不显示搜索框
                            'value' => function ($model) use ($merchant_id) {
                                if ($model->merchant_id != $merchant_id) {
                                    return false;
                                }

                                return Html::sort($model->sort);
                            },
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'label' => '类型',
                            'filter' => false, //不显示搜索框
                            'value' => function ($model) use ($merchant_id) {
                                if ($model->merchant_id != $merchant_id || Yii::$app->id == AppEnum::BACKEND) {
                                    return '平台';
                                }

                                return '商家';
                            },
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'header' => "操作",
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{template} {edit} {status} {delete}',
                            'buttons' => [
                                'edit' => function ($url, $model, $key) use ($merchant_id) {
                                    if ($model->merchant_id != $merchant_id) {
                                        return false;
                                    }

                                    return Html::edit(['ajax-edit', 'id' => $model['id']], '编辑', [
                                        'data-toggle' => 'modal',
                                        'data-target' => '#ajaxModal',
                                    ]);
                                },
                                'status' => function ($url, $model, $key) use ($merchant_id) {
                                    if ($model->merchant_id != $merchant_id) {
                                        return false;
                                    }

                                    return Html::status($model->status);
                                },
                                'delete' => function ($url, $model, $key) use ($merchant_id) {
                                    if ($model->merchant_id != $merchant_id) {
                                        return false;
                                    }

                                    return Html::delete(['delete', 'id' => $model->id]);
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