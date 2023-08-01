<?php

namespace addons\TinyShop\merchant\modules\marketing\forms;

use addons\TinyShop\common\enums\MarketingEnum;
use addons\TinyShop\common\models\marketing\CouponTypeMap;

/**
 * Class MarketingCouponTypeForm
 * @package addons\TinyShop\merchant\modules\marketing\forms
 */
class MarketingCouponTypeForm extends CouponTypeMap
{
    /**
     * @var array
     */
    public $couponTypes = [];

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['couponTypes'], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'couponTypes' => '优惠券',
        ];
    }

    /**
     * @param $insert
     * @param $changedAttributes
     */
    public function create()
    {
        CouponTypeMap::deleteAll(['marketing_id' => $this->marketing_id, 'marketing_type' => $this->marketing_type]);
        if (!empty($this->couponTypes)) {
            foreach ($this->couponTypes as $key => $couponType) {
                $model = new CouponTypeMap();
                $model->marketing_id = $this->marketing_id;
                $model->marketing_type = $this->marketing_type;
                $model->coupon_type_id = $couponType['id'];
                $model->number = $couponType['number'];
                $model->save();
            }
        }
    }
}
