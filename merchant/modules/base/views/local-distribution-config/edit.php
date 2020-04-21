<?php

use yii\widgets\ActiveForm;
use common\helpers\Url;

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
                        <nav class="goods-nav">
                            <ul>
                                <li><a href="<?= Url::to(['local-distribution-member/index']) ?>">配送人员</a></li>
                                <li class="selected"><a href="<?= Url::to(['local-distribution-config/edit']) ?>">配送费用</a></li>
                                <li><a href="<?= Url::to(['local-distribution-area/edit']) ?>">本地配送地区</a></li>
                            </ul>
                        </nav>
                        <div class="box">
                            <?php $form = ActiveForm::begin([
                                'fieldConfig' => [
                                    'template' => "<div class='col-sm-2 text-right'>{label}</div><div class='col-sm-10'>{input}\n{hint}\n{error}</div>",
                                ],
                            ]); ?>
                            <!-- /.box-header -->
                            <div class="box-body table-responsive">
                                <div class="form-group">
                                    <?= $form->field($model, 'order_money')->textInput(); ?>
                                    <?= $form->field($model, 'freight')->textInput(); ?>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="col-sm-2 text-right">
                                                    <label class="control-label">配送时间</label>
                                                </div>
                                                <div class="col-sm-5">
                                                    <?= $form->field($model, 'forenoon_start', [
                                                        'template' => "{label}{input}\n{hint}\n{error}",
                                                    ])->dropDownList($forenoon_start); ?>
                                                </div>
                                                <div class="col-sm-5">
                                                    <?= $form->field($model, 'forenoon_end', [
                                                        'template' => "{label}{input}\n{hint}\n{error}",
                                                    ])->dropDownList($forenoon_end); ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="col-sm-2 text-right">
                                                    <label class="control-label"></label>
                                                </div>
                                                <div class="col-sm-5">
                                                    <?= $form->field($model, 'afternoon_start', [
                                                        'template' => "{label}{input}\n{hint}\n{error}",
                                                    ])->dropDownList($afternoon_start); ?>
                                                </div>
                                                <div class="col-sm-5">
                                                    <?= $form->field($model, 'afternoon_end', [
                                                        'template' => "{label}{input}\n{hint}\n{error}",
                                                    ])->dropDownList($afternoon_end); ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="col-sm-2 text-right">
                                                    <label class="control-label"></label>
                                                </div>
                                                <div class="col-sm-10">
                                                    <div class="help-block">配送时间可单独设置上午或下午，若起始时间与结束时间均未设置，则默认为该时间段不提供配送服务</div>
                                                </div>
                                            </div>
                                        </div>
                                        <?= $form->field($model, 'discounts')->widget(unclead\multipleinput\MultipleInput::class, [
                                            'max' => 10,
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

