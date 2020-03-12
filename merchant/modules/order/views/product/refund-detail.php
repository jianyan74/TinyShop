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
                        <th>实际退款：</th>
                        <td><span class="orange"><?= $model['refund_real_money'] ?></span>元</td>
                    </tr>
                    <tr>
                        <th>退还余额：</th>
                        <td><span class="orange"><?= $model['refund_balance_money'] ?></span>元</td>
                    </tr>
                    <tr>
                        <th>退款原因：</th>
                        <td><?= $model['refund_reason'] ?? '买/卖双方协商一致' ?></td>
                    </tr>
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
            <div class="info-div">协商记录</div>
            <table class="hide">
                <tbody>
                <tr class="tr-title">
                    <td>买家</td>
                    <td class="td-time">2019-06-27 10:01:10</td>
                </tr>
                <tr>
                    <td colspan="2">
                        <span>【买家申请退款】</span>
                    </td>
                </tr>
                </tbody>
                <tbody>
                <tr class="tr-title">
                    <td>卖家</td>
                    <td class="td-time">2019-06-27 10:01:35</td>
                </tr>
                <tr>
                    <td colspan="2">
                        <span>【退款申请不通过】</span>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>