<?php

namespace addons\TinyShop\services\marketing;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\web\NotFoundHttpException;
use yii\web\UnprocessableEntityHttpException;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use common\helpers\StringHelper;
use common\enums\UseStateEnum;
use common\components\Service;
use common\helpers\EchantsHelper;
use common\helpers\BcHelper;
use addons\TinyShop\common\models\marketing\Coupon;
use addons\TinyShop\common\enums\RangeTypeEnum;
use addons\TinyShop\merchant\modules\marketing\forms\CouponTypeForm;
use addons\TinyShop\common\enums\SubscriptionActionEnum;
use addons\TinyShop\common\models\marketing\CouponType;
use addons\TinyShop\common\enums\CouponGetTypeEnum;
use addons\TinyShop\common\enums\DiscountTypeEnum;
use addons\TinyShop\common\enums\MarketingEnum;

/**
 * Class CouponService
 * @package addons\TinyShop\services\marketing
 */
class CouponService extends Service
{
    /**
     * @return array|ActiveRecord[]
     */
    public function findStateCount($member_id = '')
    {
        $list = [
            'get' => 0, // 已领取
            'un_sed' => 0, // 已使用
            'past_due' => 0 // 已过期
        ];

        $data = Coupon::find()
            ->select(['state', 'count(id) as count'])
            ->where(['member_id' => $member_id, 'status' => StatusEnum::ENABLED])
            ->andFilterWhere(['in', 'state', [UseStateEnum::GET, UseStateEnum::USE, UseStateEnum::PAST_DUE]])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->groupBy('state')
            ->asArray()
            ->all();

        $data = ArrayHelper::map($data, 'state', 'count');
        isset($data[UseStateEnum::GET]) && $list['get'] = $data[UseStateEnum::GET];
        isset($data[UseStateEnum::USE]) && $list['un_sed'] = $data[UseStateEnum::USE];
        isset($data[UseStateEnum::PAST_DUE]) && $list['past_due'] = $data[UseStateEnum::PAST_DUE];

        return $list;
    }

    /**
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findCountByState($member_id = '')
    {
        $default = [
            [
                'state' => 0,
                'count' => 0,
            ],
            [
                'state' => 1,
                'count' => 0,
            ],
            [
                'state' => 2,
                'count' => 0,
            ],
            [
                'state' => 3,
                'count' => 0,
            ],
        ];


        $data = Coupon::find()
            ->select(['state', 'count(id) as count'])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->andFilterWhere(['member_id' => $member_id])
            ->groupBy('state')
            ->asArray()
            ->all();

        !isset($data[0]) && $data[0] = $default[0];
        !isset($data[1]) && $data[1] = $default[1];
        !isset($data[2]) && $data[2] = $default[2];
        !isset($data[3]) && $data[3] = $default[3];

        return $data;
    }

    /**
     * @param $type
     * @param $state
     * @return array
     */
    public function getBetweenCountStatToEchant($type, $state)
    {
        $fields = [
            'count' => '数量',
        ];

        $fieldMap = [
            UseStateEnum::GET => 'fetch_time',
            UseStateEnum::USE => 'use_time',
            UseStateEnum::PAST_DUE => 'end_time',
        ];

        $condition = [];
        $field = $fieldMap[$state];
        $state == UseStateEnum::PAST_DUE && $condition = ['state' => UseStateEnum::PAST_DUE];

        // 获取时间和格式化
        list($time, $format) = EchantsHelper::getFormatTime($type);

        // 获取数据
        return EchantsHelper::lineOrBarInTime(function ($start_time, $end_time, $formatting) use (
            $state,
            $field,
            $condition
        ) {
            return Coupon::find()
                ->select([
                    'count(id) as count',
                    "from_unixtime(".$field.", '$formatting') as time",
                ])
                ->andWhere(['between', $field, $start_time, $end_time])
                ->andFilterWhere($condition)
                ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
                ->groupBy(['time'])
                ->asArray()
                ->all();
        }, $fields, $time, $format);
    }

