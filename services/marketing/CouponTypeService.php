<?php

namespace addons\TinyShop\services\marketing;

use Yii;
use yii\db\ActiveQuery;
use yii\web\UnprocessableEntityHttpException;
use common\components\Service;
use common\enums\StatusEnum;
use addons\TinyShop\common\enums\RangeTypeEnum;
use addons\TinyShop\common\models\marketing\CouponType;

/**
 * Class CouponTypeService
 * @package addons\TinyShop\services\marketing
 * @author jianyan74 <751393839@qq.com>
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
        if (isset($model['myGet']['count']) && $model['myGet']['count'] >= $model['max_fetch']) {
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
     * @param $id
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findById($id)
    {
        return CouponType::find()
            ->where(['id' => $id])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->one();
    }

    /**
     * 可领取优惠券
     *
     * @param $product_id
     */
    public function getCanReceiveCouponByProductId($product_id, $member_id)
    {
        $with = [];
        if (!empty($member_id)) {
            $with = ['myGet' => function(ActiveQuery $query) use ($member_id) {
                return $query->andWhere(['member_id' => $member_id]);
            }];
        }

        $ids = Yii::$app->tinyShopService->marketingCouponProduct->getCouponTypeIds($product_id);

        return CouponType::find()
            ->where(['status' => StatusEnum::ENABLED])
            ->andWhere(['range_type' => RangeTypeEnum::ALL])
            ->andWhere(['<', 'get_start_time', time()])
            ->andWhere(['>', 'get_end_time', time()])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->orFilterWhere([
                'and',
                ['in', 'id', $ids],
                ['<', 'get_start_time', time()],
                ['>', 'get_end_time', time()],
                ['status' => StatusEnum::ENABLED],
                ['merchant_id' => $this->getMerchantId()],
            ])
            ->with($with)
            ->asArray()
            ->all();
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

        if (!$couponType->save()) {
            throw new UnprocessableEntityHttpException($this->getError($couponType));
        }
    }
}