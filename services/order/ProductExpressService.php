<?php

namespace addons\TinyShop\services\order;

use Yii;
use yii\helpers\Json;
use common\components\Service;
use common\enums\StatusEnum;
use common\helpers\StringHelper;
use addons\TinyShop\common\models\order\ProductExpress;
use addons\TinyShop\common\enums\ProductExpressShippingTypeEnum;

/**
 * Class ProductExpressService
 * @package addons\TinyShop\services\order
 * @author jianyan74 <751393839@qq.com>
 */
class ProductExpressService extends Service
{
    /**
     * 获取物流追踪状态
     *
     * @param $order_id
     * @return array
     */
    public function getStatusByOrderId($order_id, $member_id)
    {
        $data = $this->findByOrderIdAndMemberId($order_id, $member_id);
        $order = Yii::$app->tinyShopService->order->findById($order_id);
        $address = [
            'receiver_realname' => $order['receiver_realname'],
            'receiver_name' => $order['receiver_name'],
            'receiver_mobile' => StringHelper::hideStr($order['receiver_mobile'], 3),
            'receiver_details' => $order['receiver_details'],
            'receiver_zip' => $order['receiver_zip'],
            'receiver_longitude' => $order['receiver_longitude'],
            'receiver_latitude' => $order['receiver_latitude'],
        ];

        foreach ($data as &$record) {
            !is_array($record['order_product_ids']) && $record['order_product_ids'] = Json::decode($record['order_product_ids']);
            $record['address'] = $address;
            $record['order_product'] = Yii::$app->tinyShopService->orderProduct->findByIds($record['order_product_ids']);
            //物流追踪
            $record['trace'] = [];
            // 需要物流
            if ($record['shipping_type'] == ProductExpressShippingTypeEnum::LOGISTICS) {
                $record['trace'] = Yii::$app->services->extendLogistics->query($record['express_no'], $record['express_company'], $record['buyer_mobile'], true);
            }
        }

        return [
            'count' => count($data),
            'data' => $data,
        ];
    }

    /**
     * 重置订单商品获取快递号
     *
     * @param $product
     * @param $order_id
     * @return mixed
     */
    public function regroupProduct($product, $order_id)
    {
        $list = $this->findByOrderId($order_id);

        foreach ($product as &$item) {
            $item['express'] = '';

            foreach ($list as $record) {
                if (in_array($item['id'], $record['order_product_ids'])) {
                    $item['express'] = $record['express_company'] . ' | ' .  $record['express_no'];

                    if ($record['shipping_type'] == ProductExpressShippingTypeEnum::NOT_LOGISTICS) {
                        $item['express'] = '无需物流';
                    }
                }
            }
        }

        return $product;
    }

    /**
     * @param $order_id
     * @param $buyer_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findByOrderIdAndMemberId($order_id, $buyer_id)
    {
        return ProductExpress::find()
            ->where([
                'order_id' => $order_id,
                'buyer_id' => $buyer_id,
                'status' => StatusEnum::ENABLED
            ])
            ->asArray()
            ->all();
    }

    /**
     * @param $order_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findByOrderId($order_id)
    {
        return ProductExpress::find()
            ->where(['order_id' => $order_id, 'status' => StatusEnum::ENABLED])
            ->all();
    }
}
