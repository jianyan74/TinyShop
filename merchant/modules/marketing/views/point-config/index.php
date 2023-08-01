<?php

use yii\widgets\ActiveForm;
use common\enums\WhetherEnum;
use addons\TinyShop\common\enums\PointConfigDeductionTypeEnum;

$this->title = '积分抵现';
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-12 col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= $this->title; ?></h3>
            </div>
            <?php $form = ActiveForm::begin([
                'fieldConfig' => [
                    'template' => "<div class='row'><div class='col-sm-2 text-right'>{label}</div><div class='col-sm-10'>{input}\n{hint}\n{error}</div></div>",
                ]
            ]); ?>
            <div class="box-body">
                <div class="col-lg-12">
                    <?= $form->field($model, 'status')->radioList(WhetherEnum::getOpenMap()); ?>
                    <?= $form->field($model, 'convert_rate')->textInput(); ?>
                    <?= $form->field($model, 'min_order_money')->textInput(); ?>
                    <?= $form->field($model, 'deduction_type')->radioList(PointConfigDeductionTypeEnum::getMap()); ?>
                    <div id="maxDeductionMoney" class="hide">
                        <?= $form->field($model, 'max_deduction_money')->textInput(); ?>
                    </div>
                    <div id="maxDeductionRate" class="hide">
                        <?= $form->field($model, 'max_deduction_rate')->textInput(); ?>
                    </div>
                    <?= $form->field($model, 'explain')->textarea(); ?>
                </div>
            </div>
            <div class="box-footer text-center">
                <button class="btn btn-primary" type="submit">保存</button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        changeDeductionType();
    });

    $("input[name='PointConfig[deduction_type]']").click(function () {
        changeDeductionType();
    });

    function changeDeductionType() {
        var val = $("input[name='PointConfig[deduction_type]']:checked").val();
        $('#maxDeductionMoney').addClass('hide');
        $('#maxDeductionRate').addClass('hide');

        if (parseInt(val) === <?= PointConfigDeductionTypeEnum::MONEY?>) {
            $('#maxDeductionMoney').removeClass('hide');
        }

        if (parseInt(val) === <?= PointConfigDeductionTypeEnum::RATE?>) {
            $('#maxDeductionRate').removeClass('hide');
        }
    }
</script>
