<?php

use common\helpers\Url;
use yii\widgets\LinkPager;
use common\enums\PayTypeEnum;
use common\helpers\Html;
use addons\TinyShop\common\helpers\OrderHelper;
use addons\TinyShop\common\enums\RefundStatusEnum;
use addons\TinyShop\common\enums\AccessTokenGroupEnum;

$this->title = '售后服务';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= $this->title; ?></h3>
                <div class="box-tools">

                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>商品信息</th>
                        <th>商品清单</th>
                        <th>订单金额</th>
                        <th>收货信息</th>
                        <th>买家</th>
                        <th>交易状态</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($models as $model) { ?>
                        <tr>
                            <td colspan="8">
                                <span class="fa fa-angle-down"></span>
                                订单编号：<?= $model->order_sn; ?>
                                <div class="pull-right">
                                    下单时间：<?= Yii::$app->formatter->asDatetime($model->created_at) ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <small><?= $model['product_name']; ?></small>
                                <br>
                                <small style="color: #999"><?= $model['sku_name']; ?></small>
                            </td>
                            <td>
                                <span class="pull-left"><?= $model['price']; ?>元</span>
                                <span class="pull-right"><?= $model['num']; ?>件</span><br>
                                <?= OrderHelper::refundOperation($model['id'], $model['refund_status'], '')?>
                            </td>
                            <td style="text-align: center">
                                <span class="orange"><?= $model->product_money; ?></span><br>
                                <small><?= PayTypeEnum::getValue($model['payment_type']) ?></small>
                            </td>
                            <td>
                                <?= $model->receiver_name; ?><br>
                                <?= $model->receiver_mobile; ?><br>
                                <?= $model->receiver_region_name; ?> <?= $model->receiver_address; ?>
                            </td>
                            <td style="text-align: center">
                                <span class="blue member-view pointer" data-href="<?= Url::to(['/member/view', 'member_id' => $model->member_id]); ?>"><?= Html::encode($model->user_name); ?></span><br>
                                <span class="blue" style="font-size: 12px"><?= AccessTokenGroupEnum::getValue($model->order_from); ?></span>
                            </td>
                            <td>
                                <?= RefundStatusEnum::getValue($model['refund_status'])?>
                            </td>
                            <td>
                                <span class="cyan order-view pointer" data-href="<?= Url::to(['order/detail', 'id' => $model->order_id]) ?>">订单详情</span>
                            </td>
                        </tr>
                        <?php if(!empty($model->seller_memo)) { ?>
                            <tr>
                                <td colspan="7">
                                    卖家备注：<?= $model->seller_memo; ?>
                                </td>
                            </tr>
                        <?php } ?>
                        <tr style="background-color: #ecf0f5;"><td colspan="8"></td></tr>
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
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
    </div>
</div>

<script>
    var orderProductAgreeUrl = "<?= Url::to(['refund-pass']); ?>";
    var orderProductRefuseUrl = "<?= Url::to(['refund-no-pass']); ?>";
    var orderProductDeliveryUrl = "<?= Url::to(['refund-delivery']); ?>";
</script>