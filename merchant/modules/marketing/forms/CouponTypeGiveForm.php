<?php

namespace addons\TinyShop\merchant\modules\marketing\forms;

use yii\base\Model;

/**
 * Class CouponTypeGiveForm
 * @package addons\TinyShop\merchant\modules\marketing\forms
 * @author jianyan74 <751393839@qq.com>
 */
class CouponTypeGiveForm extends Model
{
    public $member_id;
    public $coupon_type_id;
    public $num = 1;
    public $title;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['num', 'coupon_type_id', 'member_id'], 'required'],
            [['num', 'coupon_type_id', 'member_id'], 'integer', 'min' => 1],
            [['num'], 'integer', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'num' => '数量',
            'coupon_type_id' => '优惠券',
            'member_id' => '手机号码',
            'title' => '优惠券名称',
        ];
    }
}