    /**
     * 查看优惠券
     *
     * @param $member_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getReadByMemberId($member_id)
    {
        $data = Coupon::find()
            ->where([
                'member_id' => $member_id,
                'state' => UseStateEnum::GET,
                'status' => StatusEnum::ENABLED,
                'is_read' => StatusEnum::DISABLED,
            ])
            ->andWhere(['between', 'start_time', 'end_time', time()])
            ->andWhere(['in', 'get_type', CouponGetTypeEnum::getShowMap()])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->asArray()
            ->all();

        if (empty($data)) {
            return [];
        }

        $ids = ArrayHelper::getColumn($data, 'id');
        Coupon::updateAll(['is_read' => StatusEnum::ENABLED], ['in', 'id', $ids]);

        return [
            'total' => count($data),
            'list' => array_slice($data, 0, 10),
        ];
    }

    /**
     * 创建优惠券
     *
     * @param CouponTypeForm $couponType
     * @param $count
     * @throws Exception
     */
    public function create(CouponTypeForm $couponType, $count)
    {
        $codes = StringHelper::randomList($count);
        $rows = [];
        foreach ($codes as $code) {
            $rows[] = [
                'coupon_type_id' => $couponType->id,
                'merchant_id' => $couponType->merchant_id,
                'code' => $code,
            ];
        }

        $field = ['coupon_type_id', 'merchant_id', 'code'];
        !empty($rows) && Yii::$app->db->createCommand()->batchInsert(Coupon::tableName(), $field, $rows)->execute();
    }

    /**
     * 赠送从已有优惠券中领取
     *
     * @param CouponType $couponType
     * @param $member_id
     * @param int $get_type
     * @param string $coupon
     * @return Coupon
     * @throws NotFoundHttpException
     * @throws UnprocessableEntityHttpException
     */
    public function give(CouponType $couponType, $member_id, $get_type = CouponGetTypeEnum::ONESELF, $coupon = '')
    {
        /** @var Coupon $model */
        if (!empty($coupon)) {
            $model = $coupon;
        } else {
            $model = Coupon::find()
                ->where(['coupon_type_id' => $couponType->id, 'state' => UseStateEnum::UNCLAIMED])
                ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
                ->one();
        }

        if (!$model) {
            throw new NotFoundHttpException('优惠券已被领取完');
        }

        $model->member_id = $member_id;
        $model->state = UseStateEnum::GET;
        $model->get_type = $get_type;
        $model->title = $couponType->title;
        $model->discount_type = $couponType->discount_type;
        $model->discount = $couponType->discount;
        $model->at_least = $couponType->at_least;
        $model->single_type = $couponType->single_type;
        $model->fetch_time = time();

        // 领到券当日开始 N 天内有效
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

        // 赠送优惠券提醒
        Yii::$app->tinyShopService->notify->createRemindByReceiver(
            $model->id,
            SubscriptionActionEnum::COUPON_GIVE,
            $member_id,
            [
                'couponType' => ArrayHelper::merge(
                    ArrayHelper::toArray($couponType),
                    ['get_type' => $get_type]
                ),
            ]
        );

        return $model;
    }

    /**
     * 赠送新的优惠券
     *
     * @param CouponType $couponType
     * @param $member_id
     * @param $map_id
     * @param $get_type
     * @param $number
     * @return Coupon
     * @throws UnprocessableEntityHttpException
     */
    public function giveByNewRecord(
        CouponType $couponType,
        $member_id,
        $map_id = 0,
        $get_type = CouponGetTypeEnum::ONESELF,
        $number = 1
    ) {
        $model = new Coupon();
        $model = $model->loadDefaultValues();
        $model->map_id = $map_id;
        $model->member_id = $member_id;
        $model->coupon_type_id = $couponType->id;
        $model->merchant_id = $couponType->merchant_id;
        $model->state = UseStateEnum::GET;
        $model->get_type = $get_type;
        $model->title = $couponType['title'];
        $model->discount_type = $couponType['discount_type'];
        $model->discount = $couponType['discount'];
        $model->at_least = $couponType['at_least'];
        $model->single_type = $couponType['single_type'];
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

        // 增加发送数量
        CouponType::updateAllCounters(['count' => 1], ['id' => $couponType->id]);

        // 赠送优惠券提醒
        if (!in_array($get_type, [CouponGetTypeEnum::ONESELF]) ) {
            Yii::$app->tinyShopService->notify->createRemindByReceiver(
                $model->id,
                SubscriptionActionEnum::COUPON_GIVE,
                $member_id,
                ['couponType' => ArrayHelper::merge(ArrayHelper::toArray($couponType), ['get_type' => $get_type])]
            );
        }

        if ($number > 1) {
            $number--;
            return $this->giveByNewRecord($couponType, $member_id, $map_id, $get_type, $number);
        }

        return $model;
    }

