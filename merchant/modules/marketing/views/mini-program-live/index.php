<?php

use common\helpers\Html;
use yii\grid\GridView;
use common\helpers\Url;
use common\helpers\ImageHelper;
use common\enums\WhetherEnum;
use common\enums\MiniProgramLiveStatusEnum;

$this->title = '微信小程序直播';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= $this->title; ?></h3>
                <div class="box-tools">
                    <span class="btn btn-primary btn-xs sync">同步房间</span>
                </div>
            </div>
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
                        'id' => 'grid',
                    ],
                    'columns' => [
                        'room_id',
                        'name',
                        'anchor_name',
                        [
                            'label' => '直播间背景墙',
                            'filter' => false, //不显示搜索框
                            'value' => function ($model) {
                                if (!empty($model->cover)) {
                                    return ImageHelper::fancyBox($model->cover);
                                }
                            },
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'label' => '分享卡片',
                            'filter' => false, //不显示搜索框
                            'value' => function ($model) {
                                if (!empty($model->share_img)) {
                                    return ImageHelper::fancyBox($model->share_img);
                                }
                            },
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'label' => '直播状态(仅供参考)',
                            'format' => 'raw',
                            'value' => function ($model) {
                                if ($model->live_status > MiniProgramLiveStatusEnum::END) {
                                    return MiniProgramLiveStatusEnum::getValue($model->live_status);
                                }

                                return  '正常';
                            },
                        ],
                        [
                            'label' => '直播时间',
                            'format' => 'raw',
                            'value' => function ($model) {
                                $html = '';
                                $html .= '开始时间：' . Yii::$app->formatter->asDatetime($model->start_time) . "<br>";
                                $html .= '结束时间：' . Yii::$app->formatter->asDatetime($model->end_time) . "<br>";
                                $html .= '有效状态：' . Html::timeStatus($model->start_time, $model->end_time);

                                return $html;
                            },
                        ],
                        [
                            'label' => '推荐?',
                            'format' => 'raw',
                            'filter' => Html::activeDropDownList($searchModel, 'is_recommend', WhetherEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                ]
                            ),
                            'value' => function ($model) {
                                return WhetherEnum::getValue($model->is_recommend);
                            },
                        ],
                        [
                            'label' => '置顶?',
                            'format' => 'raw',
                            'filter' => Html::activeDropDownList($searchModel, 'is_stick', WhetherEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                ]
                            ),
                            'value' => function ($model) {
                                return WhetherEnum::getValue($model->is_stick);
                            },
                        ],
                        [
                            'header' => "操作",
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{edit} {status} {delete}',
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
                                    return Html::delete(['destroy', 'id' => $model->id]);
                                },
                            ],
                        ],
                    ],
                ]); ?>
            </div>
        </div>
    </div>
</div>

<script>
    // 获取资源
    $(".sync").click(function () {
        rfAffirm('同步中,请不要关闭当前页面');
        sync();
    });

    // 正式同步
    function sync(offset = 0, count = 10, clear = 1) {
        $.ajax({
            type: "get",
            url: "<?= Url::to(['sync'])?>",
            dataType: "json",
            data: {offset: offset, count: count, clear:clear},
            success: function (data) {
                if (data.code == 200) {
                    var data = data.data;
                    sync(data.offset, data.count, 0);
                } else if (data.code == 201) {
                    rfAffirm(data.message);
                    window.location.reload();
                } else {
                    rfAffirm(data.message);
                }
            }
        });
    }
</script>
