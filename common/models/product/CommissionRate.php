<?php

namespace addons\TinyShop\common\models\product;

use Yii;

/**
 * This is the model class for table "{{%addon_shop_product_commission_rate}}".
 *
 * @property int $id
 * @property int $merchant_id 商户id
 * @property int $product_id 商品ID
 * @property double $distribution_commission_rate 分销佣金比率
 * @property double $regionagent_commission_rate 区域代理分红佣金比率
 * @property int $status 是否启用分销
 * @property int $created_at 创建时间
 * @property int $updated_at 修改时间
 */
class CommissionRate extends \common\models\base\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_shop_product_commission_rate}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'product_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['product_id'], 'required'],
            [['distribution_commission_rate', 'regionagent_commission_rate'], 'number', 'min' => 0, 'max' => 100],
            [['distribution_commission_rate', 'regionagent_commission_rate'], 'verifyMax'],
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
            'product_id' => '商品ID',
            'distribution_commission_rate' => '分销佣金比率(%)',
            'regionagent_commission_rate' => '区域代理分红佣金比率(%)',
            'status' => '是否启用分销',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }

    /**
     * @param $attribute
     */
    public function verifyMax($attribute)
    {
        if (($this->distribution_commission_rate + $this->regionagent_commission_rate) > 100) {
            $this->addError($attribute, '佣金比率相加不得超过100%');
        }
    }
}
