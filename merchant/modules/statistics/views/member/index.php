<?php
use yii\widgets\LinkPager;
use common\helpers\Url;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;
use yii\widgets\ActiveForm;

$addon = <<< HTML
<span class="input-group-addon">
    <i class="glyphicon glyphicon-calendar"></i>
</span>
HTML;

$memberActive = \addons\TinyShop\common\enums\MemberActiveEnum::getMap();

$this->title = '用户分析';
$this->params['breadcrumbs'][] = ['label' => $this->title];
?>

<div class="row">
    <div class="col-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="<?= Url::to(['index']) ?>"> 用户分析</a></li>
                <?php foreach ($memberActive as $key => $value){ ?>
                    <li><a href="<?= Url::to(['active', 'type' => $key]) ?>"> <?= $value; ?></a></li>
                <?php } ?>
            </ul>
            <div class="tab-content">
                <div class="active tab-pane">
                    <?php $form = ActiveForm::begin([
                        'action' => Url::to(['index']),
                        'method' => 'get',
                    ]); ?>
                    <div class="col-3 m-b">
                        <div class="input-group drp-container">
                            <?= DateRangePicker::widget([
                                'name' => 'queryDate',
                                'value' => $start_time . '-' . $end_time,
                                'readonly' => 'readonly',
                                'useWithAddon' => true,
                                'convertFormat' => true,
                                'startAttribute' => 'start_time',
                                'endAttribute' => 'end_time',
                                'startInputOptions' => ['value' => $start_time],
                                'endInputOptions' => ['value' => $end_time],
                                'pluginOptions' => [
                                    'locale' => ['format' => 'Y-m-d'],
                                ]
                            ]) . $addon;?>
                            <span class="input-group-btn"><button class="btn btn-white"><i class="fa fa-search"></i> 搜索</button></span>
                        </div>
                    </div>
                    <?php ActiveForm::end(); ?>
                    <div class="row">
                        <div class="col-12">
                            <?= GridView::widget([
                                'dataProvider' => $dataProvider,
                                'filterModel' => $searchModel,
                                //重新定义分页样式
                                'tableOptions' => ['class' => 'table table-hover'],
                                'columns' => [
                                    [
                                        'class' => 'yii\grid\SerialColumn',
                                        'visible' => false, // 不显示#
                                    ],
                                    [
                                        'attribute' => 'buyer_id',
                                        'label' => '用户ID',
                                        'headerOptions' => ['class' => 'col-md-1'],
                                    ],
                                    'member.nickname',
                                    'member.mobile',
                                    [
                                        'attribute' => 'product_count',
                                        'label' => '订单量',
                                    ],
                                    [
                                        'attribute' => 'pay_money',
                                        'label' => '支付金额',
                                    ],
                                    [
                                        'attribute' => 'refund_money',
                                        'label' => '退款金额',
                                    ],
                                ],
                            ]); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
