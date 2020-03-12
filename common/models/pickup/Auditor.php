<?php

namespace addons\TinyShop\common\models\pickup;

use common\behaviors\MerchantBehavior;
use common\models\member\Member;
use Yii;

/**
 * This is the model class for table "{{%addon_shop_pickup_auditor}}".
 *
 * @property int $id 审核人id
 * @property int $member_id 用户id
 * @property int $pickup_point_id 自提点门店id
 * @property int $status 状态
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class Auditor extends \common\models\base\BaseModel
{
    use MerchantBehavior;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_shop_pickup_auditor}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['member_id', 'pickup_point_id'], 'required'],
            [['member_id', 'pickup_point_id', 'status', 'created_at', 'updated_at'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => '用户',
            'pickup_point_id' => '自提点',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPickupPoint()
    {
        return $this->hasOne(Point::class, ['id' => 'pickup_point_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasOne(Member::class, ['id' => 'member_id']);
    }
}
