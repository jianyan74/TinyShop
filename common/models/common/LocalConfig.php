<?php

namespace addons\TinyShop\common\models\common;

use common\behaviors\MerchantBehavior;

/**
 * This is the model class for table "{{%addon_tiny_shop_common_local_distribution_config}}".
 *
 * @property int $id
 * @property int|null $merchant_id 商户id
 * @property float $order_money 订单金额
 * @property float $freight 运费
 * @property string|null $distribution_time 配送时间
 * @property string|null $shipping_fee 阶梯配送费用
 * @property int|null $make_day 可预约天数
 * @property int|null $interval_time 配送间隔时间
 * @property int|null $auto_order_receiving 自动接单
 * @property int $is_start 是否是起步价
 * @property int|null $created_at 创建时间
 * @property int|null $updated_at 更新时间
 */
class LocalConfig extends \common\models\base\BaseModel
{
    use MerchantBehavior;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_common_local_config}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_money', 'freight', 'make_day', 'interval_time', 'distribution_time'], 'required'],
            [['merchant_id', 'make_day', 'interval_time', 'auto_order_receiving', 'is_start', 'created_at', 'updated_at'], 'integer'],
            [['order_money', 'freight'], 'number'],
            [['distribution_time', 'shipping_fee'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'merchant_id' => '商户id',
            'order_money' => '订单最低支付金额',
            'freight' => '起送运费',
            'distribution_time' => '配送时间',
            'shipping_fee' => '阶梯配送费用',
            'make_day' => '可预约天数',
            'interval_time' => '配送间隔时间',
            'auto_order_receiving' => '自动接单',
            'is_start' => '是否是起步价',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
