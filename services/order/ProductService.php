<?php

namespace addons\TinyShop\services\order;

use addons\TinyShop\common\enums\SubscriptionActionEnum;
use addons\TinyShop\common\models\order\Order;
use common\helpers\StringHelper;
use Yii;
use common\enums\StatusEnum;
use common\components\Service;
use common\helpers\EchantsHelper;
use addons\TinyShop\common\enums\OrderStatusEnum;
use addons\TinyShop\common\enums\RefundStatusEnum;
use addons\TinyShop\common\models\order\OrderProduct;
use addons\TinyShop\common\enums\ExplainStatusEnum;
use yii\web\UnprocessableEntityHttpException;

/**
 * Class ProductService
 * @package addons\TinyShop\services\order
 * @author jianyan74 <751393839@qq.com>
 */
class ProductService extends Service
{
    /**
     * 判断订单内的商品是否正常
     *
     * @param $ids
     * @param $order_id
     * @return int|string
     */
    public function isNormal($ids, $order_id)
    {
        $list = OrderProduct::find()
            ->select(['shipping_status', 'refund_status'])
            ->where(['order_id' => $order_id, 'status' => StatusEnum::ENABLED])
            ->andWhere(['in', 'id', $ids])
            ->asArray()
            ->all();

        foreach ($list as $item) {
            // 已发货
            if ($item['shipping_status'] == StatusEnum::ENABLED) {
                return false;
            }

            // 发起了退款请求
            if (!in_array($item['refund_status'], RefundStatusEnum::deliver())) {
                return false;
            }
        }

        return true;
    }

    /**
     * 获取产品的最多出售数量和价格
     *
     * @param int $num
     * @param string $orderBy
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getMaxCountMoney($type, $num = 30, $orderBy = 'num')
    {
        // 获取时间和格式化
        list($time, $format) = EchantsHelper::getFormatTime($type);
        // 获取数据
        return EchantsHelper::lineGraphic(function ($start_time, $end_time) use ($num, $orderBy) {
            $data = OrderProduct::find()
                ->select(['product_id', "sum($orderBy) as count"])
                ->where(['order_status' => OrderStatusEnum::ACCOMPLISH, 'gift_flag' => StatusEnum::DISABLED])
                ->andWhere(['between', 'created_at', $start_time, $end_time])
                ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
                ->groupBy(['product_id'])
                ->with('product')
                ->orderBy("count asc")
                ->limit($num)
                ->asArray()
                ->all();

            foreach ($data as &$datum) {
                $datum['product']['name'] = StringHelper::textNewLine($datum['product']['name'], 20, 1);
            }

            return [array_column($data, 'count'), array_column(array_column($data, 'product'), 'name')];
        }, $time);
    }

    /**
     * 正常发货
     *
     * @param $ids
     * @param $order_id
     * @throws \yii\web\NotFoundHttpException
     * @throws \yii\web\UnprocessableEntityHttpException
     */
    public function consign($ids, $order_id)
    {
        OrderProduct::updateAll(['shipping_status' => StatusEnum::ENABLED], ['in', 'id', $ids]);

        // 自动更新订单状态
        Yii::$app->tinyShopService->order->autoUpdateStatus($order_id);
    }

    /**
     * 评价
     *
     * @param $order_product_id
     * @return int
     */
    public function evaluate($order_product_id)
    {
        return OrderProduct::updateAll(['is_evaluate' => ExplainStatusEnum::EVALUATE], ['id' => $order_product_id]);
    }

    /**
     * 追加评价
     *
     * @param $order_product_id
     * @return int
     */
    public function superadditionEvaluate($order_product_id)
    {
        return OrderProduct::updateAll(['is_evaluate' => ExplainStatusEnum::SUPERADDITION], ['id' => $order_product_id]);
    }

    /**
     * 获取售后数量
     *
     * @param string $member_id
     * @return false|string|null
     */
    public function getAfterSaleCount($member_id)
    {
        return OrderProduct::find()
            ->select(['count(distinct order_id) as count'])
            ->where(['status' => StatusEnum::ENABLED])
            ->andFilterWhere(['buyer_id' => $member_id])
            ->andWhere(['in', 'refund_status', RefundStatusEnum::refund()])
            ->scalar();
    }

    /**
     * @param $id
     * @return array|\yii\db\ActiveRecord|null|OrderProduct
     */
    public function findById($id)
    {
        return OrderProduct::find()
            ->where(['id' => $id, 'status' => StatusEnum::ENABLED])
            ->one();
    }

    /**
     * @param $id
     * @return array|\yii\db\ActiveRecord|null|OrderProduct
     */
    public function findByIds($ids)
    {
        return OrderProduct::find()
            ->select(['id', 'product_picture', 'product_name', 'sku_name', 'num'])
            ->where(['status' => StatusEnum::ENABLED])
            ->andWhere(['in', 'id', $ids])
            ->asArray()
            ->all();
    }

    /**
     * 获取某个商品的购买数量
     *
     * @param $product_id
     * @param $member_id
     * @return false|string|null
     */
    public function findSumByMember($product_id, $member_id, $marketing_id = '', $marketing_type = '')
    {
        $condition = [];
        if (!empty($marketing_id) && !empty($marketing_type)) {
            $condition = [
                'marketing_id' => $marketing_id,
                'marketing_type' => $marketing_type
            ];
        }

        return OrderProduct::find()
            ->select('sum(num)')
            ->where([
                'product_id' => $product_id,
                'buyer_id' => $member_id,
                'status' => StatusEnum::ENABLED
            ])
            ->andFilterWhere($condition)
            ->andWhere(['in', 'order_status', OrderStatusEnum::haveBought()])
            ->scalar();
    }

    /**
     * 获取订单售后数量
     *
     * @param $order_id
     * @return false|string|null
     */
    public function findAfterSaleCountByOrderId($order_id)
    {
        return OrderProduct::find()
            ->select(['count(id) as count'])
            ->where(['status' => StatusEnum::ENABLED])
            ->andWhere(['order_id' => $order_id])
            ->andWhere(['in', 'refund_status', RefundStatusEnum::refund()])
            ->scalar() ?? 0;
    }

    /**
     * @param $order_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findByOrderId($order_id)
    {
        return OrderProduct::find()
            ->where(['order_id' => $order_id])
            ->asArray()
            ->all();
    }

    /**
     * TODO 判断赠品状态
     *
     * @param $gift_flag
     * @param $member_id
     * @return bool|int|string|null
     */
    public function findCountByGiftFlag($gift_flag, $member_id)
    {
        return OrderProduct::find()
            ->where(['buyer_id' => $member_id, 'gift_flag' => $gift_flag])
            ->andWhere(['>=', 'order_status', OrderStatusEnum::NOT_PAY])
            ->count();
    }
}
