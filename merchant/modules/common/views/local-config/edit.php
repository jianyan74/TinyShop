<?php

use yii\widgets\ActiveForm;
use common\helpers\Url;
use unclead\multipleinput\MultipleInput;

$this->title = '配送费用';
$this->params['breadcrumbs'][] = ['label' => $this->title];

?>

<div class="row">
    <div class="col-sm-12">
        <div class="nav-tabs-custom">
            <?= $this->render('../common/_express_nav', [
                'type' => 'local-distribution',
            ]) ?>
            <div class="tab-content">
                <div class="tab-pane active">
                    <div class="tab-pane active">
                        <nav class="nav-tabs-child">
                            <ul>
                                <li class="selected"><a href="<?= Url::to(['local-config/edit']) ?>">配送时间及费用</a></li>
                                <li><a href="<?= Url::to(['local-area/edit']) ?>">同城配送地区</a></li>
                            </ul>
                        </nav>
                        <div class="box">
                            <?php $form = ActiveForm::begin([
                                'fieldConfig' => [
                                    'template' => "<div class='row'><div class='col-sm-2 text-right'>{label}</div><div class='col-sm-10'>{input}\n{hint}\n{error}</div></div>",
                                ],
                            ]); ?>
                            <!-- /.box-header -->
                            <div class="box-body table-responsive">
                                <div class="form-group">
                                    <?= $form->field($model, 'auto_order_receiving')->radioList(\common\enums\WhetherEnum::getMap()); ?>
                                    <?= $form->field($model, 'order_money')->textInput(); ?>
                                    <?= $form->field($model, 'freight')->textInput(); ?>
                                    <div class="form-group">
                                        <?= $form->field($model, 'make_day')->dropDownList(\common\helpers\ArrayHelper::numBetween(1, 15, true, 1, '天')); ?>
                                        <?= $form->field($model, 'interval_time')->dropDownList($section)->hint('用户只能选择多少时间后的上门时间'); ?>
                                        <?= $form->field($model, 'distribution_time')->widget(MultipleInput::class, [
                                            'iconSource' => 'fa',
                                            'min' => 1,
                                            'max' => 100,
                                            'columns' => [
                                                [
                                                    'name'  => 'start_time',
                                                    'title' => '开始时间',
                                                    'type'  => 'dropDownList',
                                                    'items' => $section
                                                ],
                                                [
                                                    'name'  => 'end_time',
                                                    'title' => '结束时间',
                                                    'type'  => 'dropDownList',
                                                    'items' => $section
                                                ],
                                            ]
                                        ]);
                                        ?>
                                        <div class="row">
                                            <div class="col-sm-2 text-right">
                                                <label class="control-label"></label>
                                            </div>
                                            <div class="col-sm-10">
                                                <div class="help-block">若起始时间与结束时间均未设置，则默认为该时间段不提供配送服务</div>
                                            </div>
                                        </div>
                                        <?= $form->field($model, 'shipping_fee')->widget(unclead\multipleinput\MultipleInput::class, [
                                            'iconSource' => 'fa',
                                            'max' => 20,
                                            'min' => 0,
                                            'columns' => [
                                                [
                                                    'name'  => 'order_money',
                                                    'title' => '消费金额',
                                                    'options' => [
                                                        'class' => 'input-priority'
                                                    ]
                                                ],
                                                [
                                                    'name'  => 'freight',
                                                    'title' => '配送费用',
                                                    'options' => [
                                                        'class' => 'input-priority'
                                                    ]
                                                ]
                                            ]
                                        ]);
                                        ?>
                                        <!-- /.box-body -->
                                    </div>
                                    <div class="box-footer text-center">
                                        <button class="btn btn-primary" type="submit">保存</button>
                                    </div>
                                    <?php ActiveForm::end(); ?>
                                    <!-- /.box -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
