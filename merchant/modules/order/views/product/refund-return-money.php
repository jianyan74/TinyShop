<?php

use yii\widgets\ActiveForm;
use common\helpers\Url;
use common\helpers\Html;

$form = ActiveForm::begin([
    'enableAjaxValidation' => true,
    'validationUrl' => Url::to(['refund-return-money', 'id' => $product['id']]),
    'fieldConfig' => [
        'template' => "<div class='col-sm-4 text-right'>{label}</div><div class='col-sm-8'>{input}\n{hint}\n{error}</div>",
    ]
]);
?>
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">关闭</span>
        </button>
        <h4 class="modal-title">基本信息</h4>
    </div>
    <div class="modal-body">
        <div class="form-group">
            <div class="col-sm-4 text-right">
                <label class="control-label">退款金额</label>
            </div>
            <div class="col-sm-8">
                <?= Html::textInput(Html::getInputName($product, 'refund_balance_money'), $product['product_money'], [
                    'class' => 'form-control',
                ]) ?>
                <div class="help-block">最多可退款 <?= $maxRefundMoney ?> 元 (包含运费)</div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-4 text-right">
                <label class="control-label">退款方式</label>
            </div>
            <div class="col-sm-8">
                <?= Html::dropDownList(Html::getInputName($product, 'refund_type'), $defaultRefundType, $refundTypes, [
                        'class' => 'form-control',
                ])?>
                <div class="help-block"></div>
            </div>
        </div>
        <?= $form->field($product, 'memo')->textarea(); ?>
        <div class="form-group hide">
            <div class="col-sm-4 text-right">
                <label class="control-label">买家申请退款金额</label>
            </div>
            <div class="col-sm-8">
                <?= $product['refund_require_money'] ?> 元
                <div class="help-block"></div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-4 text-right">
                <label class="control-label">买家实际付款金额</label>
            </div>
            <div class="col-sm-8">
                <?= $product['product_money'] ?> 元
                <div class="help-block"></div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
        <button class="btn btn-primary" type="submit">保存</button>
    </div>
<?php ActiveForm::end(); ?>