    /**
     * 获取用户所有可用优惠券
     *
     * @param int $member_id
     * @param int $merchant_id
     * @param array $orderProducts
     * @return array
     */
    public function getUsableByMemberId(int $member_id, int $merchant_id, array $groupOrderProducts)
    {
        $models = Coupon::find()
            ->where([
                'and',
                ['member_id' => $member_id],
                ['state' => UseStateEnum::GET],
                ['status' => StatusEnum::ENABLED],
                ['<', 'start_time', time()],
                ['>', 'end_time', time()],
            ])
            ->andWhere(['merchant_id' => $merchant_id])
            ->with(['couponType', 'product', 'cate'])
            ->limit(100)
            ->asArray()
            ->all();

        $coupons = [];
        foreach ($models as $model) {
            if ($coupon = $this->getPredictDiscountByCoupon($model, $groupOrderProducts)) {
                $coupons[] = $coupon;
            }
        }

        return $coupons;
    }

    /**
     * @param $coupon
     * @param $groupOrderProducts
     * @return mixed
     */
    public function getPredictDiscountByCoupon($coupon, $groupOrderProducts)
    {
        // 参与的商品ID
        $productIds = [];
        // 商品总金额
        $totalMoney = 0;
        $maxMoney = 0;
        $maxProductId = 0;
        $rangeType = $coupon['couponType']['range_type'];

        // 可用商品ID和不可用
        $usableProductIds = $disabledProductIds = [];
        foreach ($coupon['product'] as $item) {
            if ($item['marketing_type'] == MarketingEnum::COUPON_IN) {
                $usableProductIds[] = $item['product_id'];
            } else {
                $disabledProductIds[] = $item['product_id'];
            }
        }

        // 可用分类ID和不可用
        $usableCateIds = $disabledCateIds = [];
        foreach ($coupon['cate'] as $item) {
            if ($item['marketing_type'] == MarketingEnum::COUPON_IN) {
                $usableCateIds[] = $item['cate_id'];
            } else {
                $disabledCateIds[] = $item['cate_id'];
            }
        }

        switch ($rangeType) {
            // 全部商品参加
            case RangeTypeEnum::ALL;
                $productIds = array_keys($groupOrderProducts);
                break;
            // 指定商品参加
            case RangeTypeEnum::ASSIGN_PRODUCT;
                foreach ($usableProductIds as $usableProductId) {
                    if (isset($groupOrderProducts[$usableProductId])) {
                        $productIds[] = $usableProductId;
                    }
                }
                break;
            // 指定商品不参加
            case RangeTypeEnum::NOT_ASSIGN_PRODUCT;
                foreach ($groupOrderProducts as $productId => $groupOrderProduct) {
                    if (!in_array($productId, $disabledProductIds)) {
                        $productIds[] = $productId;
                    }
                }
                break;
            // 指定分类参加
            case RangeTypeEnum::ASSIGN_CATE;
                foreach ($groupOrderProducts as $productId => $groupOrderProduct) {
                    if (array_intersect($groupOrderProduct['cateIds'], $usableCateIds)) {
                        $productIds[] = $productId;
                    }
                }
                break;
            // 指定分类不参加
            case RangeTypeEnum::NOT_ASSIGN_CATE;
                foreach ($groupOrderProducts as $productId => $groupOrderProduct) {
                    $tmpStatus = true;
                    foreach ($groupOrderProduct['cateIds'] as $cateId) {
                        if (in_array($cateId, $disabledCateIds)) {
                            $tmpStatus = false;
                            break;
                        }
                    }

                    $tmpStatus == true && $productIds[] = $productId;
                }
                break;
        }

        if (empty($productIds)) {
            return false;
        }

        // 计算最终优惠金额
        foreach ($productIds as $id) {
            $money = $groupOrderProducts[$id]['product_money'];
            if ($money > $maxMoney) {
                $maxMoney = $money;
                $maxProductId = $id;
            }

            $totalMoney = BcHelper::add($totalMoney, $money);
        }

        // 金额不满足
        if ($coupon['at_least'] > $totalMoney) {
            return false;
        }

        // 单品卷
        if ($coupon['single_type'] == StatusEnum::ENABLED) {
            $totalMoney = $maxMoney;
            $productIds = [$maxProductId];
        }

        // 减钱
        if ($coupon['discount_type'] == DiscountTypeEnum::MONEY) {
            $predictDiscount = $coupon['discount'];
        } else {
            $predictDiscount = BcHelper::mul($totalMoney, $coupon['discount']);
            $predictDiscount = BcHelper::sub($totalMoney, BcHelper::div($predictDiscount, 10));
        }

        $coupon = ArrayHelper::toArray($coupon);
        unset($coupon['map_id'], $coupon['use_order_id'], $coupon['couponType'], $coupon['product'], $coupon['cate']);
        $coupon['predictTotalMoney'] = $totalMoney;
        $coupon['predictDiscount'] = $predictDiscount;
        $coupon['productIds'] = $productIds;

        return $coupon;
    }

