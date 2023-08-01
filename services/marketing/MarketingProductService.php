<?php

namespace addons\TinyShop\services\marketing;

use Yii;
use yii\helpers\Json;
use yii\web\UnprocessableEntityHttpException;
use common\components\Service;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use addons\TinyShop\common\enums\MarketingEnum;
use addons\TinyShop\common\models\marketing\MarketingProduct;
use addons\TinyShop\common\models\marketing\MarketingProductSku;

/**
 * Class MarketingProductService
 * @package addons\TinyShop\services\marketing
 * @author jianyan74 <751393839@qq.com>
 */
class MarketingProductService extends Service
{
    /**
     * @param $marketing_id
     * @param $marketing_type
     * @param $status
     */
    public function updateStatus($marketing_id, $marketing_type, $status)
    {
        MarketingProduct::updateAll(['status' => $status], ['marketing_id' => $marketing_id, 'marketing_type' => $marketing_type]);
        MarketingProductSku::updateAll(['status' => $status], ['marketing_id' => $marketing_id, 'marketing_type' => $marketing_type]);
    }

    /**
     * 删除
     *
     * @param $marketing_id
     * @param array|string $marketing_type
     */
    public function delByMarketing($marketing_id, $marketing_type)
    {
        if (is_array($marketing_type)) {
            MarketingProduct::deleteAll([
                'and',
                ['marketing_id' => $marketing_id],
                ['in', 'marketing_type', $marketing_type],
            ]);
        } else {
            MarketingProduct::deleteAll(['marketing_id' => $marketing_id, 'marketing_type' => $marketing_type]);
        }
    }

    /**
     * 下架参与营销的商品
     *
     * @param $product_id
     * @param $all
     * @return void
     */
    public function loseByProductId($product_id, $all = false)
    {
        if ($all == true) {
            MarketingProduct::updateAll(['status' => StatusEnum::DISABLED], ['product_id' => $product_id]);
            MarketingProductSku::updateAll(['status' => StatusEnum::DISABLED], ['product_id' => $product_id]);

            return;
        }

        $condition = [
            'and',
            ['product_id' => $product_id],
            ['>', 'end_time', time()],
            [
                'in',
                'marketing_type',
                [
                    MarketingEnum::BARGAIN,
                    MarketingEnum::PRE_SELL,
                    MarketingEnum::DISCOUNT,
                    MarketingEnum::SEC_KILL,
                    MarketingEnum::GROUP_BUY,
                    MarketingEnum::WHOLESALE
                ]
            ],
        ];

        // 涉及营销规格的下架
        MarketingProduct::updateAll(['status' => StatusEnum::DISABLED], $condition);
        MarketingProductSku::updateAll(['status' => StatusEnum::DISABLED], $condition);
    }

    /**
     * 验证营销
     *
     * @param $product_ids
     * @param $marketing_id
     * @param $marketing_type
     * @param $start_time
     * @param $end_time
     * @param $status
     * @return bool|void
     * @throws UnprocessableEntityHttpException
     */
    public function verifyMarketing($product_ids, $marketing_id, $marketing_type, $prediction_time, $end_time, $status)
    {
        if (in_array($status, [StatusEnum::DELETE, StatusEnum::DISABLED]) || empty($product_ids)) {
            return true;
        }

        // 不允许参加会员折扣
        if (
            in_array($marketing_type, MarketingEnum::notMemberDiscount()) &&
            !empty($product_ids) &&
            ($products = Yii::$app->tinyShopService->product->findByIds($product_ids, ['id', 'name', 'is_member_discount']))
        ) {
            foreach ($products as $product) {
                if ($product['is_member_discount'] == StatusEnum::ENABLED) {
                    throw new UnprocessableEntityHttpException($product['name'] . ' 已参加会员折扣');
                }
            }
        }

        // 查找是否已存在营销
        if ($timeManage = $this->findUnderwayActivityByProductIds($product_ids, $prediction_time, $end_time)) {
            foreach ($timeManage as $key => $value) {
                // 判断当前的 ID 是否一致
                if ($value['marketing_id'] == $marketing_id && $value['marketing_type'] == $marketing_type) {
                    continue;
                }

                // 满减送跳过
                if (
                    $value['marketing_type'] == MarketingEnum::FULL_GIVE ||
                    $value['marketing_type'] == MarketingEnum::COUPON
                ) {
                    continue;
                }

                if ($value['marketing_type'] != $marketing_type) {
                    throw new UnprocessableEntityHttpException($value['product']['name'] . ' 在该时间段已存在' . MarketingEnum::getValue($value['marketing_type']));
                } elseif ($value['prediction_time'] != $prediction_time && $value['end_time'] != $end_time) {
                    throw new UnprocessableEntityHttpException($value['product']['name'] . ' 在该时间段已存在' . MarketingEnum::getValue($value['marketing_type']));
                }
            }
        }
    }

