<?php

namespace addons\TinyShop\common\models\marketing;

use common\behaviors\MerchantBehavior;

/**
 * This is the model class for table "{{%addon_shop_marketing_mini_program_live_goods}}".
 *
 * @property int $id id
 * @property int $merchant_id 商户id
 * @property int $live_id 关联id
 * @property string $name 产品名称
 * @property string $cover 产品照片
 * @property string $url 跳转链接
 * @property string $price 产品价格
 * @property int $status 状态[-1:删除;0:禁用;1启用]
 * @property int $created_at 创建时间
 * @property int $updated_at 修改时间
 */
class MiniProgramLiveGoods extends \common\models\base\BaseModel
{
    use MerchantBehavior;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_shop_marketing_mini_program_live_goods}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'live_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['price'], 'number'],
            [['name', 'cover', 'url'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'id',
            'merchant_id' => '商户id',
            'live_id' => '关联id',
            'name' => '产品名称',
            'cover' => '产品照片',
            'url' => '跳转链接',
            'price' => '产品价格',
            'status' => '状态[-1:删除;0:禁用;1启用]',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }
}
