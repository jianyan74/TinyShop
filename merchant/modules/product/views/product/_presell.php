<?php

use addons\TinyShop\common\enums\PresellDeliveryTypeEnum;
use common\enums\WhetherEnum;
use kartik\datetime\DateTimePicker;

?>

<?= $form->field($model, 'is_open_presell')->radioList(WhetherEnum::getMap()); ?>
<?= $form->field($model, 'presell_price')->textInput(); ?>
<?= $form->field($model, 'presell_delivery_type')->radioList(PresellDeliveryTypeEnum::getMap()); ?>
<div class="presell-day <?php if ($model->presell_delivery_type == 1) {echo 'hide';} ?>">
    <?= $form->field($model, 'presell_day')->textInput(); ?>
</div>
<div class="presell-time <?php if ($model->presell_delivery_type != 1) {echo 'hide';} ?>">
    <?= $form->field($model, 'presell_time')->widget(DateTimePicker::class, [
        'language' => 'zh-CN',
        'options' => [
            'value' => $model->isNewRecord ? date('Y-m-d H:i') : date('Y-m-d H:i', $model->presell_time),
        ],
        'pluginOptions' => [
            'format' => 'yyyy-mm-dd hh:ii',
            'todayHighlight' => true,//今日高亮
            'autoclose' => true,//选择后自动关闭
            'todayBtn' => true,//今日按钮显示
        ],
    ]); ?>
</div>
<script>
    // 预售
    $("input[name='ProductForm[presell_delivery_type]']").click(function () {
        var val = $(this).val();
        if (val == '1') {
            $('.presell-time').removeClass('hide');
            $('.presell-day').addClass('hide');
        } else {
            $('.presell-day').removeClass('hide');
            $('.presell-time').addClass('hide');
        }
    });
</script>