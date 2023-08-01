<?php

namespace addons\TinyShop\common\models\common;

use common\behaviors\MerchantBehavior;

/**
 * This is the model class for table "{{%addon_tiny_shop_common_placing_area}}".
 *
 * @property int $id
 * @property int $merchant_id 店铺id
 * @property string|null $no_placing_province_ids 不支持下单省id组
 * @property string|null $no_placing_city_ids 不支持下单市id组
 * @property string|null $no_placing_area_ids 不支持下单区id组
 * @property int|null $status 状态[-1:删除;0:禁用;1启用]
 * @property int|null $created_at 添加时间
 * @property int|null $updated_at 修改时间
 */
class PlacingArea extends \common\models\base\BaseModel
{
    use MerchantBehavior;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_common_placing_area}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status'], 'required'],
            [['merchant_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['no_placing_province_ids', 'no_placing_city_ids', 'no_placing_area_ids'], 'string'],
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
            'no_placing_province_ids' => '不支持下单省id组',
            'no_placing_city_ids' => '不支持下单市id组',
            'no_placing_area_ids' => '不支持下单区id组',
            'status' => '下单限制',
            'created_at' => '添加时间',
            'updated_at' => '修改时间',
        ];
    }
}
