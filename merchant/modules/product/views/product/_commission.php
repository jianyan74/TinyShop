<?= $form->field($commissionRate, 'status')->radioList([0 => '关闭', 1 => '开启']); ?>
<?= $form->field($commissionRate, 'distribution_commission_rate')->textInput()->hint('和区域代理佣金比率相加不能超过100'); ?>
<?= $form->field($commissionRate, 'regionagent_commission_rate')->textInput()->hint('和分销佣金比率相加不能超过100'); ?>