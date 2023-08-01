<?php

namespace addons\TinyShop\common\models\common;

use common\behaviors\MerchantBehavior;

/**
 * This is the model class for table "{{%addon_tiny_shop_common_local_area}}".
 *
 * @property int $id
 * @property int|null $merchant_id 商户id
 * @property string|null $province_ids 省id
 * @property string|null $city_ids 市id
 * @property string|null $area_ids 区县id
 * @property string|null $community_ids 社区乡镇id
 * @property int|null $status 状态
 * @property int|null $created_at 创建时间
 * @property int|null $updated_at 更新时间
 */
class LocalArea extends \common\models\base\BaseModel
{
    use MerchantBehavior;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_common_local_area}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['province_ids', 'city_ids', 'area_ids', 'community_ids'], 'safe'],
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
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