    /**
     * @param $product_ids
     * @return array
     */
    public function getMarketingType($product_ids)
    {
        $marketing = $this->findUnderwayActivityByProductIds($product_ids, time(), time());

        $data = [];
        foreach ($marketing as $item) {
            !isset($data[$item['product_id']]) && $data[$item['product_id']] = [];
            $data[$item['product_id']][] = MarketingEnum::getValue($item['marketing_type']);
        }

        return $data;
    }

    /**
     * 查询活动中的营销的商品
     *
     * @param $product_ids
     * @param $start_time
     * @param $end_time
     * @param string $marketing_type
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findUnderwayActivityByProductIds($product_ids, $start_time, $end_time)
    {
        // 查询开始时间是否在已有的营销时间内
        return MarketingProduct::find()
            ->where(['in', 'product_id', $product_ids])
            ->andWhere([
                'or',
                ['between', 'start_time', $start_time, $end_time],
                ['between', 'end_time', $start_time, $end_time],
                [
                    'and',
                    ['<=', 'start_time', $start_time],
                    ['>=', 'end_time', $end_time],
                ]
            ])
            ->andWhere(['status' => StatusEnum::ENABLED])
            ->andWhere(['is_template' => StatusEnum::DISABLED])
            ->with(['product'])
            ->asArray()
            ->all();
    }

    /**
     * @param $product_id
     * @param $marketing_id
     * @param $marketing_type
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findOneByProductId($product_id, $marketing_id, $marketing_type, $marketing_product_id = '')
    {
        return MarketingProduct::find()
            ->where([
                'product_id' => $product_id,
                'marketing_id' => $marketing_id,
                'marketing_type' => $marketing_type,
                'is_template' => 0,
            ])
            ->andWhere(['status' => StatusEnum::ENABLED])
            ->andFilterWhere(['id' => $marketing_product_id])
            ->with(['sku'])
            ->asArray()
            ->one();
    }

    /**
     * @param $marketing_id
     * @param $marketing_type
     * @param int $is_template
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findByMarketing($marketing_id, $marketing_type, $is_template = 0, $with = ['product.sku', 'sku'])
    {
        return MarketingProduct::find()
            ->where([
                'marketing_id' => $marketing_id,
                'marketing_type' => $marketing_type,
                'is_template' => $is_template,
                'status' => StatusEnum::ENABLED
            ])
            ->with($with)
            ->asArray()
            ->all();
    }

    /**
     * @param $marketing_id
     * @param $marketing_type
     * @return array
     */
    public function findProductIdsByMarketing($marketing_id, $marketing_type)
    {
        return MarketingProduct::find()
            ->select(['product_id'])
            ->where(['marketing_id' => $marketing_id, 'marketing_type' => $marketing_type])
            ->asArray()
            ->column();
    }

    /**
     * @param $marketing_id
     * @param $marketing_type
     * @return array
     */
    public function findByMarketingIdAndTypeWithSku($marketing_id, $marketing_type)
    {
        return MarketingProduct::find()
            ->where(['marketing_id' => $marketing_id, 'marketing_type' => $marketing_type])
            ->with(['sku'])
            ->asArray()
            ->all();
    }

