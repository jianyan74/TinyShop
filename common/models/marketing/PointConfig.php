<?php

namespace addons\TinyShop\common\models\marketing;

use common\behaviors\MerchantBehavior;

/**
 * This is the model class for table "{{%addon_tiny_shop_marketing_point_config}}".
 *
 * @property int $id 主键
 * @property int|null $merchant_id 商户id
 * @property float $convert_rate 1积分对应金额
 * @property float|null $min_order_money 订单金额门槛
 * @property int|null $deduction_type 抵现金额上限
 * @property float|null $max_deduction_money 每笔订单最多抵扣金额
 * @property float|null $max_deduction_rate 每笔订单最多抵扣比率
 * @property string|null $explain 积分说明
 * @property int|null $status 状态[-1:删除;0:禁用;1启用]
 * @property int|null $created_at 添加时间
 * @property int|null $updated_at 修改时间
 */
class PointConfig extends \common\models\base\BaseModel
{
    use MerchantBehavior;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_marketing_point_config}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'deduction_type', 'status', 'created_at', 'updated_at'], 'integer'],
            [['min_order_money', 'max_deduction_money', 'max_deduction_rate'], 'number', 'min' => 0],
            [['convert_rate'], 'number', 'min' => 0.01],
            [['max_deduction_rate'], 'number', 'min' => 0, 'max' => 100],
            [['convert_rate', 'min_order_money', 'max_deduction_money', 'max_deduction_rate', 'status'], 'required'],
            [['explain'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键',
            'merchant_id' => '商户id',
            'convert_rate' => '积分抵现比率',
            'min_order_money' => '订单金额门槛',
            'deduction_type' => '抵现金额上限',
            'max_deduction_money' => '每笔订单最多抵扣金额',
            'max_deduction_rate' => '每笔订单最多抵扣比率',
            'status' => '积分抵现',
            'explain' => '积分说明',
            'created_at' => '添加时间',
            'updated_at' => '修改时间',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeHints()
    {
        return [
            'status' => '只有启用该选项，才可以使用积分抵现功能',
            'convert_rate' => '单位: 元; 积分抵现比率 1 积分可抵多少元现金',
            'min_order_money' => '订单金额超出该金额可使用积分抵现',
            'max_deduction_rate' => '单位: 百分比；0 - 100%',
        ];
    }
}
