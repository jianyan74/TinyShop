<?php

namespace addons\TinyShop\common\models\marketing;

use yii\db\ActiveQuery;
use yii\base\InvalidConfigException;
use common\traits\HasOneMerchant;
use common\traits\HasOneMember;
use addons\TinyShop\common\enums\MarketingEnum;
use addons\TinyShop\common\models\product\Product;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%addon_tiny_shop_marketing_coupon}}".
 *
 * @property int $id 优惠券id
 * @property int|null $member_id 领用人
 * @property int|null $merchant_id 店铺Id
 * @property int $coupon_type_id 优惠券类型id
 * @property float|null $discount 活动金额
 * @property int|null $discount_type 活动金额类型
 * @property string $title 优惠券名称
 * @property string|null $code 优惠券编码
 * @property int|null $map_id 创建关联ID
 * @property int|null $map_type 创建关联类型
 * @property int|null $use_order_id 优惠券使用订单id
 * @property float|null $at_least 满多少元使用 0代表无限制
 * @property int|null $state 优惠券状态 0未领用 1已领用（未使用） 2已使用 3已过期
 * @property int|null $get_type 获取方式
 * @property int|null $single_type 单品卷
 * @property int|null $is_read 浏览状态
 * @property int|null $fetch_time 领取时间
 * @property int|null $use_time 使用时间
 * @property int|null $start_time 有效期开始时间
 * @property int|null $end_time 有效期结束时间
 * @property int|null $status 状态
 */
class Coupon extends ActiveRecord
{
    use HasOneMerchant, HasOneMember;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_marketing_coupon}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'member_id',
                    'merchant_id',
                    'coupon_type_id',
                    'discount_type',
                    'map_id',
                    'map_type',
                    'use_order_id',
                    'state',
                    'get_type',
                    'single_type',
                    'is_read',
                    'fetch_time',
                    'use_time',
                    'start_time',
                    'end_time',
                    'status',
                ],
                'integer',
            ],
            [['discount', 'at_least'], 'number'],
            [['title'], 'string', 'max' => 50],
            [['code'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '优惠券id',
            'member_id' => '领用人',
            'merchant_id' => '店铺Id',
            'coupon_type_id' => '优惠券类型id',
            'discount' => '活动金额',
            'discount_type' => '活动金额类型',
            'title' => '优惠券名称',
            'code' => '优惠券编码',
            'map_id' => '创建关联ID',
            'map_type' => '创建关联类型',
            'use_order_id' => '优惠券使用订单id',
            'at_least' => '满多少元使用',
            'state' => '优惠券状态',
            'get_type' => '获取方式',
            'single_type' => '单品卷',
            'is_read' => '浏览状态',
            'fetch_time' => '领取时间',
            'use_time' => '使用时间',
            'start_time' => '有效期开始时间',
            'end_time' => '有效期结束时间',
            'status' => '状态',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getCouponType()
    {
        return $this->hasOne(CouponType::class, ['id' => 'coupon_type_id']);
    }

    /**
     * 关联商品
     *
     * @return ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasMany(MarketingProduct::class, ['marketing_id' => 'coupon_type_id'])
            ->select(['id', 'marketing_id', 'marketing_type', 'product_id'])
            ->andWhere(['in', 'marketing_type', [MarketingEnum::COUPON_IN, MarketingEnum::COUPON_NOT_IN]]);
    }

    /**
     * 关联分类
     *
     * @return ActiveQuery
     */
    public function getCate()
    {
        return $this->hasMany(MarketingCate::class, ['marketing_id' => 'coupon_type_id'])
            ->select(['id', 'marketing_id', 'marketing_type', 'cate_id'])
            ->andWhere(['in', 'marketing_type', [MarketingEnum::COUPON_IN, MarketingEnum::COUPON_NOT_IN]]);
    }
}
