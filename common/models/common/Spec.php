<?php

namespace addons\TinyShop\common\models\common;

use common\behaviors\MerchantBehavior;
use common\enums\StatusEnum;

/**
 * This is the model class for table "{{%addon_tiny_shop_common_spec}}".
 *
 * @property int $id
 * @property int|null $merchant_id 商户id
 * @property string $title 规格名称
 * @property int $sort 排序
 * @property int|null $type 展示方式[1:文字;2:颜色;3:图片]
 * @property string|null $explain 规格说明
 * @property int|null $is_tmp 临时数据
 * @property int $status 状态(-1:已删除,0:禁用,1:正常)
 * @property int|null $created_at 创建时间
 * @property int|null $updated_at 修改时间
 */
class Spec extends \common\models\base\BaseModel
{
    use MerchantBehavior;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_common_spec}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'sort', 'type', 'is_tmp', 'status', 'created_at', 'updated_at'], 'integer'],
            [['title'], 'required'],
            [['title'], 'string', 'max' => 50],
            [['explain'], 'string', 'max' => 100],
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
            'title' => '规格名称',
            'sort' => '排序',
            'type' => '类型',
            'explain' => '规格说明',
            'is_tmp' => '临时数据',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }

    /**
     * 关联规格值
     *
     * @return \yii\db\ActiveQuery
     */
    public function getValue()
    {
        return $this->hasMany(SpecValue::class, ['spec_id' => 'id'])->orderBy('sort asc')->andWhere(['is_tmp' => StatusEnum::DISABLED]);
    }

    public function afterDelete()
    {
        SpecValue::deleteAll(['merchant_id' => $this->merchant_id, 'spec_id' => $this->id]);

        parent::afterDelete();
    }
}
