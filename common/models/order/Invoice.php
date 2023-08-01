<?php

namespace addons\TinyShop\common\models\order;

use common\enums\AuditStatusEnum;
use common\traits\HasOneMember;

/**
 * This is the model class for table "{{%addon_tiny_shop_order_invoice}}".
 *
 * @property int $id
 * @property int|null $merchant_id 商户id
 * @property int|null $store_id 店铺ID
 * @property int|null $member_id 用户ID
 * @property int|null $order_id 订单ID
 * @property string|null $order_sn 订单编号
 * @property string|null $title 公司抬头
 * @property string|null $duty_paragraph 公司税号
 * @property string|null $opening_bank 公司开户行
 * @property string|null $opening_bank_account 公司开户行账号
 * @property string|null $address 公司地址
 * @property string|null $phone 公司电话
 * @property string|null $remark 备注
 * @property string|null $explain 说明
 * @property int|null $type 类型 1企业 2个人
 * @property float|null $tax_money 税费
 * @property int|null $audit_status 开具状态
 * @property int|null $audit_time 开具时间
 * @property int|null $status 状态
 * @property int|null $created_at 创建时间
 * @property int|null $updated_at 修改时间
 */
class Invoice extends \common\models\base\BaseModel
{
    use HasOneMember;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_order_invoice}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'store_id', 'member_id', 'order_id', 'type', 'audit_status', 'audit_time', 'status', 'created_at', 'updated_at'], 'integer'],
            [['tax_money'], 'number'],
            [['order_sn'], 'string', 'max' => 30],
            [['title', 'duty_paragraph', 'opening_bank'], 'string', 'max' => 200],
            [['opening_bank_account'], 'string', 'max' => 100],
            [['address', 'remark', 'explain'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 50],
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
            'store_id' => '店铺ID',
            'member_id' => '用户ID',
            'order_id' => '订单ID',
            'order_sn' => '订单编号',
            'title' => '公司抬头',
            'duty_paragraph' => '公司税号',
            'opening_bank' => '公司开户行',
            'opening_bank_account' => '公司开户行账号',
            'address' => '公司地址',
            'phone' => '公司电话',
            'remark' => '备注',
            'explain' => '说明',
            'type' => '类型', // 1 企业 2 个人
            'tax_money' => '税费',
            'audit_status' => '开具状态',
            'audit_time' => '开具时间',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }

    /**
     * 关联订单
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }

    /**
     * @param $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if ($this->audit_status == AuditStatusEnum::ENABLED && $this->oldAttributes['audit_status'] != AuditStatusEnum::ENABLED) {
            $this->audit_time = time();
        }

        return parent::beforeSave($insert);
    }
}
