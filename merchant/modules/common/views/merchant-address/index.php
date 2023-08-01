<?php

use yii\grid\GridView;
use common\helpers\Url;
use common\helpers\Html;
use common\enums\WhetherEnum;
use addons\TinyShop\common\enums\MerchantAddressTypeEnum;

$this->title = '商家地址库';
$this->params['breadcrumbs'][] = ['label' => $this->title];

?>

<div class="row">
    <div class="col-sm-12">
        <div class="nav-tabs-custom">
            <?= $this->render('../common/_express_nav', [
                'type' => 'address',
            ]) ?>
            <div class="tab-content">
                <div class="tab-pane active">
                    <div class="box">
                        <div class="box-header">
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
                                    'contacts',
                                    'mobile',
                                    'tel_no',
                                    'address_name',
                                    'address_details',
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
                                        'label'=> '地址类型',
                                        'filter' => Html::activeDropDownList($searchModel, 'type', MerchantAddressTypeEnum::getMap(), [
                                                'prompt' => '全部',
                                                'class' => 'form-control'
                                            ]
                                        ),
                                        'value' => function ($model) {
                                            return MerchantAddressTypeEnum::getValue($model->type);
                                        },
                                        'format' => 'raw',
                                    ],
                                    [
                                        'label' => '创建时间',
                                        'attribute' => 'created_at',
                                        'filter' => false, //不显示搜索框
                                        'format' => ['date', 'php:Y-m-d H:i'],
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