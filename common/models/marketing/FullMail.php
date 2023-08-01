<?php

namespace addons\TinyShop\common\models\marketing;

use common\behaviors\MerchantBehavior;

/**
 * This is the model class for table "{{%addon_tiny_shop_marketing_full_mail}}".
 *
 * @property int $id
 * @property int $merchant_id 店铺id
 * @property float $full_mail_money 包邮所需订单金额
 * @property string|null $no_mail_province_ids 不包邮省id组
 * @property string|null $no_mail_city_ids 不包邮市id组
 * @property int|null $status 状态[-1:删除;0:禁用;1启用]
 * @property int|null $created_at 添加时间
 * @property int|null $updated_at 修改时间
 */
class FullMail extends \common\models\base\BaseModel
{
    use MerchantBehavior;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_marketing_full_mail}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['full_mail_money'], 'number', 'min' => 0],
            [['full_mail_money', 'status'], 'required'],
            [['no_mail_province_ids', 'no_mail_city_ids'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'merchant_id' => '店铺id',
            'full_mail_money' => '包邮所需订单金额',
            'no_mail_province_ids' => '不包邮省id组',
            'no_mail_city_ids' => '不包邮市id组',
            'status' => '状态',
            'created_at' => '添加时间',
            'updated_at' => '修改时间',
        ];
    }
}
