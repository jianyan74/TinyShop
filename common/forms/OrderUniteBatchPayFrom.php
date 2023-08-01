<?php

namespace addons\TinyShop\common\forms;

use Yii;
use common\interfaces\PayHandler;
use common\helpers\BcHelper;
use yii\web\UnprocessableEntityHttpException;

/**
 * 订单混合批量支付
 *
 * Class OrderUniteBatchPayFrom
 * @package addons\TinyShop\common\forms
 * @author jianyan74 <751393839@qq.com>
 */
class OrderUniteBatchPayFrom extends OrderBatchPayFrom implements PayHandler
{
    /**
     * 支付金额
     *
     * @return float
     */
    public function getTotalFee(): float
    {
        $payMoney = parent::getTotalFee();

        $member_id = Yii::$app->user->identity->member_id;
        $account = Yii::$app->services->memberAccount->findByMemberId($member_id);

        if ($account->user_money >= $payMoney) {
            throw new UnprocessableEntityHttpException('请直接使用余额支付');
        }

        return BcHelper::sub($payMoney, $account->user_money);
    }
}
