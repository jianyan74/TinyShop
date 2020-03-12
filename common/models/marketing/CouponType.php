<?php

namespace addons\TinyShop\common\models\marketing;

use addons\TinyShop\common\models\product\Product;
use common\behaviors\MerchantBehavior;
use common\helpers\StringHelper;
use addons\TinyShop\common\enums\PreferentialTypeEnum;
use common\traits\HasOneMerchant;

/**
 * This is the model class for table "{{%addon_shop_marketing_coupon_type}}".
 *
 * @property int $id 优惠券类型Id
 * @property int $merchant_id 店铺ID
 * @property string $title 优惠券名称
 * @property string $money 发放面额
 * @property int $count 发放数量
 * @property int $get_count 扣减数量
 * @property int $type 优惠券类型 1:满减;2:折扣
 * @property int $discount 折扣 1-100
 * @property int $max_fetch 每人最大领取个数 0无限制
 * @property string $at_least 满多少元使用 0代表无限制
 * @property int $need_user_level 领取人会员等级
 * @property int $range_type 使用范围0部分产品使用 1全场产品使用
 * @property int $is_show 是否允许首页显示0不显示1显示
 * @property int $start_time 有效日期开始时间
 * @property int $end_time 有效日期结束时间
 * @property int $get_start_time 领取有效日期开始时间
 * @property int $get_end_time 领取有效日期结束时间
 * @property int $term_of_validity_type 有效期类型 0固定时间 1领取之日起
 * @property int $fixed_term 领取之日起N天内有效
 * @property int $status 状态[-1:删除;0:禁用;1启用]
 * @property int $created_at 创建时间
 * @property int $updated_at 修改时间
 */
class CouponType extends \common\models\base\BaseModel
{
    use MerchantBehavior, HasOneMerchant;

    const TERM_OF_VALIDITY_TYPE_FIXATION = 0;
    const TERM_OF_VALIDITY_TYPE_GET = 1;

    /**
     * @var array
     */
    public static $termOfValidityTypeExplain = [
        self::TERM_OF_VALIDITY_TYPE_FIXATION => '固定时间',
        self::TERM_OF_VALIDITY_TYPE_GET => '领到券当日开始N天内有效',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_shop_marketing_coupon_type}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'merchant_id',
                    'type',
                    'count',
                    'get_count',
                    'max_fetch',
                    'need_user_level',
                    'range_type',
                    'is_show',
                    'term_of_validity_type',
                    'fixed_term',
                    'status',
                    'created_at',
                    'updated_at',
                ],
                'integer',
            ],
            [['discount'], 'integer', 'min' => 1, 'max' => 100],
            [['type'], 'requireVerifier'],
            [['count', 'at_least', 'title', 'type', 'term_of_validity_type'], 'required'],
            [['money', 'at_least'], 'number', 'min' => 0],
            [['count'], 'integer', 'min' => 0],
            [['title'], 'string', 'max' => 50],
            [['start_time', 'end_time', 'get_start_time', 'get_end_time'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'merchant_id' => 'Merchant ID',
            'title' => '名称',
            'money' => '面额',
            'count' => '发放数量',
            'get_count' => '领取数量',
            'type' => '优惠券类型',
            'discount' => '折扣率',
            'max_fetch' => '每人最大领取数',
            'at_least' => '满多少元使用',
            'need_user_level' => '需要会员级别',
            'range_type' => '参与商品',
            'is_show' => '首页显示',
            'start_time' => '生效时间',
            'end_time' => '过期时间',
            'get_start_time' => '可领取开始时间',
            'get_end_time' => '可领取过期时间',
            'term_of_validity_type' => '有效时间',
            'fixed_term' => '领取之日起几天有效',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @param $attribute
     */
    public function requireVerifier($attribute)
    {
        if ($this->type == PreferentialTypeEnum::MONEY && empty($this->money)) {
            $this->addError('money', '面额不能为空');
        }

        if ($this->type == PreferentialTypeEnum::DISCOUNT && empty($this->discount)) {
            $this->addError('discount', '折扣不能为空');
        }
    }

    /**
     * 关联产品
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasMany(CouponProduct::class, ['coupon_type_id' => 'id']);
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
            ->viaTable(CouponProduct::tableName(), ['coupon_type_id' => 'id'])
            ->select(['id', 'name'])
            ->asArray();
    }

    /**
     * 关联我的领取总数量
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMyGet()
    {
        return $this->hasOne(Coupon::class, ['coupon_type_id' => 'id'])
            ->select(['count(id) as count', 'coupon_type_id'])
            ->groupBy('coupon_type_id');
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        $this->start_time = StringHelper::dateToInt($this->start_time);
        $this->end_time = StringHelper::dateToInt($this->end_time);
        $this->get_start_time = StringHelper::dateToInt($this->get_start_time);
        $this->get_end_time = StringHelper::dateToInt($this->get_end_time);

        return parent::beforeSave($insert);
    }
}
