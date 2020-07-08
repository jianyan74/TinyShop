<?php

use yii\grid\GridView;
use common\helpers\Html;
use common\helpers\ImageHelper;
use common\helpers\Url;

$this->title = '商品管理';
$this->params['breadcrumbs'][] = ['label' => $this->title];
?>

<div class="row">
    <div class="col-sm-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li><a href="<?= Url::to(['index', 'product_status' => 1]) ?>">出售中</a></li>
                <li><a href="<?= Url::to(['index', 'product_status' => 0]) ?>">已下架</a></li>
                <li><a href="<?= Url::to(['index', 'stock_warning' => 1]) ?>">库存报警</a></li>
                <li class="active"><a href="<?= Url::to(['recycle']) ?>">回收站</a></li>
            </ul>
            <div class="tab-content">
                <div class="col-sm-12 m-b-sm">
                    <?= Html::a('批量删除</a>', "javascript:void(0);",
                        ['class' => 'btn btn-white btn-sm m-l-n-md destroy-all']); ?>
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
                                'attribute' => 'covers',
                                'label' => '主图',
                                'filter' => false, //不显示搜索框
                                'value' => function ($model) {
                                    $covers = unserialize($model->covers);
                                    if (!empty($covers)) {
                                        return ImageHelper::fancyBox($covers[0]);
                                    }
                                },
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            'name',
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
                                        return Html::edit(['restore', 'id' => $model['id']], '还原');
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

<script>
    let url = '';
    // 删除全部
    $(".destroy-all").on("click", function () {
        url = "<?= Url::to(['destroy-all'])?>";
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
