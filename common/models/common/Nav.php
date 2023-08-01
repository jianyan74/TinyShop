<?php

namespace addons\TinyShop\common\models\common;

use common\behaviors\MerchantBehavior;
use Yii;

/**
 * This is the model class for table "{{%addon_tiny_shop_common_nav}}".
 *
 * @property int $id 序号
 * @property int|null $merchant_id 商户id
 * @property string $name 标识
 * @property string|null $data 内容
 * @property int|null $status 状态
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class Nav extends \common\models\base\BaseModel
{
    use MerchantBehavior;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_common_nav}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['data'], 'safe'],
            [['name'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '序号',
            'merchant_id' => '商户id',
            'name' => '标识',
            'data' => '内容',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
