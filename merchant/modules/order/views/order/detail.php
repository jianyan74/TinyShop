<?php

use common\enums\AppEnum;
use common\helpers\Url;
use common\helpers\ImageHelper;
use common\enums\StatusEnum;
use common\helpers\Html;
use common\enums\PayTypeEnum;
use addons\TinyShop\common\enums\PayGroupEnum;
use addons\TinyShop\common\enums\OrderStatusEnum;
use addons\TinyShop\common\enums\ShippingTypeEnum;
use addons\TinyShop\common\enums\OrderTypeEnum;
use addons\TinyShop\common\helpers\OrderHelper;
use addons\TinyShop\common\enums\MarketingEnum;
use addons\TinyShop\common\models\order\Order;

/** @var Order $model */

$this->title = '订单详情';
$this->params['breadcrumbs'][] = ['label' => '订单管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<style>
    .col-distance {
        width: 20%;
    }
</style>

<div class="ns-main">
    <div class="mod-table">
        <?php if ($model->order_status != OrderStatusEnum::REPEAL) { ?>
            <div class="step-region">
                <ul class="ui-step">
                    <li class="<?= !empty($model->created_at) ? 'ui-step-done' : ''; ?>  col-distance">
                        <div class="ui-step-title">买家下单</div>
                        <div class="ui-step-number">1</div>
                        <div class="ui-step-meta"><?= !empty($model->created_at) ? Yii::$app->formatter->asDatetime($model->created_at) : ''; ?></div>
                    </li>
                    <li class="<?= !empty($model->pay_time) ? 'ui-step-done' : ''; ?>  col-distance">
                        <div class="ui-step-title">买家付款</div>
                        <div class="ui-step-number">2</div>
                        <div class="ui-step-meta"><?= !empty($model->pay_time) ? Yii::$app->formatter->asDatetime($model->pay_time) : ''; ?></div>
                    </li>
                    <li class="<?= !empty($model->consign_time) ? 'ui-step-done' : ''; ?>  col-distance">
                        <div class="ui-step-title">商家发货</div>
                        <div class="ui-step-number">3</div>
                        <div class="ui-step-meta"><?= !empty($model->consign_time) ? Yii::$app->formatter->asDatetime($model->consign_time) : ''; ?></div>
                    </li>
                    <li class="<?= !empty($model->sign_time) ? 'ui-step-done' : ''; ?>  col-distance">
                        <div class="ui-step-title">买家收货</div>
                        <div class="ui-step-number">4</div>
                        <div class="ui-step-meta"><?= !empty($model->sign_time) ? Yii::$app->formatter->asDatetime($model->sign_time) : ''; ?></div>
                    </li>
                    <li class="<?= !empty($model->finish_time) ? 'ui-step-done' : ''; ?>  col-distance">
                        <div class="ui-step-title">交易完成</div>
                        <div class="ui-step-number">5</div>
                        <div class="ui-step-meta"><?= !empty($model->finish_time) ? Yii::$app->formatter->asDatetime($model->finish_time) : ''; ?></div>
                    </li>
                </ul>
            </div>
        <?php } ?>
        <div class="step-region clearfix">
            <div class="info-region">
                <div class="info-div">订单信息<span class="secured-title">担保交易</span></div>
                <table class="info-table">
                    <tbody>
                    <tr>
                        <th>订单编号：</th>
                        <td><?= $model->order_sn; ?></td>
                    </tr>
                    <?php foreach ($model['payLog'] as $payLog) { ?>
                        <?php if ($payLog['order_group'] != PayGroupEnum::ORDER) { ?>
                            <tr>
                                <th>订单交易号：</th>
                                <td>
                                    <?= $payLog['out_trade_no']; ?> <br>
                                    <?= PayGroupEnum::getOrderDetailValue($payLog['order_group']); ?>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                    <tr style="display: table-row;">
                        <th>订单类型：</th>
                        <td><?= OrderTypeEnum::getValue($model->order_type) ?></td>
                    </tr>
                    <tr>
                        <th>付款方式：</th>
                        <td><?= PayTypeEnum::getValue($model->pay_type) ?></td>
                    </tr>
                    <tr>
                        <th>买家：</th>
                        <td>
                            <span class="blue member-view pointer" data-href="<?= Url::toRoute(['/member/member/view', 'id' => $model->buyer_id]); ?>"><?= Html::encode($model->buyer_nickname); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>买家IP：</th>
                        <td><span><?= $model->buyer_ip; ?></span></td>
                    </tr>
                    </tbody>
                </table>
                <div class="dashed-line"></div>
                <table class="info-table">
                    <tbody>
                    <tr>
                        <th>配送方式：</th>
                        <td><?= ShippingTypeEnum::getValue($model->shipping_type) ?>  <?= $model->company_name ?? ''; ?></td>
                    </tr>
                    <!-- 物流配送 -->
                    <?php if (in_array($model['shipping_type'], [ShippingTypeEnum::LOGISTICS, ShippingTypeEnum::LOCAL_DISTRIBUTION])) { ?>
                        <tr>
                            <th>配送时间：</th>
                            <td><?= !empty($model['distribution_time_out']) ? $model['distribution_time_out'] : '尽快送达'; ?></td>
                        </tr>
                        <!-- 物流 -->
                        <tr>
                            <th>收货信息：</th>
                            <td>
                                <?= $model->receiver_realname; ?>，<?= $model->receiver_mobile; ?>
                                ，<?= $model->receiver_name; ?> <?= Html::encode($model->receiver_details); ?>
                            </td>
                        </tr>
                    <?php } ?>
                    <!-- 自提 -->
                    <?php if (ShippingTypeEnum::PICKUP == $model->shipping_type) { ?>
                        <tr>
                            <th>自提地点：</th>
                            <td>
                                <?= $model->store->contacts; ?>
                                <?= $model->store->mobile; ?> <br>
                                <?= $model->store->address_name; ?>
                                <?= $model->store->address_details; ?>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if (!empty($model->invoice)) { ?>
                        <tr>
                            <th>发票抬头：</th>
                            <td><span><?= Html::encode($model->invoice->title); ?></span></td>
                        </tr>
                        <tr>
                            <th>发票税号：</th>
                            <td><span><?= Html::encode($model->invoice->duty_paragraph); ?></span></td>
                        </tr>
                        <?php if (!empty($model->invoice->address)) {?>
                            <tr>
                                <th>发票地址：</th>
                                <td><span><?= Html::encode($model->invoice->address); ?></span></td>
                            </tr>
                        <?php } ?>
                        <?php if (!empty($model->invoice->phone)) {?>
                            <tr>
                                <th>发票号码：</th>
                                <td><span><?= Html::encode($model->invoice->phone); ?></span></td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <th>发票内容：</th>
                            <td><span><?= Html::encode($model->invoice->explain); ?></span></td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <th>买家留言：</th>
                        <td><?= !empty($model->buyer_message) ? Html::encode($model->buyer_message) : '此订单没有留言'; ?></td>
                    </tr>
                    <tr>
                        <th>卖家留言：</th>
                        <td><?= !empty($model->seller_memo) ? Html::encode($model->seller_memo) : '此订单没有留言'; ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="state-region">
                <div style="padding: 0 0 30px 40px;" id="<?= $model['id'] ?>">
                    <div class="state-title"><span
                                class="icon info">!</span>订单状态：<?= OrderStatusEnum::getValue($model['order_status']) ?></div>
                    <div class="state-action">
                        <?= $this->render('_operation-link', [
                            'model' => $model,
                            'class' => 'btn btn-primary btn-sm',
                        ]) ?>
                    </div>
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

<?php if (!empty($product)) { ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-body">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>商品图</th>
                            <th>商品</th>
                            <th>原价(元)</th>
                            <th>参考单价(元)</th>
                            <th>数量</th>
                            <th>调整金额(元)</th>
                            <th>小计金额(元)</th>
                            <th>赠品?</th>
                            <th class="text-align-center">售后</th>
                            <th>配送状态</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($product as $detail) { ?>
                            <tr>
                                <td>
                                    <?= ImageHelper::fancyBox(ImageHelper::default($detail['product_picture'])); ?>
                                </td>
                                <td>
                                    <small><?= Html::textNewLine($detail['product_name']); ?></small>
                                    <br>
                                    <small style="color: #999"><?= $detail['sku_name']; ?></small>
                                </td>
                                <td><?= $detail['product_original_money']; ?></td>
                                <td><?= $detail['price']; ?></td>
                                <td><?= $detail['num']; ?></td>
                                <td><?= $detail['adjust_money']; ?></td>
                                <td><?= $detail['product_money']; ?></td>
                                <td>
                                    <?= $detail['gift_flag'] > 0 ? '<span class="label label-primary">是</span>' : '<span class="label label-default">否</span>'; ?>
                                </td>
                                <td><?= OrderHelper::refundOperation($detail['after_sale_id'], $detail['refund_status'], $detail['refund_type'])?></td>
                                <td><?= OrderStatusEnum::getValue($detail['order_status']); ?></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<?php if (!empty($productExpress)) { ?>
    <?php foreach ($productExpress as $key => $express) { ?>
        <div class="row">
            <div class="col-lg-12">
                <div class="box">
                    <div class="box-body">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>商品图</th>
                                <th>商品</th>
                                <th>原价(元)</th>
                                <th>参考单价(元)</th>
                                <th>数量</th>
                                <th>调整金额(元)</th>
                                <th>小计金额(元)</th>
                                <th>赠品?</th>
                                <th class="text-align-center">售后</th>
                                <th>配送状态</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td colspan="9">
                                    <?php if ($express['shipping_type'] == StatusEnum::ENABLED) { ?>
                                        包裹 + <?= $key + 1 ?>;
                                        物流公司: <?= $express['express_company'] ?>;
                                        运单号: <?= $express['express_no'] ?>
                                        <span class="m-l-md">
                                            <?= Html::a('修改运单号',
                                                ['product-express/update', 'id' => $express['id']], [
                                                    'class' => 'cyan',
                                                    'data-toggle' => 'modal',
                                                    'data-target' => '#ajaxModalLg',
                                                ]) ?>
                                        </span>
                                        <span class="m-l-sm">
                                             <?= Html::a('查看物流状态',
                                                 ['product-express/company', 'id' => $express['id']], [
                                                     'class' => 'cyan',
                                                     'data-toggle' => 'modal',
                                                     'data-target' => '#ajaxModalLg',
                                                 ]) ?>
                                        </span>
                                    <?php } else { ?>
                                        无需物流
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php foreach ($express['product'] as $detail) { ?>
                                <tr>
                                    <td>
                                        <?= ImageHelper::fancyBox(ImageHelper::default($detail['product_picture'])); ?>
                                    </td>
                                    <td>
                                        <small><?= $detail['product_name']; ?></small>
                                        <br>
                                        <small style="color: #999"><?= $detail['sku_name']; ?></small>
                                    </td>
                                    <td><?= $detail['product_original_money']; ?></td>
                                    <td><?= $detail['price']; ?></td>
                                    <td><?= $detail['num']; ?></td>
                                    <td><?= $detail['adjust_money']; ?></td>
                                    <td><?= $detail['product_money']; ?></td>
                                    <td>
                                        <?= $detail['gift_flag'] > 0 ? '<span class="label label-primary">是</span>' : '<span class="label label-default">否</span>'; ?>
                                    </td>
                                    <td><?= OrderHelper::refundOperation($detail['after_sale_id'], $detail['refund_status'], $detail['refund_type'])?></td>
                                    <td><?= OrderStatusEnum::getValue($detail['order_status']); ?></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
<?php } ?>

<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-body">
                <div class="float-right">
                    商品总金额：￥<?= $model['product_original_money'] ?>，
                    <?php foreach ($marketingDetails as $marketingDetail) { ?>
                        <?php if ($marketingDetail['discount_money'] > 0) {?>
                            <?php if (in_array($marketingDetail['marketing_type'], [MarketingEnum::GIVE_POINT, MarketingEnum::GIVE_GROWTH])) { ?>
                                <?= $marketingDetail['marketing_name'] ?>：<?= $marketingDetail['discount_money'] ?>，
                            <?php } else { ?>
                                <?= $marketingDetail['marketing_name'] ?>：￥-<?= $marketingDetail['discount_money'] ?>，
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                    <?php if ($model['tax_money'] > 0) {?>发票税额：￥<?= $model['tax_money'] ?>，<?php } ?>
                    <?php if ($model['point'] > 0) {?>使用积分：<?= $model['point'] ?>，<?php } ?>
                    实际需支付：<b class="red">￥<?= $model['pay_money'] ?></b> （含运费
                    ￥<?= $model['shipping_money'] ?>）
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
                        <th>订单日志</th>
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

<script>
    var orderProductAgreeUrl = "<?= Url::to(['after-sale/pass']); ?>";
    var orderProductRefuseUrl = "<?= Url::to(['after-sale/refuse']); ?>";
    var orderProductTakeDeliveryUrl = "<?= Url::to(['after-sale/take-delivery']); ?>"; // 确认收货
    var orderProductDeliveryUrl = "<?= Url::to(['after-sale/delivery']); ?>"; // 发货(换货)
    var orderStockUpAccomplishUrl = "<?= Url::to(['stock-up-accomplish']); ?>";
    var orderDeliveryUrl = "<?= Url::to(['take-delivery']); ?>";
    var orderCloseUrl = "<?= Url::to(['close']); ?>";
    var orderChargebackUrl = "<?= Url::to(['chargeback']); ?>";
    var orderAffirmUrl = "<?= Url::to(['affirm']); ?>";
</script>
