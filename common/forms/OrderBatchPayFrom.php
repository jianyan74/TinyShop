<?php

namespace addons\TinyShop\common\forms;

use Yii;
use yii\base\Model;
use common\helpers\BcHelper;
use common\interfaces\PayHandler;
use addons\TinyShop\common\enums\OrderStatusEnum;
use addons\TinyShop\common\models\order\Order;
use addons\TinyShop\common\models\order\OrderProduct;

/**
 * Class OrderBatchPayFrom
 * @package addons\TinyShop\common\forms
 */
class OrderBatchPayFrom extends Model implements PayHandler
{
    /**
     * @var
     */
    public $unite_no;

    /**
     * @var Order
     */
    public $orders;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            ['unite_no', 'required'],
            ['unite_no', 'verifyPay'],
        ];
    }

    /**
     * @param $attribute
     * @throws \yii\web\UnprocessableEntityHttpException
     */
    public function verifyPay($attribute)
    {
        $this->orders = Yii::$app->tinyShopService->order->findByUniteNo($this->unite_no);
        if (!$this->orders) {
            $this->addError($attribute, '找不到订单');

            return;
        }

        /** @var Order $order */
        foreach ($this->orders as $order) {
            if ($order['order_status'] != OrderStatusEnum::NOT_PAY) {
                $this->addError($attribute, '订单已完成');

                return;
            }

            // 支付前验证库存
            Yii::$app->tinyShopService->productSku->decrRepertory($order, $order->product, null, false);
        }
    }

    /**
     * 支付说明
     *
     * @return string
     */
    public function getBody(): string
    {
        return '订单支付';
    }

    /**
     * 支付详情
     *
     * @return string
     */
    public function getDetails(): string
    {
        return '';
    }

    /**
     * 支付金额
     *
     * @return float
     */
    public function getTotalFee(): float
    {
        $payMoney = 0;
        /** @var Order $order */
        foreach ($this->orders as $order) {
            $payMoney = BcHelper::add($payMoney, $order->pay_money);
        }

        return $payMoney;
    }

    /**
     * 获取订单号
     *
     * @return float
     */
    public function getOrderSn(): string
    {
        return $this->unite_no;
    }

    /**
     * 交易流水号
     *
     * @return string
     */
    public function getOutTradeNo(): string
    {
        if ($pay = Yii::$app->services->extendPay->findByOrderSn($this->unite_no)) {
            return $pay->unite_no;
        }

        return '';
    }

    /**
     * @return int
     */
    public function getMerchantId(): int
    {
        return 0;
    }

    /**
     * 是否查询订单号(避免重复生成)
     *
     * @return bool
     */
    public function isQueryOrderSn(): bool
    {
        return true;
    }
}
