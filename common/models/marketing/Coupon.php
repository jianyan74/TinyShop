<?php

namespace addons\TinyShop\common\models\marketing;

use addons\TinyShop\common\models\product\Product;
use common\behaviors\MerchantBehavior;
use common\models\member\Member;
use common\traits\HasOneMerchant;

/**
 * This is the model class for table "{{%addon_shop_marketing_coupon}}".
 *
 * @property int $id 优惠券id
 * @property string $title 标题
 * @property string $coupon_type_id 优惠券类型id
 * @property string $merchant_id 店铺Id
 * @property string $code 优惠券编码
 * @property int $type 优惠券类型 1:满减;2:折扣
 * @property string $at_least 满多少元使用 0代表无限制
 * @property int $member_id 领用人
 * @property int $use_order_id 优惠券使用订单id
 * @property int $create_order_id 创建订单id(优惠券只有是完成订单发放的优惠券时才有值)
 * @property int $discount 折扣 1-100
 * @property string $money 面额
 * @property int $state 优惠券状态 0未领用 1已领用（未使用） 2已使用 3已过期
 * @property int $get_type 获取方式1订单2.首页领取
 * @property int $fetch_time 领取时间
 * @property int $use_time 使用时间
 * @property int $start_time 有效期开始时间
 * @property int $end_time 有效期结束时间
 */
class Coupon extends \yii\db\ActiveRecord
{
    use MerchantBehavior, HasOneMerchant;

    const STATE_UNUNSED = 0;
    const STATE_GET = 1;
    const STATE_UNSED = 2;
    const STATE_PAST_DUE = 3;

    /**
     * @var array
     */
    public static $stateExplain = [
        self::STATE_UNUNSED => '未领取',
        self::STATE_GET => '已领取',
        self::STATE_UNSED => '已使用',
        self::STATE_PAST_DUE => '已过期',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_shop_marketing_coupon}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'type',
                    'discount',
                    'coupon_type_id',
                    'merchant_id',
                    'member_id',
                    'use_order_id',
                    'create_order_id',
                    'state',
                    'get_type',
                    'fetch_time',
                    'use_time',
                    'start_time',
                    'end_time',
                    'status',
                ],
                'integer',
            ],
            [['money', 'at_least'], 'number'],
            [['code'], 'string', 'max' => 100],
            [['title'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '标题',
            'type' => '优惠券类型',
            'coupon_type_id' => 'Coupon Type ID',
            'merchant_id' => 'Merchant ID',
            'code' => '优惠券编码',
            'member_id' => '领用人',
            'use_order_id' => '优惠券使用订单id',
            'create_order_id' => '创建订单id',
            'money' => '面额',
            'at_least' => '满多少元使用',
            'discount' => '折扣率',
            'state' => '优惠券状态',
            'get_type' => '获取方式',
            'fetch_time' => '领取时间',
            'use_time' => '使用时间',
            'start_time' => '有效期开始时间',
            'end_time' => '有效期结束时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasOne(Member::class, ['id' => 'member_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCouponType()
    {
        return $this->hasOne(CouponType::class, ['id' => 'coupon_type_id']);
    }

    /**
     * 可用商品
     *
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getUsableProduct()
    {
        return $this->hasMany(Product::class, ['id' => 'product_id'])
            ->viaTable(CouponProduct::tableName(), ['coupon_type_id' => 'coupon_type_id'])
            ->select(['id', 'name'])
            ->asArray();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCouponProduct()
    {
        return $this->hasMany(CouponProduct::class, ['coupon_type_id' => 'coupon_type_id']);
    }
}
