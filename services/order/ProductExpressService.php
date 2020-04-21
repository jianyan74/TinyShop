<?php

namespace addons\TinyShop\services\order;

use Yii;
use common\components\Service;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use addons\TinyShop\common\models\order\ProductExpress;
use yii\helpers\Json;

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

        foreach ($data as &$record) {
            !is_array($record['order_product_ids']) && $record['order_product_ids'] = Json::decode($record['order_product_ids']);
            $record['order_product'] = Yii::$app->tinyShopService->orderProduct->findByIds($record['order_product_ids']);
            //物流追踪
            $record['trace'] = [];
            // 需要物流
            if ($record['shipping_type'] == ProductExpress::SHIPPING_TYPE_LOGISTICS) {
                try {
                    if (!empty($record['express_no'])) {
                        // aliyun(阿里云)、juhe(聚合)、kdniao(快递鸟)、kd100(快递100)
                        $logistics = Yii::$app->logistics->aliyun($record['express_no'], null, true);
                        $record['trace'] = $logistics->getList();
                    }
                } catch (\Exception $e) {
                    Yii::debug($e->getMessage());
                }
            }
        }

        return [
            'count' => count($data),
            'data' => $data,
        ];
    }

    /**
     * 重置订单产品获取快递号
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

                    if ($record['shipping_type'] == ProductExpress::SHIPPING_TYPE_NOT_LOGISTICS) {
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