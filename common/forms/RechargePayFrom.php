<?php

namespace addons\TinyShop\common\forms;

use Yii;
use yii\base\Model;
use common\helpers\StringHelper;
use common\interfaces\PayHandler;
use addons\TinyShop\common\models\order\Recharge;

/**
 * Class RechargePayFrom
 * @package addons\TinyShop\common\forms
 * @author jianyan74 <751393839@qq.com>
 */
class RechargePayFrom extends Model implements PayHandler
{
    /**
     * @var
     */
    public $money;

    /**
     * 充值订单
     *
     * @var Recharge
     */
    protected $order;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            ['money', 'required'],
            ['money', 'number', 'min' => 0.01],
            ['money', 'verifyPay'],
        ];
    }

    /**
     * @param $attribute
     * @throws \yii\web\UnprocessableEntityHttpException
     */
    public function verifyPay($attribute)
    {
        $model = new Recharge();
        $model = $model->loadDefaultValues();
        $model->price = $this->money;
        // 充值赠送
        if (!empty($rechargeConfig = Yii::$app->tinyShopService->marketingRechargeConfig->getGiveMoney($this->money))) {
            $model->give_price = $rechargeConfig->give_price;
            $model->give_point = $rechargeConfig->give_point;
            $model->give_growth = $rechargeConfig->give_growth;
            $model->give_coupon_type_ids = [];
        }

        $model->member_id = Yii::$app->user->identity->member_id;
        $model->order_sn = time() . StringHelper::random(8, true);
        $model->out_trade_no = date('YmdHis') . StringHelper::random(8, true);
        $model->save();

        $this->order = $model;
    }

    /**
     * 支付说明
     *
     * @return string
     */
    public function getBody(): string
    {
        return '在线充值';
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
        return $this->money;
    }

    /**
     * @return int
     */
    public function getMerchantId(): int
    {
        return Yii::$app->services->merchant->getNotNullId();
    }

    /**
     * 获取订单号
     *
     * @return float
     */
    public function getOrderSn(): string
    {
        return $this->order->order_sn;
    }

    /**
     * 交易流水号
     *
     * @return string
     */
    public function getOutTradeNo()
    {
        return $this->order->out_trade_no;
    }

    /**
     * 是否查询订单号(避免重复生成)
     *
     * @return bool
     */
    public function isQueryOrderSn(): bool
    {
        return false;
    }
}
