<?php

namespace addons\TinyShop\common\forms;

use Yii;
use yii\base\Model;
use common\interfaces\PayHandler;
use addons\TinyShop\common\enums\OrderStatusEnum;
use addons\TinyShop\common\models\order\OrderProduct;
use addons\TinyShop\common\models\order\Order;

/**
 * Class OrderPayFrom
 * @package addons\TinyShop\common\forms
 * @author jianyan74 <751393839@qq.com>
 */
class OrderPayFrom extends Model implements PayHandler
{
    /**
     * @var
     */
    public $order_id;

    /**
     * @var Order
     */
    protected $order;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            ['order_id', 'required'],
            ['order_id', 'integer', 'min' => 0],
            ['order_id', 'verifyPay'],
        ];
    }

    /**
     * @param $attribute
     * @throws \yii\web\UnprocessableEntityHttpException
     */
    public function verifyPay($attribute)
    {
        empty($this->order) && $this->order = Yii::$app->tinyShopService->order->findById($this->order_id);
        if (!$this->order) {
            $this->addError($attribute, '找不到订单');

            return;
        }

        if ($this->order['order_status'] != OrderStatusEnum::NOT_PAY) {
            $this->addError($attribute, '订单已完成');

            return;
        }

        // 支付前验证库存
        Yii::$app->tinyShopService->productSku->decrRepertory($this->order, $this->order->product, null, false);
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
        return $this->order['pay_money'];
    }

    /**
     * 获取订单号
     *
     * @return float
     */
    public function getOrderSn(): string
    {
        return $this->order['order_sn'];
    }

    /**
     * 交易流水号
     *
     * @return string
     */
    public function getOutTradeNo()
    {
        return $this->order['out_trade_no'];
    }

    /**
     * @return int
     */
    public function getMerchantId(): int
    {
        return $this->order['merchant_id'];
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
