<?php

namespace addons\TinyShop\services\marketing;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\UnprocessableEntityHttpException;
use common\enums\StatusEnum;
use common\components\Service;
use common\helpers\ArrayHelper;
use addons\TinyShop\common\enums\RangeTypeEnum;
use addons\TinyShop\common\models\marketing\CouponType;
use addons\TinyShop\common\models\marketing\Coupon;
use addons\TinyShop\merchant\forms\CouponTypeForm;

/**
 * Class CouponService
 * @package addons\TinyShop\services\marketing
 * @author jianyan74 <751393839@qq.com>
 */
class CouponService extends Service
{
    /**
     * 关闭所有过期的优惠券
     */
    public function closeAll()
    {
        Coupon::updateAll(['state' => Coupon::STATE_PAST_DUE], [
            'and',
            ['state' => Coupon::STATE_GET],
            ['<', 'end_time', time()]
        ]);
    }

    /**
     * @param $id
     * @param $member_id
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findByIdWithType($id, $member_id)
    {
        return Coupon::find()
            ->where(['id' => $id, 'member_id' => $member_id])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->with('couponType')
            ->one();
    }

    /**
     * 赠送优惠券
     *
     * @param CouponType $couponType
     * @param $member_id
     * @return array|\yii\db\ActiveRecord|null
     * @throws NotFoundHttpException
     * @throws \yii\web\UnprocessableEntityHttpException
     */
    public function give(CouponType $couponType, $member_id)
    {
        /** @var Coupon $model */
        $model = Coupon::find()
            ->where(['coupon_type_id' => $couponType['id'], 'state' => Coupon::STATE_UNUNSED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->one();

        if (!$model) {
            throw new NotFoundHttpException('优惠券已被领取完');
        }

        $model->member_id = $member_id;
        $model->state = Coupon::STATE_GET;
        $model->title = $couponType['title'];
        $model->type = $couponType['type'];
        $model->discount = $couponType['discount'];
        $model->at_least = $couponType['at_least'];
        $model->money = $couponType['money'];
        $model->fetch_time = time();

        // 领到券当日开始N天内有效
        if ($couponType->term_of_validity_type == StatusEnum::ENABLED) {
            $model->start_time = time();
            $model->end_time = time() + $couponType->fixed_term * 60 * 60 * 24;
        } else {
            $model->start_time = $couponType->start_time;
            $model->end_time = $couponType->end_time;
        }

        if (!$model->save()) {
            throw new UnprocessableEntityHttpException($this->getError($model));
        }

        Yii::$app->tinyShopService->marketingCouponType->deductionRepertory($couponType);

        return $model;
    }

    /**
     * @param $id
     * @param $member_id
     * @return false|string|null
     */
    public function findCountById($id, $member_id)
    {
        return Coupon::find()
            ->select('count(id) as count')
            ->where(['coupon_type_id' => $id, 'member_id' => $member_id, 'state' => Coupon::STATE_GET])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->scalar();
    }

    /**
     * 查询用户优惠劵总数
     *
     * @param $member_id
     * @return false|string|null
     */
    public function findCountByMemberId($member_id)
    {
        return Coupon::find()
            ->select('count(id) as count')
            ->where(['member_id' => $member_id, 'state' => Coupon::STATE_GET, 'status' => StatusEnum::ENABLED])
            ->andWhere(['between', 'start_time', 'end_time', time()])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->scalar();
    }

    /**
     * 获取用户所有可用优惠券
     *
     * @param $member_id
     * @param array $orderProducts
     * @return array
     */
    public function getListByMemberId(int $member_id, array $orderProducts)
    {
        $models = Coupon::find()
            ->where([
                'and',
                ['member_id' => $member_id],
                ['state' => Coupon::STATE_GET],
                ['status' => StatusEnum::ENABLED],
                ['<', 'start_time', time()],
                ['>', 'end_time', time()],
            ])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->with(['couponType', 'couponProduct'])
            ->asArray()
            ->all();

        // 可用产品
        $productIds = ArrayHelper::getColumn($orderProducts, 'product_id');

        $coupon = [];
        foreach ($models as $model) {
            // 全场
            if ($model['couponType']['range_type'] == RangeTypeEnum::ALL) {
                $coupon[] = $model;
            } else {
                $couponProductIds = ArrayHelper::getColumn($model['couponProduct'] ?? [], 'product_id');
                // 判断是否在可用产品内
                foreach ($couponProductIds as $couponProductId) {
                    if (in_array($couponProductId, $productIds)) {
                        $coupon[] = $model;

                        break;
                    }
                }
            }
        }

        return $coupon;
    }

    /**
     * 使用优惠券
     *
     * @param Coupon $coupon
     * @param $order_id
     */
    public function used(Coupon $coupon, $order_id)
    {
        $coupon->use_order_id = $order_id;
        $coupon->use_time = time();
        $coupon->state = Coupon::STATE_UNSED;
        $coupon->save();
    }

    /**
     * 创建优惠券
     *
     * @param CouponTypeForm $couponType
     * @param $count
     * @throws \yii\db\Exception
     */
    public function create(CouponTypeForm $couponType, $count)
    {
        $rows = [];
        for ($i = 0; $i < $count; $i++) {
            $code = time() . rand(10000, 99999);
            $merchant_id = Yii::$app->services->merchant->getId();
            $rows[] = [
                'coupon_type_id' => $couponType->id,
                'merchant_id' => empty($merchant_id) ? 0 : $merchant_id,
                'code' => $code,
            ];
        }

        $field = ['coupon_type_id', 'merchant_id', 'code'];
        !empty($rows) && Yii::$app->db->createCommand()->batchInsert(Coupon::tableName(), $field, $rows)->execute();
    }
}