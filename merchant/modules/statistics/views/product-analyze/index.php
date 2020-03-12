<?php

use common\helpers\Html;
use yii\grid\GridView;
use common\helpers\Url;

$this->title = '商品分析';
$this->params['breadcrumbs'][] = ['label' => '数据统计'];
$this->params['breadcrumbs'][] = ['label' => $this->title];
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
                    'options' => [
                        'id' => 'grid'
                    ],
                    'columns' => [
                        [
                            'class' => 'yii\grid\SerialColumn',
                            'visible' => true, // 不显示#
                        ],
                        'name',
                        [
                            'attribute' => 'price',
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
                        ],
                        [
                            'label'=> '近30天下单商品数',
                            'attribute' => 'stat_num',
                        ],
                        [
                            'label'=> '近30天下单金额',
                            'attribute' => 'stat_money',
                        ],

                    ],
                ]); ?>
            </div>
        </div>
    </div>
</div>