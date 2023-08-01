<?php

namespace addons\TinyShop\common\forms;

use Yii;
use common\interfaces\PayHandler;
use common\helpers\BcHelper;
use yii\web\UnprocessableEntityHttpException;

/**
 * 订单混合支付
 *
 * 余额+第三方(支付宝、微信等)
 *
 * Class OrderUnitePayFrom
 * @package addons\TinyShop\common\forms
 * @author jianyan74 <751393839@qq.com>
 */
class OrderUnitePayFrom extends OrderPayFrom implements PayHandler
{
    /**
     * 支付金额
     *
     * @return float
     */
    public function getTotalFee(): float
    {
        $account = Yii::$app->services->memberAccount->findByMemberId($this->order['buyer_id']);

        // 正常支付
        if ($account->user_money >= $this->order['pay_money']) {
            throw new UnprocessableEntityHttpException('请直接使用余额支付');
        }

        return BcHelper::sub($this->order['pay_money'], $account->user_money);
    }
}
