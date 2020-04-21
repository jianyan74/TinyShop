<?php

use yii\grid\GridView;
use common\helpers\Html;
use common\helpers\Url;

$this->title = '配送人员';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-sm-12">
        <div class="nav-tabs-custom">
            <?= $this->render('../common/_express_nav', [
                'type' => 'local-distribution',
            ]) ?>
            <div class="tab-content">
                <div class="tab-pane active">
                    <div class="tab-pane active">
                        <nav class="goods-nav">
                            <ul>
                                <li class="selected"><a href="<?= Url::to(['local-distribution-member/index']) ?>">配送人员</a></li>
                                <li><a href="<?= Url::to(['local-distribution-config/edit']) ?>">配送费用</a></li>
                                <li><a href="<?= Url::to(['local-distribution-area/edit']) ?>">本地配送地区</a></li>
                            </ul>
                        </nav>
                        <div class="box">
                            <div class="box-header">
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
                                        'name',
                                        'mobile',
                                        [
                                            'header' => "操作",
                                            'class' => 'yii\grid\ActionColumn',
                                            'template' => '{template} {edit} {status} {delete}',
                                            'buttons' => [
                                                'edit' => function ($url, $model, $key) {
                                                    return Html::edit(['ajax-edit', 'id' => $model['id']], '编辑', [
                                                        'data-toggle' => 'modal',
                                                        'data-target' => '#ajaxModal',
                                                    ]);
                                                },
                                                'status' => function ($url, $model, $key) {
                                                    return Html::status($model->status);
                                                },
                                                'delete' => function ($url, $model, $key) {
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
            </div>
        </div>
    </div>
</div>