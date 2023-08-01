<?php

use yii\grid\GridView;
use yii\helpers\Json;
use common\helpers\Html;
use common\helpers\ImageHelper;
use common\enums\StatusEnum;
use addons\TinyShop\common\enums\ProductTypeEnum;

?>

<div class="col-12" style="padding: 20px">
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
                            $model['name_segmentation'] = $model['name'];
                            return [
                                'value' => $model['id'],
                                'class' => 'product_id',
                                'data-value' => Json::encode($model),
                            ];
                        }
                    ],
                    [
                        'label'=> '主图',
                        'filter' => false, //不显示搜索框
                        'value' => function ($model) {
                            if (!empty($model['picture'])) {
                                return ImageHelper::fancyBox($model['picture']);
                            }
                        },
                        'format' => 'raw',
                        'headerOptions' => ['class' => 'col-md-1'],
                    ],
                    [
                        'attribute' => 'name',
                        'format' => 'raw',
                        'value' => function ($model) use ($marketing) {
                            $html = Html::textNewLine($model['name']) . '<br>';
                            if ($model['is_member_discount'] == StatusEnum::ENABLED) {
                                $html .= Html::tag('span', '会员折扣', ['class' => 'label label-default m-r-xs']);
                            }

                            if (isset($marketing[$model['id']])) {
                                foreach ($marketing[$model['id']] as $value) {
                                    $html .= Html::tag('span', $value, ['class' => 'label label-default m-r-xs']);
                                }
                            }

                            $html .= !empty($model['is_spec']) ? Html::tag('span', '多规格', ['class' => 'label label-default m-r-xs']) : '';

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
                        'label'=> '商品分类',
                        'filter' => Html::activeDropDownList($searchModel, 'cate_id', $cate, [
                                'prompt' => '全部',
                                'class' => 'form-control'
                            ]
                        ),
                        'format' => 'raw',
                        'headerOptions' => ['class' => 'col-md-1'],
                    ],
                    [
                        'label' => '商品类型',
                        'attribute' => 'type',
                        'filter' => Html::activeDropDownList($searchModel, 'type', ProductTypeEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control'
                            ]
                        ),
                        'value' => function ($model) {
                            return ProductTypeEnum::getValue($model['type']);
                        },
                        'format' => 'raw',
                        'headerOptions' => ['class' => 'col-md-1'],
                    ],
                ],
            ]); ?>
        </div>
    </div>
</div>
