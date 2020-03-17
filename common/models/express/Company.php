<?php

namespace addons\TinyShop\common\models\express;

use Yii;
use common\behaviors\MerchantBehavior;
use common\enums\StatusEnum;

/**
 * This is the model class for table "{{%addon_shop_express_company}}".
 *
 * @property string $id
 * @property string $merchant_id 商户id
 * @property string $title 物流公司名称
 * @property string $express_no 物流编号
 * @property string $cover 封面
 * @property string $mobile 手机号码
 * @property int $sort 排序
 * @property int $status 状态[-1:删除;0:禁用;1启用]
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class Company extends \common\models\base\BaseModel
{
    use MerchantBehavior;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_shop_express_company}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'express_no'], 'required'],
            [['merchant_id', 'is_default', 'sort', 'status', 'created_at', 'updated_at'], 'integer'],
            [['title'], 'string', 'max' => 50],
            [['express_no', 'mobile'], 'string', 'max' => 20],
            [['cover'], 'string', 'max' => 100],
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
            'title' => '公司名称',
            'express_no' => '编号',
            'cover' => '封面',
            'mobile' => '联系方式',
            'is_default' => '默认',
            'sort' => '排序',
            'status' => '状态',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (($this->isNewRecord || $this->oldAttributes['is_default'] == StatusEnum::DISABLED) && $this->is_default == StatusEnum::ENABLED) {
            self::updateAll(['is_default' => StatusEnum::DISABLED], ['merchant_id' => Yii::$app->services->merchant->getId(), 'is_default' => StatusEnum::ENABLED]);
        }

        return parent::beforeSave($insert);
    }
}
