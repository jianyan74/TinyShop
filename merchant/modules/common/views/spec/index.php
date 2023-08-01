<?php

use yii\grid\GridView;
use common\helpers\Html;
use common\helpers\Url;
use addons\TinyShop\common\enums\SpecTypeEnum;

$this->title = '商品规格';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="row">
    <div class="col-sm-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="<?= Url::to(['spec/index']) ?>">商品规格</a></li>
                <li class=""><a href="<?= Url::to(['spec-template/index']) ?>">规格模板</a></li>
                <li class="pull-right">
                    <?= Html::create(['edit']); ?>
                </li>
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
                            ],
                            'title',
                            [
                                'label' => '规格值',
                                'filter' => false, //不显示搜索框
                                'value' => function ($model) {
                                    $data = [];
                                    foreach ($model->value as $item) {
                                        $data[] = $item['title'];
                                    }

                                    return implode(',', $data);
                                },
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
                                'attribute' => 'type',
                                'filter' => Html::activeDropDownList($searchModel, 'type', SpecTypeEnum::getMap(), [
                                        'prompt' => '全部',
                                        'class' => 'form-control'
                                    ]
                                ),
                                'value' => function ($model) {
                                    return SpecTypeEnum::getValue($model->type);
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
