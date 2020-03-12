<?php

use yii\widgets\ActiveForm;
use common\enums\WhetherEnum;
use common\enums\StatusEnum;
use common\helpers\StringHelper;
use kartik\datetime\DateTimePicker;
use addons\TinyShop\common\enums\RangeTypeEnum;
use addons\TinyShop\common\enums\PreferentialTypeEnum;
use addons\TinyShop\common\models\marketing\CouponType;

$this->title = $model->isNewRecord ? '创建' : '编辑';
$this->params['breadcrumbs'][] = ['label' => '优惠劵', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">基本信息</h3>
            </div>
            <?php $form = ActiveForm::begin([
                'fieldConfig' => [
                    'template' => "<div class='col-sm-2 text-right'>{label}</div><div class='col-sm-10'>{input}\n{hint}\n{error}</div>",
                ],
            ]); ?>
            <div class="box-body">
                <div class="col-lg-12">
                    <?= $form->field($model, 'title')->textInput(); ?>
                    <?= $form->field($model, 'type')->radioList(PreferentialTypeEnum::getMap()); ?>
                    <?= $form->field($model, 'at_least')->textInput(); ?>
                    <div id="money" class="<?= $model->type == PreferentialTypeEnum::DISCOUNT ? 'hide' : ''; ?>">
                        <?= $form->field($model, 'money')->textInput(); ?>
                    </div>
                    <div id="discount" class="<?= $model->type == PreferentialTypeEnum::MONEY ? 'hide' : ''; ?>">
                        <?= $form->field($model, 'discount')->textInput()->hint('百分比，范围(1-100)'); ?>
                    </div>
                    <?php if ($model->isNewRecord) { ?>
                        <?= $form->field($model, 'count')->textInput(); ?>
                    <?php } else { ?>
                        <?= $form->field($model, 'defaultCount')->textInput([
                            'value' => $model->count,
                            'readonly' => 'readonly',
                        ]); ?>
                        <?= $form->field($model, 'reissuenNum')->textInput(); ?>
                    <?php } ?>
                    <?= $form->field($model, 'max_fetch')->textInput()->hint('输入0表示无限制'); ?>
                    <div class="row">
                        <div class="col-sm-2"></div>
                        <div class="col-sm-5">
                            <?= $form->field($model, 'get_start_time', [
                                'template' => "{label}{input}\n{hint}\n{error}",
                            ])->widget(DateTimePicker::class, [
                                'language' => 'zh-CN',
                                'options' => [
                                    'value' => StringHelper::intToDate($model->get_start_time),
                                ],
                                'pluginOptions' => [
                                    'format' => 'yyyy-mm-dd hh:ii',
                                    'todayHighlight' => true,//今日高亮
                                    'autoclose' => true,//选择后自动关闭
                                    'todayBtn' => true,//今日按钮显示
                                ],
                            ]); ?>
                        </div>
                        <div class="col-sm-5">
                            <?= $form->field($model, 'get_end_time', [
                                'template' => "{label}{input}\n{hint}\n{error}",
                            ])->widget(DateTimePicker::class, [
                                'language' => 'zh-CN',
                                'options' => [
                                    'value' => StringHelper::intToDate($model->get_end_time),
                                ],
                                'pluginOptions' => [
                                    'format' => 'yyyy-mm-dd hh:ii',
                                    'todayHighlight' => true,//今日高亮
                                    'autoclose' => true,//选择后自动关闭
                                    'todayBtn' => true,//今日按钮显示
                                ],
                            ]); ?>
                        </div>
                    </div>
                    <?= $form->field($model, 'term_of_validity_type')->radioList(CouponType::$termOfValidityTypeExplain); ?>
                    <div id="fixed_term" class="<?= $model->term_of_validity_type == 0 ? 'hide' : ''; ?>">
                        <?= $form->field($model, 'fixed_term')->textInput(); ?>
                    </div>
                    <div id="time" class="<?= $model->term_of_validity_type == 1 ? 'hide' : ''; ?>">

                        <div class="row">
                            <div class="col-sm-2"></div>
                            <div class="col-sm-5">
                                <?= $form->field($model, 'start_time', [
                                    'template' => "{label}{input}\n{hint}\n{error}",
                                ])->widget(DateTimePicker::class, [
                                    'language' => 'zh-CN',
                                    'options' => [
                                        'value' => StringHelper::intToDate($model->start_time),
                                    ],
                                    'pluginOptions' => [
                                        'format' => 'yyyy-mm-dd hh:ii',
                                        'todayHighlight' => true,//今日高亮
                                        'autoclose' => true,//选择后自动关闭
                                        'todayBtn' => true,//今日按钮显示
                                    ],
                                ]); ?>
                            </div>
                            <div class="col-sm-5">
                                <?= $form->field($model, 'end_time', [
                                    'template' => "{label}{input}\n{hint}\n{error}",
                                ])->widget(DateTimePicker::class, [
                                    'language' => 'zh-CN',
                                    'options' => [
                                        'value' => StringHelper::intToDate($model->end_time),
                                    ],
                                    'pluginOptions' => [
                                        'format' => 'yyyy-mm-dd hh:ii',
                                        'todayHighlight' => true,//今日高亮
                                        'autoclose' => true,//选择后自动关闭
                                        'todayBtn' => true,//今日按钮显示
                                    ],
                                ]); ?>
                            </div>
                        </div>
                    </div>
                    <?= $form->field($model, 'is_show')->radioList(WhetherEnum::getMap()); ?>
                    <?= $form->field($model, 'status')->radioList(StatusEnum::getMap()); ?>
                    <?= $form->field($model, 'range_type')->radioList(RangeTypeEnum::getMap()); ?>
                    <div class="<?= $model->range_type == 1 ? 'hide' : ''; ?>" id="productIds">
                        <?= $form->field($model, 'product_ids')->widget(\addons\TinyShop\common\widgets\product\ProductSelect::class)->label(false); ?>
                    </div>
                </div>
            </div>
            <div class="box-footer text-center">
                <button class="btn btn-primary" type="submit">保存</button>
                <span class="btn btn-white" onclick="history.go(-1)">返回</span>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<script>
    $("input[name='CouponTypeForm[range_type]']").click(function () {
        var val = $(this).val();
        if (parseInt(val) === 1) {
            $('#productIds').addClass('hide');
        } else {
            $('#productIds').removeClass('hide');
        }
    });

    $("input[name='CouponTypeForm[term_of_validity_type]']").click(function () {
        var val = $(this).val();
        if (parseInt(val) === 1) {
            $('#time').addClass('hide');
            $('#fixed_term').removeClass('hide');
        } else {
            $('#time').removeClass('hide');
            $('#fixed_term').addClass('hide');
        }
    });

    $("input[name='CouponTypeForm[type]']").click(function () {
        var val = $(this).val();
        if (parseInt(val) === 2) {
            $('#money').addClass('hide');
            $('#discount').removeClass('hide');
        } else {
            $('#money').removeClass('hide');
            $('#discount').addClass('hide');
        }
    })
</script>