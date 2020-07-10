<?php

use common\helpers\ImageHelper;
use addons\TinyShop\common\enums\RefundStatusEnum;
use addons\TinyShop\common\enums\RefundTypeEnum;

$this->title = '订单详情';
$this->params['breadcrumbs'][] = ['label' => '订单管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="ns-main">
    <div class="mod-table">
        <div class="step-region">
            <ul class="ui-step">
                <li class="<?= $model->refund_status > 0 ? 'ui-step-done' : '';?>  col-lg-4">
                    <div class="ui-step-title">买家申请退款</div>
                    <div class="ui-step-number">1</div>
                    <div class="ui-step-meta"><?= !empty($model->refund_time) ? Yii::$app->formatter->asDatetime($model->refund_time) : '';?></div>
                </li>
                <li class="<?= $model->refund_status > RefundStatusEnum::APPLY ? 'ui-step-done' : '';?>  col-lg-4">
                    <div class="ui-step-title">商家退款处理</div>
                    <div class="ui-step-number">2</div>
                    <div class="ui-step-meta"></div>
                </li>
                <li class="<?= $model->refund_status == RefundStatusEnum::CONSENT ? 'ui-step-done' : '';?>  col-lg-4">
                    <div class="ui-step-title">退款完成</div>
                    <div class="ui-step-number">3</div>
                    <div class="ui-step-meta"></div>
                </li>
            </ul>
        </div>
        <div class="step-region clearfix">
            <div class="info-region">
                <div class="info-div">退款信息</div>
                <div>
                    <div class="info-goods">
                        <div class="ui-centered-image">
                            <img src="<?= ImageHelper::default($model['product_picture']) ?>" width="45" height="45">
                        </div>
                        <div class="info-goods-content">
                            <div><?= $model['product_name'] ?> - <?= $model['sku_name'] ?> </div>
                        </div>
                    </div>
                    <div class="dashed-line"></div>
                </div>
                <table class="info-table">
                    <tbody>
                    <tr>
                        <th>退款方式：</th>
                        <td>
                            <span class="orange"><?= RefundTypeEnum::getValue($model['refund_type']) ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>申请退款：</th>
                        <td><span class="orange"><?= $model['refund_require_money'] ?></span>元</td>
                    </tr>
                    <tr>
                        <th>退还金额：</th>
                        <td><span class="orange"><?= $model['refund_balance_money'] ?></span>元</td>
                    </tr>
                    <tr>
                        <th>退款原因：</th>
                        <td><?= !empty($model['refund_reason']) ? $model['refund_reason'] : '无' ?></td>
                    </tr>
                    <tr>
                        <th>退款说明：</th>
                        <td><?= !empty($model['refund_explain']) ? $model['refund_explain'] : '无' ?></td>
                    </tr>
                    <?php if (!empty($model['refund_shipping_code'])){ ?>
                        <tr>
                            <th>退款物流单号：</th>
                            <td><?= $model['refund_shipping_code'] ?></td>
                        </tr>
                        <tr>
                            <th>退款物流公司：</th>
                            <td><?= $model['refund_shipping_company'] ?></td>
                        </tr>
                    <?php } ?>
                    <?php if (!empty($model['refund_evidence'])){ ?>
                        <tr>
                            <th>退款凭证：</th>
                            <td><?= ImageHelper::fancyBoxs($model['refund_evidence']) ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="state-region">
                <div style="padding: 0 0 30px 40px;" id="<?= $model['id'] ?>">
                    <div class="state-title"><span class="icon info">!</span>订单状态：<?= RefundStatusEnum::getValue($model['refund_status']) ?></div>
                </div>
                <div class="state-remind-region">
                    <div class="dashed-line"></div>
                    <div class="state-remind">
                        <div class="tixing">提醒：</div>
                        <ul>
                            <li>如果无法发货，请及时与买家联系并说明情况后进行退款；</li>
                            <li>买家申请退款后，须征得买家同意后再发货，否则买家有权拒收货物；</li>
                            <li>买家付款后超过7天仍未发货，将有权申请客服介入发起退款维权；</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="step-region safeguard-log">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>协商日志</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($orderRefund as $action) { ?>
                    <tr>
                        <td>
                            操作备注: <?= $action['action_member_name'] ?>
                            于 <?= Yii::$app->formatter->asDatetime($action['created_at']) ?>
                            【<?= $action['action'] ?>】
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>