    /**
     * 关闭所有过期的优惠券
     */
    public function closeAll()
    {
        Coupon::updateAll(['state' => UseStateEnum::PAST_DUE], [
            'and',
            ['state' => UseStateEnum::GET],
            ['<', 'end_time', time()],
        ]);
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
        $coupon->state = UseStateEnum::USE;
        $coupon->save();
    }

    /**
     * 关闭退回
     *
     * @param $id
     * @param $member_id
     */
    public function back($id, $member_id)
    {
        Coupon::updateAll([
            'use_order_id' => 0,
            'use_time' => 0,
            'state' => UseStateEnum::GET,
        ], [
            'id' => $id,
            'member_id' => $member_id,
            'state' => UseStateEnum::USE,
        ]);
    }

    /**
     * 未使用撤回
     *
     * @param $id
     * @param $member_id
     */
    public function revocation($id, $member_id)
    {
        Coupon::updateAll([
            'use_order_id' => 0,
            'use_time' => 0,
            'get_type' => 0,
            'member_id' => 0,
            'state' => UseStateEnum::UNCLAIMED,
        ], [
            'id' => $id,
            'member_id' => $member_id,
            'state' => UseStateEnum::GET,
        ]);
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
            ->where(['member_id' => $member_id, 'state' => UseStateEnum::GET, 'status' => StatusEnum::ENABLED])
            ->andWhere(['between', 'start_time', 'end_time', time()])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->scalar();
    }

    /**
     * @param $id
     * @param $member_id
     * @return false|string|null
     */
    public function findCountById($id, $member_id, $time = 0)
    {
        return Coupon::find()
            ->select('count(id) as count')
            ->where(['coupon_type_id' => $id, 'member_id' => $member_id])
            ->andFilterWhere(['>=', 'fetch_time', $time])
            ->scalar();
    }

    /**
     * 优惠券
     *
     * @param $code
     * @return array|ActiveRecord|null
     */
    public function findByCode($code)
    {
        return Coupon::find()
            ->where(['code' => $code])
            ->with('couponType')
            ->one();
    }

    /**
     * @param $id
     * @return array|ActiveRecord|null|Coupon
     */
    public function findByMemberId($id, $member_id)
    {
        return Coupon::find()
            ->where(['id' => $id, 'member_id' => $member_id])
            ->with('couponType')
            ->one();
    }
}
