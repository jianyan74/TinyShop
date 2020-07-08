<?php

namespace addons\TinyShop\common\models\marketing;

use addons\TinyShop\common\traits\HasOneProduct;
use common\behaviors\MerchantBehavior;

/**
 * This is the model class for table "{{%addon_shop_marketing_time_manage}}".
 *
 * @property int $id
 * @property int $merchant_id 店铺编号
 * @property int $product_id 商品id
 * @property int $marketing_id 营销id
 * @property string $marketing_type 营销类型
 * @property int $start_time 开始时间
 * @property int $end_time 结束时间
 * @property int $status 状态(-1:已删除,0:禁用,1:正常)
 * @property int $created_at 创建时间
 * @property int $updated_at 修改时间
 */
class TimeManage extends \common\models\base\BaseModel
{
    use MerchantBehavior, HasOneProduct;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_shop_marketing_time_manage}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'product_id', 'marketing_id', 'start_time', 'end_time', 'status', 'created_at', 'updated_at'], 'integer'],
            [['marketing_type'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'merchant_id' => '店铺编号',
            'product_id' => '商品id',
            'marketing_id' => '营销id',
            'marketing_type' => '营销类型',
            'start_time' => '开始时间',
            'end_time' => '结束时间',
            'status' => '状态(-1:已删除,0:禁用,1:正常)',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }
}
