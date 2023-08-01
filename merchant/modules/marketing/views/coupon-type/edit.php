<?php

use yii\widgets\ActiveForm;
use common\enums\WhetherEnum;
use common\enums\StatusEnum;
use common\helpers\Url;
use common\helpers\StringHelper;
use common\widgets\cascader\Cascader;
use kartik\datetime\DateTimePicker;
use addons\TinyShop\common\enums\RangeTypeEnum;
use addons\TinyShop\common\enums\DiscountTypeEnum;
use addons\TinyShop\common\enums\TermOfValidityTypeEnum;

$this->title = $model->isNewRecord ? '创建' : '编辑';
$this->params['breadcrumbs'][] = ['label' => '优惠劵', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-12 col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">基本信息</h3>
            </div>
            <?php $form = ActiveForm::begin([
                'id' => 'form',
                'fieldConfig' => [
                    'template' => "<div class='row'><div class='col-sm-2 text-right'>{label}</div><div class='col-sm-10'>{input}\n{hint}\n{error}</div></div>",
                ],
            ]); ?>
            <div class="box-body">
                <div class="col-lg-12">
                    <?= $form->field($model, 'title')->textInput(); ?>
                    <?= $form->field($model, 'remark')->textInput(); ?>
                    <?= $form->field($model, 'at_least')->textInput(); ?>
                    <?= $form->field($model, 'discount_type')->radioList(DiscountTypeEnum::getMap()); ?>
                    <div id="money" class="hide">
                        <?= $form->field($model, 'money')->textInput(); ?>
                    </div>
                    <div id="discount" class="hide">
                        <?= $form->field($model, 'discount')->textInput()->hint('范围为 0 - 9.9。设置 0 为全部抵扣, 设置 9.9 为优惠百分之一'); ?>
                    </div>
                    <?php if ($model->isNewRecord) { ?>
                        <?= $form->field($model, 'count')->textInput(); ?>
                    <?php } else { ?>
                        <?= $form->field($model, 'defaultCount')->textInput([
                            'value' => $model->count,
                            'readonly' => 'readonly',
                        ]); ?>
                        <?= $form->field($model, 'replenishmentNum')->textInput(); ?>
                    <?php } ?>
                    <div class="row">
                        <div class="col-sm-2"></div>
                        <div class="col-sm-5">
                            <?= $form->field($model, 'max_day_fetch', [
                                'template' => "{label}{input}\n{hint}\n{error}",
                            ])->textInput()->hint('输入0表示无限制'); ?>
                        </div>
                        <div class="col-sm-5">
                            <?= $form->field($model, 'max_fetch', [
                                'template' => "{label}{input}\n{hint}\n{error}",
                            ])->textInput()->hint('输入0表示无限制'); ?>
                        </div>
                    </div>
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
                    <?= $form->field($model, 'term_of_validity_type')->radioList(TermOfValidityTypeEnum::getMap()); ?>
                    <div id="fixed_term" class="hide">
                        <?= $form->field($model, 'fixed_term')->textInput(); ?>
                    </div>
                    <div class="row hide" id="time">
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
                    <?= $form->field($model, 'range_type')->radioList(RangeTypeEnum::getFullMap()); ?>
                    <div id="productIds" class="hide">
                        <?= $form->field($model, 'products')->widget(\addons\TinyShop\common\widgets\product\Product::class, [
                            'box_id' => 'coupon',
                            'setting_sku' => false, // 不设置规格
                        ]); ?>
                    </div>
                    <div id="cateIds" class="hide">
                        <?= $form->field($model, 'cateIds')->widget(Cascader::class, [
                            'data' => $cates,
                            'multiple' => true,
                            'changeOnSelect' => true,
                        ]); ?>
                    </div>
                    <?= $form->field($model, 'is_list_visible')->radioList(WhetherEnum::getMap()); ?>
                    <?= $form->field($model, 'is_new_people')->radioList(WhetherEnum::getMap())->hint('未下单支付用户可领'); ?>
                    <?= $form->field($model, 'single_type')->radioList(WhetherEnum::getMap())->hint('"是" 取符合商品中最高价的一个进行抵扣, "否" 则是符合的商品累加价格进行抵扣'); ?>
                    <?= $form->field($model, 'status')->radioList(StatusEnum::getMap()); ?>
                </div>
            </div>
            <div class="box-footer text-center">
                <span class="btn btn-primary" onclick="beforeSubmit()">保存</span>
                <span class="btn btn-white" onclick="history.go(-1)">返回</span>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        changeRangeType();
        changeTermOfValidityType();
        changeDiscountType();
    });

    function beforeSubmit() {
        // 序列化数据
        var data = $('#form').serializeArray();
        data.push({
            'name': 'CouponTypeForm[products]',
            'value': JSON.stringify(coupon.products)
        })

        $.ajax({
            type: "post",
            url: "<?= Url::to(['edit', 'id' => $model->id]); ?>",
            dataType: "json",
            data: data,
            success: function (data) {
                submitStatus = true;
                if (parseInt(data.code) === 200) {
                    swal("保存成功", "小手一抖就打开了一个框", "success").then((value) => {
                        window.location = "<?= $referrer; ?>";
                    });
                } else {
                    rfMsg(data.message);
                }
            }
        });
    }

    $("input[name='CouponTypeForm[range_type]']").click(function () {
        changeRangeType();
    });


    $("input[name='CouponTypeForm[term_of_validity_type]']").click(function () {
        changeTermOfValidityType();
    });

    $("input[name='CouponTypeForm[discount_type]']").click(function () {
        changeDiscountType();
    })

    function changeRangeType() {
        var val = $("input[name='CouponTypeForm[range_type]']:checked").val();
        if (parseInt(val) === 1) {
            $('#productIds').addClass('hide');
            $('#cateIds').addClass('hide');
        } else if (parseInt(val) === 2 || parseInt(val) === 3) {
            $('#productIds').removeClass('hide');
            $('#cateIds').addClass('hide');
        } else {
            // 分类
            $('#productIds').addClass('hide');
            $('#cateIds').removeClass('hide');
        }
    }

    function changeTermOfValidityType() {
        var val = $("input[name='CouponTypeForm[term_of_validity_type]']:checked").val();
        if (parseInt(val) === 1) {
            $('#time').addClass('hide');
            $('#fixed_term').removeClass('hide');
        } else {
            $('#time').removeClass('hide');
            $('#fixed_term').addClass('hide');
        }
    }

    function changeDiscountType() {
        var val = $("input[name='CouponTypeForm[discount_type]']:checked").val();
        if (parseInt(val) === 2) {
            $('#money').addClass('hide');
            $('#discount').removeClass('hide');
        } else {
            $('#money').removeClass('hide');
            $('#discount').addClass('hide');
        }
    }
</script>
