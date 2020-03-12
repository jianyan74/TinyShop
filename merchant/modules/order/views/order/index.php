<?php

use common\helpers\Url;
use yii\widgets\LinkPager;
use yii\widgets\ActiveForm;
use common\enums\PayTypeEnum;
use common\helpers\ImageHelper;
use addons\TinyShop\common\enums\ShippingTypeEnum;
use addons\TinyShop\common\enums\OrderStatusEnum;
use addons\TinyShop\common\helpers\OrderHelper;
use addons\TinyShop\common\enums\OrderTypeEnum;
use addons\TinyShop\common\enums\AccessTokenGroupEnum;

$addon = <<< HTML
<span class="input-group-addon">
    <i class="glyphicon glyphicon-calendar"></i>
</span>
HTML;

$this->title = '订单管理';
$this->params['breadcrumbs'][] = ['label' => $this->title];
?>

<div class="tabs-container">
    <?= $this->render('_nav', [
        'order_status' => $order_status,
        'total' => $total,
    ]) ?>
    <div class="tab-content">
        <div class="tab-pane active">
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-12">
                        <?php $form = ActiveForm::begin([
                            'action' => Url::to(['index', 'order_status' => $order_status]),
                            'method' => 'get',
                        ]); ?>
                        <div class="col-sm-9"></div>
                        <div class="col-sm-3">
                            <div class="input-group m-b">
                                <input type="text" class="form-control" name="order_sn" placeholder="订单编号" value="<?= $order_sn ?>"/>
                                <span class="input-group-btn"><button class="btn btn-white"><i class="fa fa-search"></i> 搜索</button></span>
                            </div>
                        </div>
                        <?php ActiveForm::end(); ?>
                    </div>
                </div>
                <div class="col-sm-12">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <td>封面</td>
                            <th>商品信息</th>
                            <th>商品清单</th>
                            <th>价格</th>
                            <th>收货信息</th>
                            <th>买家</th>
                            <th>状态</th>
                            <th>配送方式</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($models as $model) { ?>
                            <?php
                            $rowspanCount = count($model['product']);
                            $rowspanStr = '';
                            $rowspanCount > 0 && $rowspanStr = "rowspan={$rowspanCount}"
                            ?>
                            <tr>
                                <td colspan="9">
                                    <span class="fa fa-angle-down"></span>
                                    订单编号：<?= $model->order_sn; ?>
                                    <span class="label label-default"><?= OrderTypeEnum::getValue($model->order_type); ?></span>
                                    <div class="pull-right">
                                        下单时间：<?= Yii::$app->formatter->asDatetime($model->created_at) ?>
                                    </div>
                                </td>
                            </tr>
                            <tr id= <?= $model->id; ?>>
                                <td>
                                    <?= ImageHelper::fancyBox($model['product'][0]['product_picture'])?>
                                </td>
                                <td>
                                    <small><?= $model['product'][0]['product_name']; ?></small>
                                    <br>
                                    <small style="color: #999"><?= $model['product'][0]['sku_name']; ?></small>
                                </td>
                                <td>
                                    <span class="pull-left"><?= $model['product'][0]['price']; ?>元 <?php if($model['product'][0]['adjust_money'] != 0) { ?>(调价：<?= $model['product'][0]['adjust_money']; ?>元)<?php } ?></span>
                                    <span class="pull-right"><?= $model['product'][0]['num']; ?>件</span><br>
                                    <?= OrderHelper::refundOperation($model['product'][0]['id'], $model['product'][0]['refund_status'])?>
                                </td>
                                <td style="text-align: center" <?= $rowspanStr; ?>>
                                    订单金额：<span class="orange"><?= Yii::$app->formatter->asDecimal($model->pay_money, 2); ?></span><br>
                                    <small>(含配送费:<?= Yii::$app->formatter->asDecimal($model['shipping_money'], 2) ?>元)</small><br>
                                    <small><?= PayTypeEnum::getValue($model['payment_type']) ?></small>
                                </td>
                                <td <?= $rowspanStr; ?>>
                                    <?= $model->receiver_name; ?><br>
                                    <?= $model->receiver_mobile; ?><br>
                                    <?= $model->receiver_region_name; ?> <?= $model->receiver_address; ?>
                                </td>
                                <td <?= $rowspanStr; ?> style="text-align: center">
                                    <?= $model->member->nickname; ?> <br>
                                    <span class="blue" style="font-size: 12px"><?= AccessTokenGroupEnum::getValue($model->order_from); ?></span>
                                </td>
                                <td <?= $rowspanStr; ?> style="text-align: center">
                                    <span class="label label-primary"><?= OrderStatusEnum::getValue($model['order_status']) ?></span>
                                </td>
                                <td <?= $rowspanStr; ?>
                                        style="text-align: center"><?= ShippingTypeEnum::getValue($model['shipping_type']); ?></td>
                                <td style="text-align: center" <?= $rowspanStr; ?>>
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
                                            <?= ImageHelper::fancyBox($detail['product_picture'])?>
                                        </td>
                                        <td>
                                            <small><?= $detail['product_name']; ?></small>
                                            <br>
                                            <small style="color: #999"><?= $detail['sku_name']; ?></small>
                                        </td>
                                        <td>
                                            <span class="pull-left"><?= $detail['price']; ?>元 <?php if($detail['adjust_money'] != 0) { ?>(调价：<?= $detail['adjust_money']; ?>元)<?php } ?></span>
                                            <span class="pull-right"><?= $detail['num']; ?>件</span><br>
                                            <?= OrderHelper::refundOperation($detail['id'], $detail['refund_status'])?>
                                        </td>
                                    </tr>
                                <?php } ?>
                                <?php $i++; ?>
                            <?php } ?>

                            <?php if(!empty($model->seller_memo)) { ?>
                                <tr>
                                    <td colspan="9">
                                        卖家备注：<?= $model->seller_memo; ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            <tr style="background-color: #ecf0f5;"><td colspan="9"></td></tr>
                        <?php } ?>
                        </tbody>
                    </table>
                    <div class="row">
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
    </div>
</div>

<script>
    var orderProductAgreeUrl = "<?= Url::to(['product/refund-pass']); ?>";
    var orderProductRefuseUrl = "<?= Url::to(['product/refund-no-pass']); ?>";
    var orderProductDeliveryUrl = "<?= Url::to(['product/refund-delivery']); ?>";
    var orderDeliveryUrl = "<?= Url::to(['take-delivery']); ?>";
    var orderCloseUrl = "<?= Url::to(['close']); ?>";
</script>