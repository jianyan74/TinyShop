<?php

namespace addons\TinyShop\services\order;

use Yii;
use common\components\Service;
use common\helpers\ArrayHelper;
use common\forms\CreditsLogForm;
use addons\TinyShop\common\enums\OrderStatusEnum;
use addons\TinyShop\common\enums\OrderTypeEnum;
use addons\TinyShop\common\models\order\Order;
use addons\TinyShop\common\enums\CouponGetTypeEnum;
use addons\TinyShop\common\enums\MarketingEnum;

/**
 * Class OrderBatchService
 * @package addons\TinyShop\services\order
 * @author jianyan74 <751393839@qq.com>
 */
class OrderBatchService extends Service
{
    /**
     * 自动签收
     *
     * @return void
     */
    public function signAll()
    {
        $orderIds = Order::find()
            ->select('id')
            ->where(['order_status' => OrderStatusEnum::SHIPMENTS])
            ->andWhere(['>', 'auto_sign_time', 0])
            ->andWhere(['<', 'auto_sign_time', time()])
            ->column();

        try {
            foreach ($orderIds as $id) {
                Yii::$app->tinyShopService->order->takeDelivery($id);
            }
        } catch (\Exception $e) {

        }
    }

    /**
     * 批量发货
     *
     * @param $setting
     * @param $orders
     * @return void
     * @throws \yii\base\InvalidConfigException
     */
    public function finalizeAll($setting, $orders = [])
    {
        if (empty($orders)) {
            $orders = Order::find()
                ->where(['order_status' => OrderStatusEnum::SING])
                ->andWhere(['in', 'order_type', OrderTypeEnum::normal()])
                ->andWhere(['<=', 'auto_finish_time', time()])
                ->with(['marketingDetail', 'member'])
                ->all();
        }

        try {
            // 赠送优惠券列表
            $giveCouponTypeMap = [];
            foreach ($orders as $order) {
                // 赠送营销判断
                foreach ($order->marketingDetail as $marketingDetail) {
                    if (empty($marketingDetail['give_coupon_type'])) {
                        continue;
                    }

                    foreach ($marketingDetail['give_coupon_type'] as $giveCouponType) {
                        if (!isset($giveCouponTypeMap[$giveCouponType['id']])) {
                            $giveCouponTypeMap[$giveCouponType['id']] = 0;
                        }

                        $giveCouponTypeMap[$giveCouponType['id']] += $giveCouponType['number'];
                    }
                }
            }

            $marketingCouponTypes = [];
            if (!empty($giveCouponTypeMap)) {
                $marketingCouponTypes = Yii::$app->tinyShopService->marketingCouponType->findByIds(array_keys($giveCouponTypeMap));
                $marketingCouponTypes = ArrayHelper::arrayKey($marketingCouponTypes, 'id');
            }

            // 会员折扣优惠
            $memberDiscountMoney = 0;
            /** @var Order $order */
            foreach ($orders as $order) {
                // 赠送优惠券和会员节省
                foreach ($order->marketingDetail as $value) {
                    if (!empty($value['give_coupon_type'])) {
                        foreach ($value['give_coupon_type'] as $giveCouponType) {
                            if (isset($marketingCouponTypes[$giveCouponType['id']])) {
                                Yii::$app->tinyShopService->marketingCoupon->giveByNewRecord(
                                    $marketingCouponTypes[$giveCouponType['id']],
                                    $order->buyer_id,
                                    $order->id,
                                    CouponGetTypeEnum::ORDER,
                                    $giveCouponTypeMap[$giveCouponType['id']]
                                );
                            }
                        }
                    }

                    // 会员折扣优惠
                    if ($value['marketing_type'] == MarketingEnum::MEMBER_DISCOUNT && $value['discount_money'] > 0) {
                        $memberDiscountMoney += $value['discount_money'];
                    }
                }

                Yii::$app->tinyShopService->order->finalize($order, $setting);

                // 加入节省
                if ($memberDiscountMoney > 0) {
                    Yii::$app->services->memberCreditsLog->incrEconomizeMoney(new CreditsLogForm([
                        'member' => $order->member,
                        'num' => $memberDiscountMoney,
                        'group' => 'order',
                        'map_id' => $order->id,
                        'remark' => '订单-' . $order->order_sn,
                    ]));
                }

                // 记录操作
                Yii::$app->services->actionLog->create('order', '自动完成', $order->id);
            }

        } catch (\Exception $e) {
            // 记录行为日志
            Yii::$app->services->log->push(500, 'tinyShopFinalizeAll', Yii::$app->services->base->getErrorInfo($e));
        }
    }

    /**
     * @return void
     * @throws \yii\base\InvalidConfigException
     */
    public function closeAll()
    {
        $orderIds = Order::find()
            ->select('id')
            ->where(['order_status' => OrderStatusEnum::NOT_PAY])
            ->andWhere(['<=', 'close_time', time()])
            ->column();

        try {
            foreach ($orderIds as $id) {
                Yii::$app->tinyShopService->order->close($id);
                // 记录操作
                Yii::$app->services->actionLog->create('order', '自动关闭订单', $id);
            }
        } catch (\Exception $e) {
            // 记录行为日志
            Yii::$app->services->log->push(500, 'tinyShopCloseAll', Yii::$app->services->base->getErrorInfo($e));
        }
    }
}
