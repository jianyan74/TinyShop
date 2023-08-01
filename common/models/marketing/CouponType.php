<?php

namespace addons\TinyShop\common\models\marketing;

use yii\db\ActiveQuery;
use common\helpers\StringHelper;
use common\models\base\BaseModel;
use common\behaviors\MerchantBehavior;
use common\traits\HasOneMerchant;
use addons\TinyShop\common\enums\MarketingEnum;

/**
 * This is the model class for table "{{%addon_tiny_shop_marketing_coupon_type}}".
 *
 * @property int $id 优惠券类型Id
 * @property int $merchant_id 店铺ID
 * @property string|null $title 优惠券名称
 * @property float|null $discount 活动金额
 * @property int|null $discount_type 活动金额类型
 * @property int|null $count 发放数量
 * @property int|null $get_count 领取数量
 * @property int|null $max_fetch 每人最大领取个数 0无限制
 * @property int|null $max_day_fetch 每人每日最大领取个数 0无限制
 * @property float|null $at_least 满多少元使用 0代表无限制
 * @property int|null $need_user_level 领取人会员等级
 * @property int|null $range_type 使用范围
 * @property int|null $get_start_time 领取有效日期开始时间
 * @property int|null $get_end_time 领取有效日期结束时间
 * @property int|null $start_time 有效日期开始时间
 * @property int|null $end_time 有效日期结束时间
 * @property float|null $min_price 优惠券最小金额
 * @property float|null $max_price 优惠券最大金额
 * @property int|null $term_of_validity_type 有效期类型
 * @property int|null $fixed_term 领取之日起N天内有效
 * @property int|null $single_type 单品卷
 * @property int|null $is_list_visible 领劵列表可见
 * @property int|null $is_new_people 新人优惠券(未下支付单)
 * @property string|null $remark 备注
 * @property int|null $status 状态[-1:删除;0:禁用;1启用]
 * @property int|null $created_at 创建时间
 * @property int|null $updated_at 修改时间
 */
class CouponType extends BaseModel
{
    use MerchantBehavior, HasOneMerchant;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_marketing_coupon_type}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'count',
                    'at_least',
                    'title',
                    'discount_type',
                    'term_of_validity_type',
                    'max_fetch',
                    'max_day_fetch',
                    'range_type',
                    'fixed_term',
                    'get_start_time',
                    'get_end_time',
                    'start_time',
                    'end_time',
                ],
                'required',
            ],
            [
                [
                    'merchant_id',
                    'discount_type',
                    'count',
                    'get_count',
                    'max_fetch',
                    'max_day_fetch',
                    'need_user_level',
                    'range_type',
                    'term_of_validity_type',
                    'fixed_term',
                    'single_type',
                    'is_list_visible',
                    'is_new_people',
                    'status',
                    'created_at',
                    'updated_at',
                ],
                'integer',
                'min' => 0,
            ],
            [['at_least', 'min_price', 'max_price'], 'number', 'min' => 0],
            [['discount'], 'number', 'min' => 0, 'max' => 9.9],
            [['remark'], 'string', 'min' => 0, 'max' => 10],
            [['discount'], 'required'],
            [['title'], 'string', 'max' => 50],
            [
                [
                    'get_start_time',
                    'get_end_time',
                    'start_time',
                    'end_time',
                ],
                'safe',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '优惠券类型Id',
            'merchant_id' => '店铺ID',
            'title' => '优惠券名称',
            'discount' => '折扣',
            'discount_type' => '优惠券类型',
            'count' => '发放数量',
            'get_count' => '领取数量',
            'max_fetch' => '每人最大领取个数',
            'max_day_fetch' => '每人每日最大领取个数',
            'at_least' => '满多少元使用',
            'need_user_level' => '领取人会员等级',
            'range_type' => '参与商品',
            'get_start_time' => '领取有效日期开始时间',
            'get_end_time' => '领取有效日期结束时间',
            'start_time' => '有效日期开始时间',
            'end_time' => '有效日期结束时间',
            'min_price' => '优惠券最小金额',
            'max_price' => '优惠券最大金额',
            'term_of_validity_type' => '有效期类型',
            'fixed_term' => '领取之日起 N 天内有效',
            'single_type' => '单品卷',
            'is_list_visible' => '领劵列表可见',
            'is_new_people' => '新人卷',
            'remark' => '备注',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }

    /**
     * 关联商品
     *
     * @return ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasMany(MarketingProduct::class, ['marketing_id' => 'id'])
            ->select(['id', 'marketing_type', 'product_id'])
            ->andWhere(['in', 'marketing_type', [MarketingEnum::COUPON_IN, MarketingEnum::COUPON_NOT_IN]]);
    }

    /**
     * 关联分类
     *
     * @return ActiveQuery
     */
    public function getCate()
    {
        return $this->hasMany(MarketingCate::class, ['marketing_id' => 'id'])
            ->select(['id', 'marketing_type', 'cate_id'])
            ->andWhere(['in', 'marketing_type', [MarketingEnum::COUPON_IN, MarketingEnum::COUPON_NOT_IN]]);
    }

    /**
     * 关联我的领取总数量
     *
     * @return ActiveQuery
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
