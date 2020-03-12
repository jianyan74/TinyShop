<?php

namespace addons\TinyShop\common\models\marketing;

use common\behaviors\MerchantBehavior;

/**
 * This is the model class for table "{{%addon_shop_marketing_point_config}}".
 *
 * @property string $id 主键
 * @property string $merchant_id 商户id
 * @property int $is_open 是否启动
 * @property string $convert_rate 1积分对应金额
 * @property string $desc 积分说明
 * @property int $status 状态[-1:删除;0:禁用;1启用]
 * @property string $created_at 添加时间
 * @property string $updated_at 修改时间
 */
class PointConfig extends \common\models\base\BaseModel
{
    use MerchantBehavior;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_shop_marketing_point_config}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'is_open', 'status', 'created_at', 'updated_at'], 'integer'],
            [['convert_rate'], 'number', 'min' => 0],
            [['desc'], 'string'],
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
            'is_open' => '是否开启积分抵现',
            'convert_rate' => '积分抵现比率',
            'desc' => '积分说明',
            'status' => '状态',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeHints()
    {
        return [
            'is_open' => '只有启用该选项，才可以使用积分抵现功能',
            'convert_rate' => '积分抵现比率 1积分可抵多少元现金',
        ];
    }
}
