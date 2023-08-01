<?php

namespace addons\TinyShop\common\models\common;

use common\behaviors\MerchantBehavior;

/**
 * This is the model class for table "{{%addon_tiny_shop_common_cash_against_area}}".
 *
 * @property int $id
 * @property int $merchant_id 商户id
 * @property string|null $province_ids 省id组
 * @property string|null $city_ids 市id组
 * @property string|null $area_ids 区id组
 * @property int|null $status 状态[-1:删除;0:禁用;1启用]
 * @property int|null $created_at 添加时间
 * @property int|null $updated_at 修改时间
 */
class CashAgainstArea extends \common\models\base\BaseModel
{
    use MerchantBehavior;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_common_cash_against_area}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['province_ids', 'city_ids', 'area_ids'], 'safe'],
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
            'province_ids' => '省id组',
            'city_ids' => '市id组',
            'area_ids' => '区id组',
            'status' => '状态',
            'created_at' => '添加时间',
            'updated_at' => '修改时间',
        ];
    }
}
