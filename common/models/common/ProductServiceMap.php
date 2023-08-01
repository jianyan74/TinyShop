<?php

namespace addons\TinyShop\common\models\common;

use common\behaviors\MerchantBehavior;
use common\enums\StatusEnum;
use common\traits\HasOneMerchant;

/**
 * This is the model class for table "{{%addon_tiny_shop_common_product_service_map}}".
 *
 * @property int $id
 * @property int|null $merchant_id 商户id
 * @property int|null $service_id 服务ID
 * @property string|null $refusal_cause 拒绝原因
 * @property int|null $audit_time 审核时间
 * @property int|null $audit_status 审核状态
 * @property int|null $status 状态
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class ProductServiceMap extends \common\models\base\BaseModel
{
    use MerchantBehavior, HasOneMerchant;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_common_product_service_map}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'service_id', 'audit_time', 'audit_status', 'status', 'created_at', 'updated_at'], 'integer'],
            [['refusal_cause'], 'string', 'max' => 255],
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
            'service_id' => '服务ID',
            'refusal_cause' => '拒绝原因',
            'audit_time' => '审核时间',
            'audit_status' => '审核状态',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductService()
    {
        return $this->hasOne(ProductService::class, ['id' => 'service_id'])
            ->select(['id', 'name', 'cover', 'explain'])
            ->where(['status' => StatusEnum::ENABLED])
            ->orderBy('sort asc');
    }
}
