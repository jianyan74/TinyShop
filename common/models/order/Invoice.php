<?php

namespace addons\TinyShop\common\models\order;

use common\models\member\Member;

/**
 * This is the model class for table "{{%addon_shop_order_invoice}}".
 *
 * @property string $id
 * @property string $merchant_id 商户id
 * @property int $order_id 订单id
 * @property int $order_sn 订单编号
 * @property string $member_id 用户id
 * @property string $title 公司抬头
 * @property string $duty_paragraph 税号
 * @property string $content 内容
 * @property int $type 类型 1企业 2个人
 * @property int $status 状态(-1:已删除,0:禁用,1:正常)
 * @property string $created_at 创建时间
 * @property string $updated_at 修改时间
 */
class Invoice extends \common\models\base\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_shop_order_invoice}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'type'], 'required'],
            [['merchant_id', 'order_sn', 'order_id', 'member_id', 'type', 'status', 'created_at', 'updated_at'], 'integer'],
            [['title', 'duty_paragraph', 'user_name', 'opening_bank', 'address'], 'string', 'max' => 200],
            [['content'], 'string', 'max' => 500],
            ['tax_money', 'number'],
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
            'order_id' => '订单id',
            'order_sn' => '订单编号',
            'tax_money' => '税额',
            'member_id' => '用户id',
            'user_name' => '用户昵称',
            'title' => '公司抬头',
            'duty_paragraph' => '纳税人识别号',
            'opening_bank' => '开户行',
            'address' => '地址及电话',
            'content' => '备注',
            'type' => '类型',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }

    /**
     * 关联用户
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasOne(Member::class, ['id' => 'member_id']);
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
}
