<?php

use common\helpers\Url;
use yii\widgets\LinkPager;
use common\enums\PayTypeEnum;
use common\enums\StatusEnum;
use common\enums\AppEnum;
use common\helpers\ImageHelper;
use common\helpers\Html;
use common\helpers\StringHelper;
use kartik\daterange\DateRangePicker;
use addons\TinyShop\common\enums\ShippingTypeEnum;
use addons\TinyShop\common\enums\OrderStatusEnum;
use addons\TinyShop\common\helpers\OrderHelper;
use addons\TinyShop\common\enums\OrderTypeEnum;
use addons\TinyShop\common\enums\AccessTokenGroupEnum;
use addons\TinyShop\common\models\order\Order;

/** @var Order $model */

$addon = <<< HTML
<span class="input-group-addon">
    <i class="glyphicon glyphicon-calendar"></i>
</span>
HTML;

$this->title = '订单管理';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => 'index'];

?>

<div class="row">
    <div class="col-sm-12">
        <div class="nav-tabs-custom">
            <?= $this->render('_nav', [
                'orderStatus' => $search->order_status,
                'total' => $total,
            ]) ?>
            <div class="tab-content">
                <div class="row m-r-xs">
                    <div class="col-sm-12">
                        <div class="float-right">
                            <div class="input-group m-b">
                            <span>
                                <a href="#" data-toggle='modal' data-target="#query" class="btn btn-white"><i class="fa fa-search"></i> 筛选查询</a>
                                <?= $this->render('_search', [
                                    'orderStatus' => $search->order_status,
                                    'search' => $search,
                                ]) ?>
                            </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <table  class="table table-bordered <?= Yii::$app->services->extendDetection->isMobile() ? 'rf-table' : ''; ?>" fixedNumber="1" fixedRightNumber="1">
                        <thead>
                        <tr>
                            <td class="text-center" width="50px">封面</td>
                            <th>商品</th>
                            <th class="text-center" width="120">单价(元) / 数量</th>
                            <th class="text-center" width="130">售后</th>
                            <th class="text-center">实收金额(元)</th>
                            <th class="text-center">买家 / 收货人</th>
                            <th class="text-center">订单状态</th>
                            <th class="text-center">配送方式</th>
                            <th class="text-center">操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($models as $model) { ?>
                            <?php
                            $rowspanCount = count($model['product']);
                            $rowspanStr = '';
                            $rowspanCount > 0 && $rowspanStr = "rowspan={$rowspanCount}";
                            $firstProduct = $model['product'][0];
                            ?>
                            <tr>
                                <td colspan="9" style="height: 50px;line-height: 2.2">
                                    <span class="fa fa-angle-down"></span>
                                    订单号：<?= $model->order_sn; ?>
                                    <span class="label label-outline-default"><?= OrderTypeEnum::getValue($model->order_type); ?></span>
                                    <div class="float-right">
                                        <span class="m-r"><?= $model->distribution_time_out ?></span>
                                        <span class="m-r">下单时间：<?= Yii::$app->formatter->asDatetime($model->created_at) ?></span>
                                        <span class="label label-outline-default"><?= AccessTokenGroupEnum::getValue($model->order_from); ?></span>
                                        <span class="label label-outline-danger"><?= $model->is_oversold > 0 ? '超卖' : ''; ?></span>
                                        <span class="m-r">
                                                <?php if($model->is_print == StatusEnum::ENABLED){ ?>
                                                    <span class="label label-outline-success">已打单</span>
                                                <?php } else { ?>
                                                    <span class="label label-outline-danger">未打单</span>
                                                <?php } ?>
                                            </span>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-white" data-toggle="dropdown" aria-expanded="false">票据打印</button>
                                            <button type="button" class="btn btn-white dropdown-toggle dropdown-icon" data-toggle="dropdown" aria-expanded="false">
                                                <span class="sr-only">切换下拉</span>
                                            </button>
                                            <ul class="dropdown-menu" role="menu" data-id="<?= $model->id; ?>" style="left: -70px;text-align: center">
                                                <?php foreach ($receiptPrinter as $print) {?>
                                                    <a href="javascript:void(0);" class="dropdown-item print-receipt" data-id="<?= $print['id'] ?>"><?= $print['title'] ?></a>
                                                <?php } ?>
                                                <a href="<?= Url::to(['print-delivery', 'id' => $model->id])?>" class="dropdown-item" target="_blank">出库单打印</a>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php if (Yii::$app->services->devPattern->isB2B2C() && Yii::$app->id == AppEnum::BACKEND) { ?>
                                <tr>
                                    <td colspan="9"><?= $model->merchant_title; ?></td>
                                </tr>
                            <?php } ?>
                            <tr id= <?= $model->id; ?>>
                                <td>
                                    <?= ImageHelper::fancyBox($firstProduct['product_picture']) ?>
                                </td>
                                <td style="max-width: 200px;border-right: 1px solid #fff;">
                                    <small><?= Yii::$app->services->extendDetection->isMobile() ? Html::textNewLine($firstProduct['product_name'], 15) : $firstProduct['product_name']; ?></small>
                                    <br>
                                    <small style="color: #999"><?= $firstProduct['sku_name']; ?></small>
                                </td>
                                <td class="text-center">
                                    <?= $firstProduct['price']; ?>
                                    <?php if ($firstProduct['adjust_money'] != 0) { ?> <br><small>(调价：<?= $firstProduct['adjust_money']; ?>)</small><?php } ?> <br>
                                    <?= $firstProduct['num']; ?>件<br>
                                </td>
                                <td>
                                    <?= OrderHelper::refundOperation($firstProduct['after_sale_id'], $firstProduct['refund_status'], $firstProduct['refund_type']) ?>
                                </td>
                                <td <?= $rowspanStr; ?> class="text-center">
                                    <span class="orange"><?= $model->pay_money; ?></span><br>
                                    <?php if ($model->final_money > 0) { ?>
                                        <small>待付尾款：<?= $model->final_money ?></small><br>
                                    <?php } ?>
                                    <small>(含配送费: <?= $model['shipping_money'] ?>)</small><br>
                                    <small><?= PayTypeEnum::getValue($model['pay_type']) ?></small>
                                </td>
                                <td <?= $rowspanStr; ?> class="text-center">
                                    <?php if (empty($model->buyer_id)) {?>
                                        散客
                                    <? } else { ?>
                                        <span class="blue member-view pointer" data-href="<?= Url::toRoute(['/member/member/view', 'id' => $model->buyer_id]); ?>"><?= $model->buyer_nickname; ?></span> <br>
                                        <?= $model->receiver_realname; ?><br>
                                        <?= StringHelper::hideStr($model->receiver_mobile, 3); ?><br>
                                        <?= Html::encode($model->receiver_name); ?> <?= Html::encode($model->receiver_details); ?>
                                    <?php } ?>
                                </td>
                                <td <?= $rowspanStr; ?> class="text-center">
                                    <?= OrderStatusEnum::getValue($model['order_status']) ?>
                                </td>
                                <td <?= $rowspanStr; ?> class="text-center"><?= ShippingTypeEnum::getValue($model['shipping_type']); ?></td>
                                <td <?= $rowspanStr; ?> class="text-center">
                                    <?= $this->render('_operation-link', [
                                        'model' => $model,
                                        'class' => ''
                                    ]) ?>
                                </td>
                            </tr>
                            <?php $i = 0; ?>
                            <?php foreach ($model['product'] as $detail) { ?>
                                <?php if ($i != 0) { ?>
                                    <tr>
                                        <td>
                                            <?= ImageHelper::fancyBox($detail['product_picture']) ?>
                                        </td>
                                        <td style="max-width: 200px;;border-right: 1px solid #fff;">
                                            <small><?= Yii::$app->services->extendDetection->isMobile() ? Html::textNewLine($detail['product_name'], 15) : $detail['product_name']; ?></small>
                                            <br>
                                            <small style="color: #999"><?= $detail['sku_name']; ?></small>
                                        </td>
                                        <td class="text-center">
                                            <?= $detail['price']; ?>
                                            <?php if ($detail['adjust_money'] != 0) { ?> <br><small>(调价: <?= $detail['adjust_money']; ?>)</small><?php } ?> <br>
                                            <?= $detail['num']; ?>件 <br>
                                        </td>
                                        <td>
                                            <?= OrderHelper::refundOperation($detail['after_sale_id'], $detail['refund_status'], $detail['refund_type']) ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                                <?php $i++; ?>
                            <?php } ?>
                            <?php if (!empty($model->buyer_message)) { ?>
                                <tr>
                                    <td colspan="9" class="orange" style="background: #fffaeb">
                                        买家留言：<?= Html::encode($model->buyer_message); ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            <?php if (!empty($model->seller_memo)) { ?>
                                <tr>
                                    <td colspan="9" class="orange" style="background: #fffaeb">
                                        卖家备注：<?= Html::encode($model->seller_memo); ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            <tr class="rf-bg">
                                <td colspan="9"></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-sm-12">
                    <?= LinkPager::widget([
                        'pagination' => $pages,
                        'maxButtonCount' => 5,
                    ]); ?>
                </div>
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

    // 打印小票
    $('.print-receipt').click(function () {
        var id = $(this).parent().data('id');
        var config_id = $(this).data('id');

        $.ajax({
            type: "get",
            url: '<?= Url::to(['print-receipt'])?>',
            dataType: "json",
            data: {id: id, config_id: config_id},
            success: function (data) {
                if (parseInt(data.code) === 200) {
                    swal('小手一抖打开一个窗', {
                        buttons: {
                            defeat: '确定',
                        },
                        title: '操作成功',
                    }).then(function (value) {

                    });
                } else {
                    rfWarning(data.message);
                }
            }
        });
    })
</script>
