<?php

namespace addons\TinyShop\common\models\forms;

use Yii;
use yii\base\Model;
use common\interfaces\PayHandler;
use common\helpers\ArrayHelper;
use common\helpers\BcHelper;
use addons\TinyShop\common\enums\OrderStatusEnum;
use addons\TinyShop\common\enums\OrderTypeEnum;
use addons\TinyShop\common\models\order\OrderProduct;

/**
 * Class OrderPayFrom
 * @package addons\TinyShop\common\models\forms
 * @author jianyan74 <751393839@qq.com>
 */
class OrderPayFrom extends Model implements PayHandler
{
    /**
     * @var
     */
    public $order_id;

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
        $this->order = Yii::$app->tinyShopService->order->findById($this->order_id);
        if (!$this->order) {
            $this->addError($attribute, '找不到订单');

            return;
        }

        if (!$this->isFinalPayment()) {
            if ($this->order['order_status'] != OrderStatusEnum::NOT_PAY) {
                $this->addError($attribute, '订单已完成');

                return;
            }

            // 支付前验证库存
            /** @var OrderProduct|array $orderProduct */
            $orderProduct = $this->order->product;
            $skuNums = ArrayHelper::map($orderProduct, 'sku_id', 'num');
            Yii::$app->tinyShopService->productSku->decrRepertory($skuNums, false);
        }
    }

    /**
     * 支付说明
     *
     * @return string
     */
    public function getBody(): string
    {
        if ($this->isFinalPayment()) {
            return '订单尾款支付';
        }

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
        if ($this->isFinalPayment()) {
            return $this->order['final_payment_money'];
        }

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
     * 是否查询订单号(避免重复生成)
     *
     * @return bool
     */
    public function isQueryOrderSn(): bool
    {
        if ($this->isFinalPayment()) {
            return false;
        }

        return true;
    }

    /**
     * 预售尾款
     *
     * @return bool
     */
    public function isFinalPayment()
    {
        return false;
    }
}