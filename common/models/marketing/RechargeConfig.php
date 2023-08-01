<?php

namespace addons\TinyShop\common\models\marketing;

use common\behaviors\MerchantBehavior;

/**
 * This is the model class for table "{{%member_recharge_config}}".
 *
 * @property int $id
 * @property int|null $merchant_id 商户
 * @property float|null $price 充值金额
 * @property float|null $give_price 赠送金额
 * @property int|null $give_point 赠送金额
 * @property int|null $give_growth 赠送成长值
 * @property int|null $sort 排序
 * @property int|null $status 状态
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class RechargeConfig extends \common\models\base\BaseModel
{
    use MerchantBehavior;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_marketing_recharge_config}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['price', 'give_price', 'give_point', 'give_growth'], 'required'],
            [['price', 'give_price', 'give_point', 'give_growth'], 'number', 'min' => 0],
            [['merchant_id', 'sort', 'status', 'created_at', 'updated_at'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'merchant_id' => '商户',
            'price' => '充值金额',
            'give_price' => '赠送金额',
            'give_point' => '赠送积分',
            'give_growth' => '赠送成长值',
            'sort' => '排序',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
