<?php

namespace addons\TinyShop\services\product;

use Yii;
use common\components\Service;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use common\models\member\Member;
use addons\TinyShop\common\models\order\Order;
use addons\TinyDistribution\common\models\forms\PromoterRecordForm;
use addons\TinyShop\common\models\product\CommissionRate;

/**
 * Class CommissionRateService
 * @package addons\TinyShop\services\product
 * @author jianyan74 <751393839@qq.com>
 */
class CommissionRateService extends Service
{
    /**
     * 返回模型
     *
     * @param $id
     * @return CommissionRate|array|\yii\db\ActiveRecord|null
     */
    public function findModelByProductId($product_id)
    {
        if (empty($product_id) || empty(($model = CommissionRate::find()->where(['product_id' => $product_id])->andFilterWhere(['merchant_id' => $this->getMerchantId()])->one()))) {
            $model = new CommissionRate();
            $model->product_id = $product_id;
            $model->merchant_id = $this->getMerchantId();
            $model->loadDefaultValues();
        }

        return $model;
    }

    /**
     * @param $product_ids
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findByProductIds($product_ids)
    {
        return CommissionRate::find()
            ->where(['in', 'product_id', $product_ids])
            ->andWhere(['status' => StatusEnum::ENABLED])
            ->asArray()
            ->all();
    }

    /**
     * @param $commission
     * @throws \yii\web\NotFoundHttpException
     * @throws \yii\web\UnprocessableEntityHttpException
     */
    public function createDistribute($commission, Order $order, Member $member)
    {
        $productIds = ArrayHelper::getColumn($commission, 'product_id');
        $commissionRate = Yii::$app->tinyShopService->productCommissionRate->findByProductIds($productIds);
        $commissionRate = ArrayHelper::arrayKey($commissionRate, 'product_id');

        foreach ($commission as $item) {
            $product_id = $item['product_id'];
            if (isset($commissionRate[$product_id])) {
                Yii::$app->tinyDistributionService->promoterRecord->promoter(new PromoterRecordForm([
                    'member' => $member, // 用户信息
                    'map_type' => 'order', // 组别
                    'remark' => $item['remark'], // 备注
                    'map_money' => $item['map_money'], // 金额
                    'map_id' => $item['map_id'], // 产品id
                    'map_sn' => $item['map_sn'], // 订单号
                    'map_cost' => $item['map_cost'], // 订单号
                    'map_return' => $item['map_return'], // 订单号
                    'pay_type' => $item['pay_type'], // 支付类型
                    'promoter_ratio' => $commissionRate[$product_id]['distribution_commission_rate'], // 三级分销比例 1-100
                    'province_id' => $order->receiver_province, // 省id
                    'city_id' => $order->receiver_city, // 市id
                    'area_id' => $order->receiver_area, // 区id
                    'regional_agent_ratio' => $commissionRate[$product_id]['regionagent_commission_rate'], // 区域代理分销比例1-100
                ]));
            }
        }
    }

    /**
     * @param $product_id
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findByProductId($product_id)
    {
        return CommissionRate::find()
            ->where(['product_id' => $product_id])
            ->asArray()
            ->one();
    }
}