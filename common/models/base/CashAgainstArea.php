<?php

namespace addons\TinyShop\common\models\base;

use common\behaviors\MerchantBehavior;

/**
 * This is the model class for table "{{%addon_shop_base_cash_against_area}}".
 *
 * @property int $id
 * @property int $merchant_id 店铺id
 * @property string $province_ids 省id组
 * @property string $city_ids 市id组
 * @property string $area_ids 区id组
 * @property int $status 状态[-1:删除;0:禁用;1启用]
 * @property string $created_at 添加时间
 * @property string $updated_at 修改时间
 */
class CashAgainstArea extends \common\models\base\BaseModel
{
    use MerchantBehavior;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_shop_base_cash_against_area}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['province_ids', 'city_ids', 'area_ids'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'merchant_id' => '店铺id',
            'province_ids' => '省id组',
            'city_ids' => '市id组',
            'area_ids' => '区id组',
            'status' => '状态[-1:删除;0:禁用;1启用]',
            'created_at' => '添加时间',
            'updated_at' => '修改时间',
        ];
    }
}
