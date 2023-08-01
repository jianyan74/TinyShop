<?php

namespace addons\TinyShop\services\marketing;

use Yii;
use yii\db\ActiveQuery;
use yii\web\UnprocessableEntityHttpException;
use common\components\Service;
use common\enums\StatusEnum;
use addons\TinyShop\common\enums\DiscountTypeEnum;
use addons\TinyShop\common\enums\MarketingEnum;
use addons\TinyShop\common\models\marketing\CouponType;
use addons\TinyShop\common\enums\RangeTypeEnum;

/**
 * Class CouponTypeService
 * @package addons\TinyShop\services\marketing
 */
class CouponTypeService extends Service
{
    /**
     * 重组显示
     *
     * @param $model
     * @return mixed
     */
    public function regroupShow($model)
    {
        // 是否可领取
        $model['is_get'] = StatusEnum::ENABLED;
        if (isset($model['myGet']['count']) && $model['myGet']['count'] >= $model['max_fetch'] && $model['max_fetch'] > 0) {
            $model['is_get'] = StatusEnum::DISABLED;
        }
        !isset($model['myGet']) && $model['myGet'] = null;

        // 计算折扣
        if ($model['count'] == 0) {
            $model['percentage'] = 0;
            $model['is_get'] = StatusEnum::DISABLED;
        } elseif ($model['get_count'] == 0) {
            $model['percentage'] = 100;
        } elseif ($model['get_count'] > 0) {
            $model['percentage'] = 100 - floor($model['get_count'] / $model['count'] * 100);
        }

        $model['percentage'] == 0 && $model['is_get'] = StatusEnum::DISABLED;

        return $model;
    }

    /**
     * 扣减/返还优惠券
     *
     * @param CouponType $couponType
     * @param int $num
     * @throws UnprocessableEntityHttpException
     */
    public function deductionRepertory(CouponType $couponType, $num = 1)
    {
        $couponType->get_count += $num;
        if ($couponType->get_count > $couponType->count) {
            throw new UnprocessableEntityHttpException('优惠券剩余数量不足');
        }

        // 更新数量
        CouponType::updateAll(['get_count' => $couponType->get_count], ['id' => $couponType->id]);
    }

    /**
     * 可领取优惠券
     *
     * @param $product_id
     */
    public function getCanReceiveCouponByProductId($product_id, $cate_ids, $member_id, $merchant_id)
    {
        $with = [];
        if (!empty($member_id)) {
            $with = [
                'myGet' => function (ActiveQuery $query) use ($member_id) {
                    return $query->andWhere(['member_id' => $member_id]);
                },
            ];
        }

        $condition = [
            'or',
            ['range_type' => RangeTypeEnum::ALL],
        ];

        /********************* 可用商品 *********************/
        $couponTypesByProduct = Yii::$app->tinyShopService->marketingProduct->getCanReceiveCouponByProductId($product_id, $merchant_id);
        $inIds = $notInIds = [];
        foreach ($couponTypesByProduct as $value) {
            if ($value['marketing_type'] == MarketingEnum::COUPON_IN) {
                $inIds[] = $value['marketing_id'];
            } else {
                $notInIds[] = $value['marketing_id'];
            }
        }

        // 可用商品
        !empty($inIds) && $condition[] = [
            'and',
            ['range_type' => RangeTypeEnum::ASSIGN_PRODUCT],
            ['in', 'id', $inIds],
        ];
        // 不可用商品
        if (!empty($notInIds)) {
            $condition[] = [
                'and',
                ['range_type' => RangeTypeEnum::NOT_ASSIGN_PRODUCT],
                ['not in', 'id', $notInIds]
            ];
        } else {
            $condition[] = ['range_type' => RangeTypeEnum::NOT_ASSIGN_PRODUCT];
        }

        $cateByProduct = Yii::$app->tinyShopService->marketingCate->getCanReceiveCouponByProductId($cate_ids, $merchant_id);

        /********************* 可用分类 *********************/
        $inIds = $notInIds = [];
        foreach ($cateByProduct as $item) {
            if ($item['marketing_type'] == MarketingEnum::COUPON_IN) {
                $inIds[] = $item['marketing_id'];
            } else {
                $notInIds[] = $item['marketing_id'];
            }
        }

        // 可用分类
        !empty($inIds) && $condition[] = [
            'and',
            ['range_type' => RangeTypeEnum::ASSIGN_CATE],
            ['in', 'id', $inIds],
        ];
        // 不可用分类
        if (!empty($notInIds)) {
            $condition[] = [
                'and',
                ['range_type' => RangeTypeEnum::NOT_ASSIGN_CATE],
                ['not in', 'id', $notInIds]
            ];
        } else {
            $condition[] = ['range_type' => RangeTypeEnum::NOT_ASSIGN_CATE];
        }

        return CouponType::find()
            ->where(['status' => StatusEnum::ENABLED])
            ->andWhere(['<', 'get_start_time', time()])
            ->andWhere(['>', 'get_end_time', time()])
            ->andWhere(['is_list_visible' => StatusEnum::ENABLED])
            ->andWhere(['in','merchant_id', [0, $merchant_id]])
            ->andWhere($condition)
            ->orderBy('get_start_time desc, merchant_id asc, range_type asc')
            ->with($with)
            ->limit(10)
            ->asArray()
            ->all();
    }

    /**
     * 自定义领取优惠券
     *
     * @param $product_id
     */
    public function findByCustom($merchant_id = '', $limit = 10, $couponTypeIds = [])
    {
        !empty($couponTypeIds) && $limit = count($couponTypeIds);

        $data = CouponType::find()
            ->select([
                'id',
                'merchant_id',
                'title',
                'count',
                'at_least',
                'discount',
                'discount_type',
                'range_type',
                'start_time',
                'end_time',
                'term_of_validity_type',
                'fixed_term',
            ])
            ->where(['status' => StatusEnum::ENABLED])
            ->andFilterWhere(['in', 'id', $couponTypeIds])
            ->andWhere(['range_type' => RangeTypeEnum::ALL])
            ->andWhere(['<', 'get_start_time', time()])
            ->andWhere(['>', 'get_end_time', time()])
            ->andFilterWhere(['merchant_id' => $merchant_id])
            ->orWhere([
                'and',
                ['<', 'get_start_time', time()],
                ['>', 'get_end_time', time()],
                ['status' => StatusEnum::ENABLED]
            ])
            ->limit($limit)
            ->asArray()
            ->all();

        foreach ($data as &$datum) {
            $datum['discount'] = !empty($datum['discount']) ? floatval($datum['discount']) : 0;
        }

        return $data;
    }

    /**
     * @param $id
     * @return array|\yii\db\ActiveRecord|null|CouponType
     */
    public function findById($id)
    {
        return CouponType::find()
            ->where(['id' => $id])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->one();
    }

    /**
     * @param $id
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findByIds($ids)
    {
        return CouponType::find()
            ->where(['in', 'id', $ids])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->all();
    }
}
