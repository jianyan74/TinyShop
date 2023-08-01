<?php

namespace addons\TinyShop\common\models\common;

use Yii;

/**
 * This is the model class for table "{{%addon_tiny_shop_common_spec_value}}".
 *
 * @property int $id
 * @property int|null $merchant_id 商户id
 * @property int $spec_id 属性编码
 * @property string $title 选项名称
 * @property string|null $data 默认数据
 * @property int $sort 排序
 * @property int $is_tmp 临时状态
 * @property int $status 状态(-1:已删除,0:禁用,1:正常)
 * @property int|null $created_at 创建时间
 * @property int|null $updated_at 修改时间
 */
class SpecValue extends \common\models\base\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_common_spec_value}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'spec_id', 'is_tmp', 'sort', 'status', 'created_at', 'updated_at'], 'integer'],
            [['spec_id', 'title'], 'required'],
            [['title'], 'string', 'max' => 125],
            [['data'], 'string', 'max' => 100],
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
            'spec_id' => '属性编码',
            'title' => '选项名称',
            'data' => '默认数据',
            'is_tmp' => '临时',
            'sort' => '排序',
            'status' => '状态(-1:已删除,0:禁用,1:正常)',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }
}
