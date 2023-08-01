<?php

use common\helpers\Html;
use common\enums\AppEnum;
use common\helpers\ImageHelper;
use addons\TinyShop\common\enums\RefundStatusEnum;
use addons\TinyShop\common\enums\RefundTypeEnum;
use addons\TinyShop\common\enums\OrderAfterSaleTypeEnum;

$this->title = '售后详情';
$this->params['breadcrumbs'][] = ['label' => '订单管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="ns-main">
    <div class="mod-table">
        <?php if($model->refund_type == RefundTypeEnum::MONEY) { ?>
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
        <?php } ?>
        <?php if($model->refund_type == RefundTypeEnum::MONEY_AND_PRODUCT) { ?>
            <div class="step-region">
                <ul class="ui-step">
                    <li class="<?= $model->refund_status > 0 ? 'ui-step-done' : '';?>  col-lg-3">
                        <div class="ui-step-title">买家申请退款</div>
                        <div class="ui-step-number">1</div>
                        <div class="ui-step-meta"><?= !empty($model->refund_time) ? Yii::$app->formatter->asDatetime($model->refund_time) : '';?></div>
                    </li>
                    <li class="<?= $model->refund_status > RefundStatusEnum::APPLY ? 'ui-step-done' : '';?>  col-lg-3">
                        <div class="ui-step-title">商家退款处理</div>
                        <div class="ui-step-number">2</div>
                        <div class="ui-step-meta"></div>
                    </li>
                    <li class="<?= $model->refund_status > RefundStatusEnum::SALES_RETURN ? 'ui-step-done' : '';?>  col-lg-3">
                        <div class="ui-step-title">等待买家发货</div>
                        <div class="ui-step-number">3</div>
                        <div class="ui-step-meta"></div>
                    </li>
                    <li class="<?= $model->refund_status == RefundStatusEnum::CONSENT ? 'ui-step-done' : '';?>  col-lg-3">
                        <div class="ui-step-title">退款完成</div>
                        <div class="ui-step-number">4</div>
                        <div class="ui-step-meta"></div>
                    </li>
                </ul>
            </div>
        <?php } ?>
        <?php if($model->refund_type == RefundTypeEnum::EXCHANGE_PRODUCT) { ?>
            <div class="step-region">
                <ul class="ui-step">
                    <li class="<?= $model->refund_status > 0 ? 'ui-step-done' : '';?>  col-lg-2">
                        <div class="ui-step-title">买家申请退款</div>
                        <div class="ui-step-number">1</div>
                        <div class="ui-step-meta"><?= !empty($model->refund_time) ? Yii::$app->formatter->asDatetime($model->refund_time) : '';?></div>
                    </li>
                    <li class="<?= $model->refund_status > RefundStatusEnum::APPLY ? 'ui-step-done' : '';?>  col-lg-2">
                        <div class="ui-step-title">商家退款处理</div>
                        <div class="ui-step-number">2</div>
                        <div class="ui-step-meta"></div>
                    </li>
                    <li class="<?= $model->refund_status > RefundStatusEnum::SALES_RETURN ? 'ui-step-done' : '';?>  col-lg-2">
                        <div class="ui-step-title">等待买家发货</div>
                        <div class="ui-step-number">3</div>
                        <div class="ui-step-meta"></div>
                    </li>
                    <li class="<?= $model->refund_status > RefundStatusEnum::AFFIRM_SHIPMENTS ? 'ui-step-done' : '';?>  col-lg-2">
                        <div class="ui-step-title">等待卖家发货</div>
                        <div class="ui-step-number">3</div>
                        <div class="ui-step-meta"></div>
                    </li>
                    <li class="<?= $model->refund_status > RefundStatusEnum::SHIPMENTS ? 'ui-step-done' : '';?>  col-lg-2">
                        <div class="ui-step-title">等待买家收到商品</div>
                        <div class="ui-step-number">4</div>
                        <div class="ui-step-meta"></div>
                    </li>
                    <li class="<?= $model->refund_status == RefundStatusEnum::MEMBER_AFFIRM ? 'ui-step-done' : '';?>  col-lg-2">
                        <div class="ui-step-title">换货完成</div>
                        <div class="ui-step-number">5</div>
                        <div class="ui-step-meta"></div>
                    </li>
                </ul>
            </div>
        <?php } ?>
        <div class="step-region clearfix">
            <div class="info-region">
                <div class="info-div">退款信息</div>
                <div>
                    <div class="info-goods">
                        <div class="ui-centered-image">
                            <img src="<?= ImageHelper::default($model['orderProduct']['product_picture']) ?>" width="45" height="45">
                        </div>
                        <div class="info-goods-content">
                            <div><?= $model['orderProduct']['product_name'] ?> - <?= $model['orderProduct']['sku_name'] ?> </div>
                        </div>
                    </div>
                    <div class="dashed-line"></div>
                </div>
                <table class="info-table">
                    <tbody>
                    <tr>
                        <th>出售状态：</th>
                        <td>
                            <?= OrderAfterSaleTypeEnum::getValue($model['type']) ?>
                        </td>
                    </tr>
                    <tr>
                        <th>退款方式：</th>
                        <td>
                            <span class="orange"><?= RefundTypeEnum::getValue($model['refund_type']) ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>申请退款：</th>
                        <td><span class="orange"><?= $model['refund_apply_money'] ?></span> 元</td>
                    </tr>
                    <tr>
                        <th>退还金额：</th>
                        <td><span class="orange"><?= $model['refund_money'] ?></span> 元</td>
                    </tr>
                    <tr>
                        <th>退款原因：</th>
                        <td><?= !empty($model['refund_reason']) ? $model['refund_reason'] : '无' ?></td>
                    </tr>
                    <tr>
                        <th>退款说明：</th>
                        <td><?= !empty($model['refund_explain']) ? $model['refund_explain'] : '无' ?></td>
                    </tr>
                    <?php if (!empty($model['refund_evidence'])){ ?>
                        <tr>
                            <th>退款凭证：</th>
                            <td><?= ImageHelper::fancyBoxs($model['refund_evidence']) ?></td>
                        </tr>
                    <?php } ?>
                    <?php if (!empty($model['member_express_no'])){ ?>
                        <tr>
                            <th>退货物流单号：</th>
                            <td>
                                <?= Html::a($model['member_express_no'],
                                    ['company', 'id' => $model['id']], [
                                        'class' => 'cyan',
                                        'data-toggle' => 'modal',
                                        'data-target' => '#ajaxModalLg',
                                    ]) ?>
                            </td>
                        </tr>
                        <tr>
                            <th>退货物流公司：</th>
                            <td><?= $model['member_express_company'] ?></td>
                        </tr>
                        <tr>
                            <th>退货物流时间：</th>
                            <td><?= Yii::$app->formatter->asDatetime($model['member_express_time']) ?></td>
                        </tr>
                    <?php } ?>
                    <?php if (!empty($model['merchant_express_no'])){ ?>
                        <tr>
                            <th>商家物流单号：</th>
                            <td>
                                <?= Html::a($model['merchant_express_no'],
                                    ['company', 'id' => $model['id']], [
                                        'class' => 'cyan',
                                        'data-toggle' => 'modal',
                                        'data-target' => '#ajaxModalLg',
                                    ]) ?>
                            </td>
                        </tr>
                        <tr>
                            <th>商家物流公司：</th>
                            <td><?= $model['merchant_express_company'] ?></td>
                        </tr>
                        <tr>
                            <th>商家发货时间：</th>
                            <td><?= Yii::$app->formatter->asDatetime($model['merchant_express_time']) ?></td>
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
                            <li>买家付款后超过 7 天仍未发货，将有权申请客服介入发起退款维权；</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-body">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>协商日志</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($orderAction as $action) { ?>
                        <tr>
                            <td>
                                操作备注: <?= $action['member_name'] ?>
                                于 <?= Yii::$app->formatter->asDatetime($action['created_at']) ?>
                                在 <?= AppEnum::getValue($action['app_id']) ?>
                                <?= $action['remark'] ?>
                                <small>「<?= $action['provinces'] ?> <?= $action['city'] ?>」</small>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
