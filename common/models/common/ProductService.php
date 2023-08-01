<?php

namespace addons\TinyShop\common\models\common;

use Yii;
use common\enums\AuditStatusEnum;
use common\behaviors\MerchantBehavior;

/**
 * This is the model class for table "{{%addon_tiny_shop_common_product_service}}".
 *
 * @property int $id
 * @property int|null $merchant_id 商户id
 * @property string|null $name 服务名称
 * @property string|null $cover 服务图标
 * @property string|null $explain 服务说明
 * @property int|null $sort 排序
 * @property int|null $status 状态
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class ProductService extends \common\models\base\BaseModel
{
    use MerchantBehavior;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_common_product_service}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'sort'], 'required'],
            [['merchant_id', 'sort', 'status', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['cover'], 'string', 'max' => 200],
            [['explain'], 'string', 'max' => 255],
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
            'name' => '服务名称',
            'cover' => '服务图标',
            'explain' => '服务说明',
            'sort' => '排序',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMap()
    {
        return $this->hasOne(ProductServiceMap::class, ['service_id' => 'id'])
            ->where(['merchant_id' => Yii::$app->services->merchant->getNotNullId()]);
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        if ($insert == true) {
            $model = new ProductServiceMap();
            $model = $model->loadDefaultValues();
            $model->merchant_id = $this->merchant_id;
            $model->service_id = $this->id;
            $model->audit_status = AuditStatusEnum::ENABLED;
            $model->save();
        }

        parent::afterSave($insert, $changedAttributes);
    }

    public function afterDelete()
    {
        ProductServiceMap::deleteAll(['service_id' => $this->id]);

        parent::afterDelete();
    }
}
