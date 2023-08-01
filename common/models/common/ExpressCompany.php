<?php

namespace addons\TinyShop\common\models\common;

use Yii;
use common\enums\StatusEnum;
use common\behaviors\MerchantBehavior;

/**
 * This is the model class for table "{{%addon_tiny_shop_common_express_company}}".
 *
 * @property int $id
 * @property int|null $merchant_id 商户id
 * @property string $title 物流公司名称
 * @property string $express_no 物流编号
 * @property string|null $cover 封面
 * @property string|null $mobile 手机号码
 * @property int|null $sort 排序
 * @property int|null $is_default 默认
 * @property int|null $status 状态[-1:删除;0:禁用;1启用]
 * @property int|null $created_at 创建时间
 * @property int|null $updated_at 更新时间
 */
class ExpressCompany extends \common\models\base\BaseModel
{
    use MerchantBehavior;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_common_express_company}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'express_no'], 'required'],
            [['merchant_id', 'sort', 'is_default', 'status', 'created_at', 'updated_at'], 'integer'],
            [['title'], 'string', 'max' => 50],
            [['express_no', 'mobile'], 'string', 'max' => 20],
            [['cover'], 'string', 'max' => 200],
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
            'title' => '公司名称',
            'express_no' => '编号',
            'cover' => '封面',
            'mobile' => '手机号码',
            'sort' => '排序',
            'is_default' => '默认',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (($this->isNewRecord || $this->oldAttributes['is_default'] == StatusEnum::DISABLED) && $this->is_default == StatusEnum::ENABLED) {
            self::updateAll(['is_default' => StatusEnum::DISABLED], ['merchant_id' => Yii::$app->services->merchant->getNotNullId(), 'is_default' => StatusEnum::ENABLED]);
        }

        return parent::beforeSave($insert);
    }
}
