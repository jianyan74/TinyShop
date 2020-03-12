<?php

namespace addons\TinyShop\common\models\marketing;

use common\behaviors\MerchantBehavior;

/**
 * This is the model class for table "{{%addon_shop_marketing_full_mail}}".
 *
 * @property int $id
 * @property int $merchant_id 店铺id
 * @property int $is_open 是否开启 0未开启 1已开启
 * @property string $full_mail_money 包邮所需订单金额
 * @property string $no_mail_province_ids 不包邮省id组
 * @property string $no_mail_city_ids 不包邮市id组
 * @property int $status 状态[-1:删除;0:禁用;1启用]
 * @property string $created_at 添加时间
 * @property string $updated_at 修改时间
 */
class FullMail extends \common\models\base\BaseModel
{
    use MerchantBehavior;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_shop_marketing_full_mail}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'is_open', 'status', 'created_at', 'updated_at'], 'integer'],
            [['full_mail_money'], 'number', 'min' => 0],
            [['no_mail_province_ids', 'no_mail_city_ids'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'merchant_id' => 'Merchat ID',
            'is_open' => '满额包邮',
            'full_mail_money' => '包邮所需订单金额',
            'no_mail_province_ids' => '未包邮省',
            'no_mail_city_ids' => '未包邮区',
            'status' => '状态',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
