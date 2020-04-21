<?php

namespace addons\TinyShop\common\models\base;

use common\behaviors\MerchantBehavior;

/**
 * This is the model class for table "{{%addon_shop_base_local_distribution_member}}".
 *
 * @property string $id
 * @property string $merchant_id 商户id
 * @property string $member_id 用户id
 * @property string $name 配送人员姓名
 * @property string $mobile 配送人员电话
 * @property string $remark 配送人员备注
 * @property int $status 状态[-1:删除;0:禁用;1启用]
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class LocalDistributionMember extends \common\models\base\BaseModel
{
    use MerchantBehavior;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_shop_base_local_distribution_member}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'mobile'], 'required'],
            [['merchant_id', 'member_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['remark'], 'string'],
            [['name', 'mobile'], 'string', 'max' => 255],
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
            'member_id' => '用户id',
            'name' => '配送人员姓名',
            'mobile' => '配送人员电话',
            'remark' => '配送人员备注',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
