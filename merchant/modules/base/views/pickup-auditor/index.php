<?php

use yii\grid\GridView;
use common\helpers\Url;
use common\helpers\Html;

$this->title = '门店自提';
$this->params['breadcrumbs'][] = ['label' => $this->title];
?>

<div class="row">
    <div class="col-sm-12">
        <div class="nav-tabs-custom">
            <?= $this->render('../common/_express_nav', [
                'type' => 'point',
            ]) ?>
            <div class="tab-content">
                <div class="tab-pane active">
                    <nav class="goods-nav">
                        <ul>
                            <li><a href="<?= Url::to(['pickup-point/index']) ?>">门店管理</a></li>
                            <li><a href="<?= Url::to(['pickup-point/config']) ?>">门店运费</a></li>
                            <li class="selected"><a href="<?= Url::to(['pickup-auditor/index']) ?>">门店审核人员</a></li>
                        </ul>
                    </nav>
                    <div class="box">
                        <div class="box-header" style="padding-bottom: 0">
                            <?= Html::a('批量删除</a>', "javascript:void(0);", ['class' => 'btn btn-white btn-sm delete-all']); ?>
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
                                        'class' => 'yii\grid\CheckboxColumn',
                                        'checkboxOptions' => function ($model, $key, $index, $column) {
                                            return ['value' => $model->id];
                                        }
                                    ],
                                    'member.nickname',
                                    'member.mobile',
                                    [
                                        'attribute' => 'pickupPoint.name',
                                        'filter' => Html::activeDropDownList($searchModel, 'pickup_point_id', $pickupPoints, [
                                                'prompt' => '全部',
                                                'class' => 'form-control'
                                            ]
                                        ),
                                        'format' => 'raw',
                                    ],
                                    [
                                        'label' => '创建时间',
                                        'attribute' => 'created_at',
                                        'filter' => false, //不显示搜索框
                                        'format' => ['date', 'php:Y-m-d H:i'],
                                    ],
                                    // 'updated_at',
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
        </div>
    </div>
</div>

<script>
    let url = '';
    // 删除全部
    $(".delete-all").on("click", function () {
        url = "<?= Url::to(['delete-all'])?>";
        sendData(url);
    });

    function sendData(url) {
        var ids = $("#grid").yiiGridView("getSelectedRows");
        $.ajax({
            type : "post",
            url : url,
            dataType : "json",
            data : {ids: ids},
            success: function(data){
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
