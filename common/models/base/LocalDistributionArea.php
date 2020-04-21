<?php

namespace addons\TinyShop\common\models\base;

use common\behaviors\MerchantBehavior;

/**
 * This is the model class for table "{{%addon_shop_base_local_distribution_area}}".
 *
 * @property string $id
 * @property string $merchant_id 商户id
 * @property string $province_ids 省id
 * @property string $city_ids 市id
 * @property string $area_ids 区县id
 * @property string $community_ids 社区乡镇id
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class LocalDistributionArea extends \common\models\base\BaseModel
{
    use MerchantBehavior;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_shop_base_local_distribution_area}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'created_at', 'updated_at'], 'integer'],
            [['province_ids', 'city_ids', 'area_ids', 'community_ids'], 'string'],
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
            'province_ids' => '省id',
            'city_ids' => '市id',
            'area_ids' => '区县id',
            'community_ids' => '社区乡镇id',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
