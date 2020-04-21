<?php

use yii\widgets\ActiveForm;
use common\widgets\webuploader\Files;
use common\enums\WhetherEnum;

$this->title = '参数设置';
$this->params['breadcrumbs'][] = ['label' => $this->title];
?>

<?php $form = ActiveForm::begin([]); ?>
<div class="row">
    <div class="col-sm-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#tab-1" aria-expanded="false">购物设置</a></li>
                <li><a data-toggle="tab" href="#tab-2" aria-expanded="false">支付设置</a></li>
                <li><a data-toggle="tab" href="#tab-3" aria-expanded="false">配送设置</a></li>
                <li><a data-toggle="tab" href="#tab-4" aria-expanded="false">发票设置</a></li>
                <li><a data-toggle="tab" href="#tab-6" aria-expanded="false">相关协议</a></li>
                <li><a data-toggle="tab" href="#tab-7" aria-expanded="false">版权</a></li>
            </ul>
            <div class="tab-content">
                <div id="tab-1" class="tab-pane active">
                    <div class="panel-body">
                        <?= $form->field($model, 'order_auto_delinery')->textInput(); ?>
                        <?= $form->field($model, 'order_buy_close_time')->textInput(); ?>
                        <?= $form->field($model, 'order_delivery_complete_time')->dropDownList([
                            0 => '立即',
                            1 => '1天',
                            2 => '2天',
                            3 => '3天',
                            4 => '4天',
                            5 => '5天',
                            6 => '6天',
                            7 => '7天',
                        ]); ?>
                        <?= $form->field($model, 'shopping_back_points')->dropDownList([
                            1 => '订单已完成',
                            2 => '已收货',
                            3 => '支付完成',
                        ]); ?>
                        <?= $form->field($model, 'evaluate_day')->textInput(); ?>
                        <?= $form->field($model, 'evaluate')->textarea(); ?>
                        <?= $form->field($model, 'after_sale_date')->textInput(); ?>
                    </div>
                </div>
                <div id="tab-2" class="tab-pane">
                    <div class="panel-body">
                        <?= $form->field($model, 'order_wechat_pay')->radioList(WhetherEnum::getMap()); ?>
                        <?= $form->field($model, 'order_ali_pay')->radioList(WhetherEnum::getMap()); ?>
                        <?= $form->field($model, 'order_balance_pay')->radioList(WhetherEnum::getMap()); ?>
                        <?= $form->field($model, 'order_cash_against_pay')->radioList(WhetherEnum::getMap()); ?>
                    </div>
                </div>
                <div id="tab-3" class="tab-pane">
                    <div class="panel-body">
                        <?= $form->field($model, 'buyer_self_lifting')->radioList(WhetherEnum::getMap()); ?>
                        <?= $form->field($model, 'is_logistics')->radioList(WhetherEnum::getMap()); ?>
                        <?= $form->field($model, 'is_delivery_shop')->radioList(WhetherEnum::getMap()); ?>
                    </div>
                </div>
                <div id="tab-4" class="tab-pane">
                    <div class="panel-body">
                        <?= $form->field($model, 'order_invoice_tax')->textInput(); ?>
                        <?= $form->field($model, 'order_invoice_content')->textarea(); ?>
                    </div>
                </div>
                <div id="tab-6" class="tab-pane">
                    <div class="panel-body">
                        <?= $form->field($model, 'protocol_register')->widget(\common\widgets\ueditor\UEditor::class); ?>
                        <?= $form->field($model, 'protocol_privacy')->widget(\common\widgets\ueditor\UEditor::class); ?>
                        <?= $form->field($model, 'protocol_recharge')->widget(\common\widgets\ueditor\UEditor::class); ?>
                    </div>
                </div>
                <div id="tab-7" class="tab-pane">
                    <div class="panel-body">
                        <?= $form->field($model, 'copyright_logo')->widget(Files::class, [
                            'type' => 'images',
                            'theme' => 'default',
                            'themeConfig' => [],
                            'config' => [
                                'pick' => [
                                    'multiple' => false,
                                ],
                            ],
                        ]); ?>
                        <?= $form->field($model, 'copyright_companyname')->textInput(); ?>
                        <?= $form->field($model, 'copyright_url')->textInput(); ?>
                        <?= $form->field($model, 'copyright_desc')->textarea(); ?>
                    </div>
                </div>
                <div class="box-footer text-center">
                    <button class="btn btn-primary" type="submit">保存</button>
                    <span class="btn btn-white" onclick="history.go(-1)">返回</span>
                </div>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
