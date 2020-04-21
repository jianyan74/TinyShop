<?php

use yii\grid\GridView;
use common\helpers\Html;
use common\helpers\ImageHelper;
use common\helpers\Url;
use common\enums\StatusEnum;
use addons\TinyShop\common\enums\VirtualProductGroupEnum;
use addons\TinyShop\common\enums\ProductShippingTypeEnum;

$this->title = '商品管理';
$this->params['breadcrumbs'][] = ['label' => $this->title];
?>

<div class="row">
    <div class="col-sm-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="<?php if ($product_status == 1 && $stock_warning != 1) { ?>active<?php } ?>"><a
                            href="<?= Url::to(['index', 'product_status' => 1]) ?>">出售中</a></li>
                <li class="<?php if ($product_status == 0) { ?>active<?php } ?>"><a
                            href="<?= Url::to(['index', 'product_status' => 0]) ?>">已下架</a></li>
                <li class="<?php if ($stock_warning == 1) { ?>active<?php } ?>"><a
                            href="<?= Url::to(['index', 'stock_warning' => 1]) ?>">库存报警</a></li>
                <li><a href="<?= Url::to(['recycle']) ?>">回收站</a></li>
                <li class="pull-right">
                    <?= Html::create(['edit'], '创建'); ?>
                </li>
            </ul>
            <div class="tab-content">
                <div class="col-sm-12 m-b-sm">
                    <?= Html::a('批量删除</a>', "javascript:void(0);",
                        ['class' => 'btn btn-white btn-sm m-l-n-md delete-all']); ?>
                    <?= Html::a('上架</a>', "javascript:void(0);", ['class' => 'btn btn-white btn-sm putaway-all']); ?>
                    <?= Html::a('下架</a>', "javascript:void(0);", ['class' => 'btn btn-white btn-sm sold-out-all']); ?>
                </div>
                <div class="active tab-pane">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        //重新定义分页样式
                        'tableOptions' => [
                            'class' => 'table table-hover rf-table',
                            'fixedNumber' => 3,
                            'fixedRightNumber' => 1,
                        ],
                        'options' => [
                            'id' => 'grid',
                        ],
                        'columns' => [
                            [
                                'class' => 'yii\grid\CheckboxColumn',
                                'checkboxOptions' => function ($model, $key, $index, $column) {
                                    return ['value' => $model->id];
                                },
                            ],
                            [
                                'class' => 'yii\grid\SerialColumn',
                                'visible' => true, // 不显示#
                            ],
                            [
                                'label' => '主图',
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
                                    $html .= empty($model['is_hot']) ? '<span class="label label-default m-r-xs">热门</span>' : '<span class="label label-success m-r-xs">热门</span>';
                                    $html .= empty($model['is_recommend']) ? '<span class="label label-default m-r-xs">推荐</span>' : '<span class="label label-success m-r-xs">推荐</span>';
                                    $html .= empty($model['is_new']) ? '<span class="label label-default m-r-xs">新品</span>' : '<span class="label label-success m-r-xs">新品</span>';
                                    $html .= $model['shipping_type'] == ProductShippingTypeEnum::FULL_MAIL ? '<span class="label label-default m-r-xs">包邮</span>' : '<span class="label label-success m-r-xs">包邮</span>';
                                    $html .= $model['is_open_commission'] == StatusEnum::DISABLED ? '' : '<span class="label label-success">分销</span> ';
                                    $html .= $model['is_open_presell'] == StatusEnum::DISABLED ? '' : '<span class="label label-success">预售</span>';

                                    return $html;
                                },
                            ],
                            [
                                'label' => '销售价',
                                'attribute' => 'price',
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'real_sales',
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'stock',
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'cate.title',
                                'label' => '产品分类',
                                'filter' => Html::activeDropDownList($searchModel, 'cate_id', $cates, [
                                        'prompt' => '全部',
                                        'class' => 'form-control',
                                    ]
                                ),
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'label' => '商品类型',
                                'filter' => Html::activeDropDownList($searchModel, 'is_virtual', [
                                    '0' => '普通商品',
                                    '1' => '虚拟商品',
                                ], [
                                        'prompt' => '全部',
                                        'class' => 'form-control',
                                    ]
                                ),
                                'value' => function ($model) {
                                    return isset($model->virtualType->group)
                                        ? VirtualProductGroupEnum::getValue($model->virtualType->group)
                                        : '普通商品';
                                },
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
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
                                'template' => '{edit} {delete}',
                                'buttons' => [
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
            <!-- /.tab-content -->
        </div>
        <!-- /.nav-tabs-custom -->
    </div>
</div>

<script>
    let url = '';
    // 删除全部
    $(".delete-all").on("click", function () {
        url = "<?= Url::to(['delete-all'])?>";
        sendData(url);
    });

    // 上架
    $(".putaway-all").on("click", function () {
        url = "<?= Url::to(['state-all', 'state' => true])?>";
        sendData(url);
    });

    // 下架
    $(".sold-out-all").on("click", function () {
        url = "<?= Url::to(['state-all', 'state' => false])?>";
        sendData(url);
    });

    function sendData(url) {
        var ids = $("#grid").yiiGridView("getSelectedRows");
        $.ajax({
            type: "post",
            url: url,
            dataType: "json",
            data: {ids: ids},
            success: function (data) {
                if (parseInt(data.code) === 200) {
                    swal("操作成功", {
                        buttons: {
                            defeat: '确定',
                        },
                    }).then((value) => {
                        location.reload();
                    });
                } else {
                    rfWarning(data.message);
                }
            }
        });
    }
</script>