    /**
     * @param $marketing_id
     * @param $product_ids
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findAllByFullGive($product_ids)
    {
        return MarketingProduct::find()
            ->where(['marketing_type' => MarketingEnum::FULL_GIVE])
            ->andWhere(['in', 'product_id', $product_ids])
            ->andWhere(['<=', 'prediction_time', time()])
            ->andWhere(['>=', 'end_time', time()])
            ->andWhere(['status' => StatusEnum::ENABLED])
            ->with(['fullGive'])
            ->asArray()
            ->all();
    }

    /**
     * @param $product_id
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findByFullGive($product_id)
    {
        return MarketingProduct::find()
            ->where([
                'marketing_type' => MarketingEnum::FULL_GIVE,
                'product_id' => $product_id
            ])
            ->andWhere(['<=', 'start_time', time()])
            ->andWhere(['>=', 'end_time', time()])
            ->andWhere(['status' => StatusEnum::ENABLED])
            ->with(['fullGive'])
            ->asArray()
            ->one();
    }

    /**
     * @param $product_id
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findByProductId($product_id)
    {
        // 活动中
        return MarketingProduct::find()
            ->where([
                'product_id' => $product_id,
                'status' => StatusEnum::ENABLED
            ])
            ->andWhere(['>', 'end_time', time()])
            ->andWhere([
                'in',
                'marketing_type',
                MarketingEnum::notMemberDiscount()
            ])
            ->asArray()
            ->one();
    }

    /**
     * @param $product_ids
     * @param $marketing_id
     * @param $marketing_type
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findGiftByMarketingIds($marketing_ids)
    {
        return MarketingProduct::find()
            ->where(['in', 'marketing_id', $marketing_ids])
            ->andWhere([
                'marketing_type' => MarketingEnum::GIFT,
                'status' => StatusEnum::ENABLED
            ])
            ->with(['product.firstSku', 'gift'])
            ->asArray()
            ->all();
    }

    /**
     * @param $product_ids
     * @param $marketing_id
     * @param $marketing_type
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findPlusBuyByProductIds($product_ids)
    {
        return MarketingProduct::find()
            ->select(['marketing_id'])
            ->where(['in', 'product_id', $product_ids])
            ->andWhere(['<', 'start_time', time()])
            ->andWhere(['>', 'end_time', time()])
            ->andWhere([
                'marketing_type' => MarketingEnum::PLUS_BUY_JOIN,
                'status' => StatusEnum::ENABLED
            ])
            ->asArray()
            ->column();
    }

    /**
     * @param $product_id
     * @param $merchant_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getCanReceiveCouponByProductId($product_id, $merchant_id)
    {
        return MarketingProduct::find()
            ->select(['marketing_id', 'marketing_type'])
            ->where(['product_id' => $product_id])
            ->andWhere(['in','merchant_id', [0, $merchant_id]])
            ->andWhere(['in', 'marketing_type', [MarketingEnum::COUPON_IN, MarketingEnum::COUPON_NOT_IN]])
            ->andWhere(['<', 'start_time', time()])
            ->andWhere(['>', 'end_time', time()])
            ->andWhere(['status' => StatusEnum::ENABLED])
            ->asArray()
            ->all();
    }

    /**
     * 组合套餐
     *
     * @param $product_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findCombinationByProductId($product_id)
    {
        $data = MarketingProduct::find()
            ->where(['product_id' => $product_id])
            ->andWhere([
                'marketing_type' => MarketingEnum::COMBINATION,
                'status' => StatusEnum::ENABLED
            ])
            ->with(['combination.combinationProduct.product'])
            ->asArray()
            ->all();

        $newData = [];
        foreach ($data as $datum) {
            $newData[] = [
                'id' => $datum['combination']['id'],
                'merchant_id' => $datum['combination']['merchant_id'],
                'title' => $datum['combination']['title'],
                'price' => $datum['combination']['price'],
                'original_price' => $datum['combination']['original_price'],
                'save_the_price' => $datum['combination']['save_the_price'],
                'product' => ArrayHelper::getColumn($datum['combination']['combinationProduct'], 'product'),
            ];
        }

        return $newData;
    }

    /**
     * 重组
     *
     * @param $marketing_id
     * @param $marketing_type
     * @param int $is_template
     * @return array
     */
    public function regroup($marketing_id, $marketing_type, $is_template = 0)
    {
        $marketingProducts = $this->findByMarketing($marketing_id, $marketing_type, $is_template);
        $data = [];
        foreach ($marketingProducts as $marketingProduct) {
            $product = $marketingProduct['product'];
            if (empty($product)) {
                continue;
            }

            unset($marketingProduct['product']);
            $product['tags'] = !empty($product['tags']) ? Json::decode($product['tags']) : [];
            $product['number'] = $marketingProduct['number'];
            $product['max_buy'] = $marketingProduct['max_buy'];
            $product['min_buy'] = $marketingProduct['min_buy'];
            $product['discount'] = $marketingProduct['discount'];
            $product['discount_type'] = $marketingProduct['discount_type'];
            $product['marketing_sales'] = $marketingProduct['marketing_sales'];
            $product['marketing_stock'] = $marketingProduct['marketing_stock'] ?? 0;
            $product['marketing_total_stock'] = $marketingProduct['marketing_total_stock'] ?? 0;
            $product['marketing_data'] = Json::decode($marketingProduct['marketing_data']);

            // 匹配 SKU
            $tmpSku = ArrayHelper::arrayKey($marketingProduct['sku'], 'sku_id');
            foreach ($product['sku'] as &$sku) {
                $skuId = $sku['id'];
                if (isset($tmpSku[$skuId])) {
                    $sku['number'] = $tmpSku[$skuId]['number'];
                    $sku['max_buy'] = $tmpSku[$skuId]['max_buy'];
                    $sku['min_buy'] = $tmpSku[$skuId]['min_buy'];
                    $sku['marketing_sales'] = $tmpSku[$skuId]['marketing_sales'];
                    $sku['marketing_stock'] = $tmpSku[$skuId]['marketing_stock'];
                    $sku['discount'] = $tmpSku[$skuId]['discount'];
                    $sku['discount_type'] = $tmpSku[$skuId]['discount_type'];
                    $sku['discount_type'] = $tmpSku[$skuId]['discount_type'];
                    $sku['marketing_data'] = Json::decode($tmpSku[$skuId]['marketing_data']);
                }
            }

            $data[] = $product;
        }

        return $data;
    }
}
