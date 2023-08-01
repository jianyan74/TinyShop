<?php

namespace addons\TinyShop\common\models\common;

use common\behaviors\MerchantBehavior;
use Yii;

/**
 * This is the model class for table "{{%addon_tiny_shop_common_spec_template}}".
 *
 * @property int $id
 * @property int|null $merchant_id 商户id
 * @property string $title 模板名称
 * @property string $spec_ids 关联规格
 * @property int $sort 排序
 * @property int $status 状态(-1:已删除,0:禁用,1:正常)
 * @property int|null $created_at 创建时间
 * @property int|null $updated_at 修改时间
 */
class SpecTemplate extends \common\models\base\BaseModel
{
    use MerchantBehavior;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_common_spec_template}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['title', 'spec_ids'], 'required'],
            [['spec_ids'], 'safe'],
            [['title'], 'string', 'max' => 50],
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
            'title' => '模板名称',
            'spec_ids' => '关联规格',
            'sort' => '排序',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }
}
