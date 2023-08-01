<?php

use yii\grid\GridView;
use common\helpers\Url;
use common\helpers\Html;
use common\enums\AuditStatusEnum;

$this->title = '商品服务';
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-sm-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li <?php if ($auditStatus == AuditStatusEnum::DISABLED){ ?>class="active"<?php } ?>><a href="<?= Url::to(['product-service-map/audit', 'auditStatus' => AuditStatusEnum::DISABLED]) ?>"> 待审核</a></li>
                <li <?php if ($auditStatus == AuditStatusEnum::ENABLED){ ?>class="active"<?php } ?>><a href="<?= Url::to(['product-service-map/audit', 'auditStatus' => AuditStatusEnum::ENABLED]) ?>"> 审核通过</a></li>
                <li <?php if ($auditStatus == AuditStatusEnum::DELETE){ ?>class="active"<?php } ?>><a href="<?= Url::to(['product-service-map/audit', 'auditStatus' => AuditStatusEnum::DELETE]) ?>"> 审核拒绝</a></li>
                <li><a href="<?= Url::to(['product-service/index']) ?>"> 商品服务</a></li>
                <li class="pull-right">
                    <?= Html::create(['ajax-edit'], '创建', [
                        'data-toggle' => 'modal',
                        'data-target' => '#ajaxModal',
                    ])?>
                </li>
            </ul>
            <div class="tab-content">
                <div class="active tab-pane">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        //重新定义分页样式
                        'tableOptions' => ['class' => 'table table-hover'],
                        'columns' => [
                            [
                                'class' => 'yii\grid\SerialColumn',
                            ],
                            'productService.name',
                            'merchant.title',
                            [
                                'label' => '创建时间',
                                'attribute' => 'created_at',
                                'filter' => false, //不显示搜索框
                                'format' => ['date', 'php:Y-m-d H:i:s'],
                            ],
                            [
                                'header' => "操作",
                                'class' => 'yii\grid\ActionColumn',
                                'template' => '{pass} {refuse} {delete}',
                                'buttons' => [
                                    'pass' => function ($url, $model, $key) {
                                        if ($model->audit_status == AuditStatusEnum::DISABLED) {
                                            return Html::a('通过', ['pass', 'id' => $model->id], [
                                                    'class' => 'blue'
                                                ]) . ' | ';
                                        }
                                    },
                                    'refuse' => function ($url, $model, $key) {
                                        if ($model->audit_status == AuditStatusEnum::DISABLED) {
                                            return Html::a('拒绝', ['refuse', 'id' => $model->id], [
                                                'class' => 'orange'
                                            ]) . ' | ';
                                        }
                                    },
                                    'delete' => function ($url, $model, $key) {
                                        return Html::a('删除', ['delete', 'id' => $model->id], [
                                            'class' => 'red'
                                        ]);
                                    },
                                ],
                            ],
                        ],
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>
