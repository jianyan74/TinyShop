<?php

use common\helpers\Url;
use yii\widgets\LinkPager;
use common\enums\PayTypeEnum;
use common\helpers\ImageHelper;
use common\helpers\Html;
use addons\TinyShop\common\enums\ShippingTypeEnum;
use addons\TinyShop\common\enums\OrderStatusEnum;
use addons\TinyShop\common\helpers\OrderHelper;
use addons\TinyShop\common\enums\OrderTypeEnum;
use addons\TinyShop\common\enums\AccessTokenGroupEnum;
use kartik\daterange\DateRangePicker;

$addon = <<< HTML
<span class="input-group-addon">
    <i class="glyphicon glyphicon-calendar"></i>
</span>
HTML;

$export = \common\helpers\ArrayHelper::toArray($search);
$export[0] = 'export';

$this->title = '订单管理';
$this->params['breadcrumbs'][] = ['label' => $this->title];
?>

<div class="tabs-container">
    <?= $this->render('_nav', [
        'order_status' => $search->order_status,
        'total' => $total,
    ]) ?>
    <div class="tab-content">
        <div class="tab-pane active">
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="input-group m-b pull-right">
                            <span>
                                <a href="#" data-toggle='modal' data-target="#query" class="btn btn-white"><i class="fa fa-search"></i> 筛选查询</a>
                            </span>
                        </div>
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
                                    <?= ImageHelper::fancyBox($model['product'][0]['product_picture']) ?>
                                </td>
                                <td style="max-width: 200px">
                                    <small><?= $model['product'][0]['product_name']; ?></small>
                                    <br>
                                    <small style="color: #999"><?= $model['product'][0]['sku_name']; ?></small>
                                </td>
                                <td>
                                    <span class="pull-left"><?= $model['product'][0]['product_money']; ?>元 <?php if ($model['product'][0]['adjust_money'] != 0) { ?>(调价：<?= $model['product'][0]['adjust_money']; ?>元)<?php } ?></span>
                                    <span class="pull-right"><?= $model['product'][0]['num']; ?>件</span><br>
                                    <?= OrderHelper::refundOperation($model['product'][0]['id'],
                                        $model['product'][0]['refund_status']) ?>
                                </td>
                                <td style="text-align: center" <?= $rowspanStr; ?>>
                                    订单金额：<span class="orange"><?= $model->pay_money; ?></span><br>
                                    <?php if ($model->final_payment_money > 0) { ?><small>
                                        待付尾款：<?= $model->final_payment_money ?></small><br><?php } ?>
                                    <small>(含配送费:<?= $model['shipping_money'] ?>元)</small><br>
                                    <small><?= PayTypeEnum::getValue($model['payment_type']) ?></small>
                                </td>
                                <td <?= $rowspanStr; ?>>
                                    <?= $model->receiver_name; ?><br>
                                    <?= $model->receiver_mobile; ?><br>
                                    <?= $model->receiver_region_name; ?> <?= Html::encode($model->receiver_address); ?>
                                </td>
                                <td <?= $rowspanStr; ?> style="text-align: center">
                                    <span class="blue member-view pointer" data-href="<?= Url::to(['/member/view', 'member_id' => $model->buyer_id]); ?>"><?= $model->user_name; ?></span> <br>
                                    <span
                                          style="font-size: 12px"><?= AccessTokenGroupEnum::getValue($model->order_from); ?></span>
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
                                            <?= ImageHelper::fancyBox($detail['product_picture']) ?>
                                        </td>
                                        <td style="max-width: 200px">
                                            <small><?= $detail['product_name']; ?></small>
                                            <br>
                                            <small style="color: #999"><?= $detail['sku_name']; ?></small>
                                        </td>
                                        <td>
                                            <span class="pull-left"><?= $detail['price']; ?>元 <?php if ($detail['adjust_money'] != 0) { ?>(调价：<?= $detail['adjust_money']; ?>元)<?php } ?></span>
                                            <span class="pull-right"><?= $detail['num']; ?>件</span><br>
                                            <?= OrderHelper::refundOperation($detail['id'], $detail['refund_status']) ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                                <?php $i++; ?>
                            <?php } ?>
                            <?php if (!empty($model->seller_memo)) { ?>
                                <tr>
                                    <td colspan="9">
                                        卖家备注：<?= Html::encode($model->seller_memo); ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            <tr style="background-color: #ecf0f5;">
                                <td colspan="9"></td>
                            </tr>
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

<div class="modal fade" id="query" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <?= Html::beginForm('', 'get') ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close"><span aria-hidden="true">×</span><span class="sr-only">关闭</span>
                </button>
                <h4 class="modal-title">筛选查询</h4>
            </div>
            <div class="modal-body">
                <div class="form-group field-cate-pid">
                    <div class="col-sm-2 text-right">
                        <label class="control-label" for="cate-pid">搜索方式</label>
                    </div>
                    <div class="col-sm-5">
                        <?= Html::dropDownList('query_type', $search->query_type, [
                            '1' => '订单编号',
                            '2' => '订单交易号',
                            '3' => '收货人姓名',
                            '4' => '收货人手机',
                        ], [
                            'class' => 'form-control'
                        ]) ?>
                        <div class="help-block"></div>
                    </div>
                    <div class="col-sm-5">
                        <?= Html::textInput('keyword', $search->keyword, [
                            'class' => 'form-control'
                        ]) ?>
                        <div class="help-block"></div>
                    </div>
                </div>
                <div class="form-group field-cate-sort">
                    <div class="col-sm-2 text-right">
                        <label class="control-label" for="cate-sort">下单时间</label>
                    </div>
                    <div class="col-sm-10">
                        <div class="input-group drp-container">
                            <?= DateRangePicker::widget([
                                'name' => 'query_date',
                                'value' => !empty($search->start_time) ? $search->start_time . ' - ' . $search->end_time : '',
                                'useWithAddon' => true,
                                'convertFormat' => true,
                                'startAttribute' => 'start_time',
                                'endAttribute' => 'end_time',
                                'options' => [
                                    'class' => 'form-control',
                                    'placeholder' => '开始时间 - 结束时间'
                                ],
                                'pluginOptions' => [
                                    'locale' => ['format' => 'Y-m-d H:i'],
                                ],
                                'startInputOptions' => ['value' => $search->start_time],
                                'endInputOptions' => ['value' => $search->end_time],
                            ]) . $addon; ?>
                        </div>
                        <div class="help-block"></div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group field-cate-sort">
                        <div class="col-sm-4 text-right">
                            <label class="control-label" for="cate-sort">订单类型</label>
                        </div>
                        <div class="col-sm-8">
                            <?= Html::dropDownList('order_type', $search->order_type, OrderTypeEnum::getMap(), [
                                'class' => 'form-control',
                                'prompt' => '请选择',
                            ]) ?>
                            <div class="help-block"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group field-cate-sort">
                        <div class="col-sm-4 text-right">
                            <label class="control-label" for="cate-sort">订单状态</label>
                        </div>
                        <div class="col-sm-8">
                            <?= Html::dropDownList('order_status', $search->order_status,
                                OrderStatusEnum::getBackendMap(), [
                                    'class' => 'form-control',
                                    'prompt' => '请选择',
                                ]) ?>
                            <div class="help-block"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group field-cate-sort">
                        <div class="col-sm-4 text-right">
                            <label class="control-label" for="cate-sort">付款方式</label>
                        </div>
                        <div class="col-sm-8">
                            <?= Html::dropDownList('payment_type', $search->payment_type, PayTypeEnum::getMap(), [
                                'class' => 'form-control',
                                'prompt' => '请选择',
                            ]) ?>
                            <div class="help-block"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group field-cate-sort">
                        <div class="col-sm-4 text-right">
                            <label class="control-label" for="cate-sort">订单来源</label>
                        </div>
                        <div class="col-sm-8">
                            <?= Html::dropDownList('order_from', $search->order_from, AccessTokenGroupEnum::getMap(), [
                                'class' => 'form-control',
                                'prompt' => '请选择',
                            ]) ?>
                            <div class="help-block"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group field-cate-sort">
                        <div class="col-sm-4 text-right">
                            <label class="control-label" for="cate-sort">配送方式</label>
                        </div>
                        <div class="col-sm-8">
                            <?= Html::dropDownList('shipping_type', $search->shipping_type, ShippingTypeEnum::getMap(), [
                                'class' => 'form-control',
                                'prompt' => '请选择',
                            ]) ?>
                            <div class="help-block"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
                <button type="reset" class="btn btn-white">重置</button>
                <button class="btn btn-primary">确定</button>
            </div>
        </div>
        <?= Html::endForm() ?>
    </div>
</div>

<script>
    var orderProductAgreeUrl = "<?= Url::to(['product/refund-pass']); ?>";
    var orderProductRefuseUrl = "<?= Url::to(['product/refund-no-pass']); ?>";
    var orderProductDeliveryUrl = "<?= Url::to(['product/refund-delivery']); ?>";
    var orderStockUpAccomplishUrl = "<?= Url::to(['stock-up-accomplish']); ?>";
    var orderDeliveryUrl = "<?= Url::to(['take-delivery']); ?>";
    var orderCloseUrl = "<?= Url::to(['close']); ?>";
</script>