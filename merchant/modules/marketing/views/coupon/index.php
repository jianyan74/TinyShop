<?php
use common\helpers\Html;
use yii\grid\GridView;
use addons\TinyShop\common\models\marketing\Coupon;

$this->title = '优惠券详情';
$this->params['breadcrumbs'][] = ['label' => '优惠劵', 'url' => ['marketing-coupon-type/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= $this->title; ?></h3>
            </div>
            <div class="box-body table-responsive">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    //重新定义分页样式
                    'tableOptions' => ['class' => 'table table-hover'],
                    'columns' => [
                        [
                            'class' => 'yii\grid\SerialColumn',
                            'visible' => true, // 不显示#
                        ],
                        [
                            'attribute' => 'couponType.title',
                            'label'=> '优惠券名称',
                        ],
                        [
                            'attribute' => 'member.nickname',
                            'label'=> '领用人',
                            'filter' => Html::activeTextInput($searchModel, 'member.nickname', [
                                    'class' => 'form-control'
                                ]
                            ),
                        ],
                        'code',
                        [
                            'attribute' => 'money',
                            'filter' => false, //不显示搜索框
                            'value' => function ($model) {
                                if (in_array($model->state, [Coupon::STATE_UNSED, Coupon::STATE_GET])) {
                                    return $model->money;
                                } else {
                                    return $model->couponType->money;
                                }
                            },
                            'format' => 'raw',
                        ],
                        [
                            'attribute' => 'state',
                            'value' => function ($model) use ($stateExplain) {
                                return $stateExplain[$model->state] ?? '';
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'state', $stateExplain, [
                                    'prompt' => '全部',
                                    'class' => 'form-control'
                                ]
                            ),
                        ],
                        [
                            'attribute' => 'fetch_time',
                            'filter' => false, //不显示搜索框
                            'value' => function ($model) {
                                return $model->fetch_time ? Yii::$app->formatter->asDatetime($model->fetch_time) : '---';
                            },
                            'format' => 'raw',
                        ],
                        [
                            'attribute' => 'use_time',
                            'filter' => false, //不显示搜索框
                            'value' => function ($model) {
                                return $model->use_time ? Yii::$app->formatter->asDatetime($model->use_time) : '---';
                            },
                            'format' => 'raw',
                        ],
                    ],
                ]); ?>
            </div>
        </div>
    </div>
</div>

