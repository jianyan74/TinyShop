<?php

use yii\helpers\Html;
use kartik\daterange\DateRangePicker;
use common\enums\PayTypeEnum;
use addons\TinyShop\common\enums\OrderTypeEnum;
use addons\TinyShop\common\enums\OrderStatusEnum;
use addons\TinyShop\common\enums\ShippingTypeEnum;
use addons\TinyShop\common\enums\AccessTokenGroupEnum;

$addon = <<< HTML
<div class="input-group-append">
    <span class="input-group-text">
        <i class="fas fa-calendar-alt"></i>
    </span>
</div>
HTML;

$payType = PayTypeEnum::getMap();
unset($payType[0]);

?>

<div class="modal fade" id="query" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <?= Html::beginForm('', 'get') ?>
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">筛选查询</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <div class="col-sm-2 text-right">
                        <label class="control-label" for="cate-pid">搜索方式</label>
                    </div>
                    <div class="col-sm-5">
                        <?= Html::dropDownList('query_type', $search->query_type, [
                            '1' => '订单编号',
                            '2' => '订单交易号',
                            '3' => '收货人姓名',
                            '4' => '收货人手机',
                            '5' => '买家昵称',
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
                <div class="form-group row">
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
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group row">
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
                        <div class="form-group row">
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
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group row">
                            <div class="col-sm-4 text-right">
                                <label class="control-label" for="cate-sort">付款方式</label>
                            </div>
                            <div class="col-sm-8">
                                <?= Html::dropDownList('pay_type', $search->pay_type, $payType, [
                                    'class' => 'form-control',
                                    'prompt' => '请选择',
                                ]) ?>
                                <div class="help-block"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group row">
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
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group row">
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
