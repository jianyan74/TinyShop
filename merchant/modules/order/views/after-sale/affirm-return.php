<?php

use yii\widgets\ActiveForm;
use common\helpers\Url;
use common\helpers\Html;

$form = ActiveForm::begin([
    'enableAjaxValidation' => true,
    'validationUrl' => Url::to(['affirm-return', 'id' => $model['id']]),
    'fieldConfig' => [
        'template' => "<div class='row'><div class='col-sm-3 text-right'>{label}</div><div class='col-sm-9'>{input}\n{hint}\n{error}</div></div>",
    ]
]);

?>

<div class="modal-header">
    <h4 class="modal-title">确认退款</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
</div>
<div class="modal-body">
    <?= $form->field($model, 'refund_money')->textInput()->hint('最多可退款 ' . $maxRefundMoney . ' 元 (包含运费)'); ?>
    <?= $form->field($model, 'refund_pay_type')->dropDownList($refundTypes); ?>
    <?= $form->field($model, 'memo')->textarea(); ?>
    <div class="form-group">
        <div class="row">
            <div class="col-sm-3 text-right">
                <label class="control-label">买家申请退款金额</label>
            </div>
            <div class="col-sm-9">
                <?= $model['refund_apply_money'] ?> 元
                <div class="help-block"></div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <div class="col-sm-3 text-right">
                <label class="control-label">买家申请退货数量</label>
            </div>
            <div class="col-sm-9">
                <?= $model['number'] ?> 件
                <div class="help-block"></div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <div class="col-sm-3 text-right">
                <label class="control-label">买家实际付款金额</label>
            </div>
            <div class="col-sm-9">
                <?= $orderProduct['product_money'] ?> 元
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
    <button class="btn btn-primary" type="submit">保存</button>
</div>

<?php ActiveForm::end(); ?>
