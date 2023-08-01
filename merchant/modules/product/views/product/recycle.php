<?php

use yii\grid\GridView;
use common\helpers\Html;
use common\helpers\ImageHelper;
use common\helpers\Url;

$this->title = '商品管理';
$this->params['breadcrumbs'][] = ['label' => $this->title];

?>

<div class="row">
    <div class="col-12 col-sm-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li><a href="<?= Url::to(['index', 'status' => 1]) ?>">出售中</a></li>
                <li><a href="<?= Url::to(['index', 'status' => 0]) ?>">已下架</a></li>
                <li class="<?= Yii::$app->services->devPattern->isB2B2C() ? '' : 'hide'; ?>"><a href="<?= Url::to(['index', 'audit_status' => 0]) ?>">审核中</a></li>
                <li class="<?= Yii::$app->services->devPattern->isB2B2C() ? '' : 'hide'; ?>"><a href="<?= Url::to(['index', 'audit_status' => -1]) ?>">审核失败</a></li>
                <li><a href="<?= Url::to(['index', 'stock_warning' => 1]) ?>">库存报警</a></li>
                <li><a href="<?= Url::to(['index', 'sell_out' => 1]) ?>">已售罄</a></li>
                <li class="active"><a href="<?= Url::to(['recycle']) ?>">回收站</a></li>
            </ul>
            <div class="tab-content">
                <div class="col-sm-12 m-b-sm m-l">
                    <?= Html::a('批量彻底删除</a>', "javascript:void(0);", ['class' => 'btn btn-white btn-sm m-l-n-md delete-all']); ?>
                    <?= Html::a('批量恢复</a>', "javascript:void(0);", ['class' => 'btn btn-white btn-sm restore-all']); ?>
                </div>
                <div class="active tab-pane">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        //重新定义分页样式
                        'tableOptions' => ['class' => 'table table-hover'],
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
                            'name',
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
                                'label' => '商品分类',
                                'filter' => Html::activeDropDownList($searchModel, 'cate_id', $cates, [
                                        'prompt' => '全部',
                                        'class' => 'form-control',
                                    ]
                                ),
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
                                'template' => '{restore} {delete}',
                                'buttons' => [
                                    'restore' => function ($url, $model, $key) {
                                        return Html::edit(['restore', 'id' => $model['id']], '还原');
                                    },
                                    'delete' => function ($url, $model, $key) {
                                        return Html::delete(['delete', 'id' => $model->id], '彻底删除');
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

    // 回复全部
    $(".restore-all").on("click", function () {
        url = "<?= Url::to(['restore-all'])?>";
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
                    swal('小手一抖打开一个窗', {
                        buttons: {
                            defeat: '确定',
                        },
                        title: '操作成功',
                    }).then(function (value) {
                        switch (value) {
                            case "defeat":
                                location.reload();
                                break;
                            default:
                        }
                    });
                } else {
                    rfWarning(data.message);
                }
            }
        });
    }
</script>
