<?php

use yii\grid\GridView;
use common\helpers\Html;
use common\helpers\ImageHelper;
use common\helpers\Url;
use addons\TinyShop\common\enums\VirtualProductGroupEnum;
use addons\TinyShop\common\enums\PointExchangeTypeEnum;

?>

<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-body">
                <div class="col-lg-12">
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
                                    return [
                                        'value' => $model->id,
                                        'class' => 'product_id',
                                        'data-id' => $model->id,
                                        'data-name' => $model->name,
                                        'data-price' => $model->price,
                                        'data-stock' => $model->stock,
                                    ];
                                }
                            ],
                            [
                                'label'=> '主图',
                                'filter' => false, //不显示搜索框
                                'value' => function ($model) {
                                    if (!empty($model->picture)) {
                                        return ImageHelper::fancyBox($model->picture);
                                    }
                                },
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'name',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    $html = $model->name . '<br>';
                                    $group = isset($model->virtualType->group)
                                        ? VirtualProductGroupEnum::getValue($model->virtualType->group)
                                        : '普通商品';

                                    $html .= '<span class="label label-default is_hot m-r-xs">' . PointExchangeTypeEnum::getValue($model->point_exchange_type) .'</span>';
                                    $html .= '<span class="label label-default is_hot m-r-xs">' . $group .'</span>';

                                    return $html;
                                },
                            ],
                            [
                                'attribute' => 'price',
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'real_sales',
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute'=> 'stock',
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'cate.title',
                                'label'=> '产品分类',
                                'filter' => Html::activeDropDownList($searchModel, 'cate_id', $cates, [
                                        'prompt' => '全部',
                                        'class' => 'form-control'
                                    ]
                                ),
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                        ],
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>