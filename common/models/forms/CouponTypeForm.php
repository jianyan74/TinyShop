<?php

namespace addons\TinyShop\common\models\forms;

use Yii;
use yii\base\Model;
use common\enums\StatusEnum;
use addons\TinyShop\common\models\marketing\CouponType;

/**
 * Class CouponTypeForm
 * @package addons\TinyShop\common\models\forms
 * @author jianyan74 <751393839@qq.com>
 */
class CouponTypeForm extends Model
{
    public $id;
    public $member_id;

    /**
     * @var CouponType
     */
    public $couponType;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            ['id', 'required'],
            ['id', 'valid'],
        ];
    }

    /**
     * @param $attribute
     */
    public function valid($attribute)
    {
        if (!($this->couponType = Yii::$app->tinyShopService->marketingCouponType->findById($this->id))) {
            return $this->addError($attribute, '找不到该优惠券');
        }

        if ($this->couponType->status != StatusEnum::ENABLED) {
            return $this->addError($attribute, '优惠券已失效');
        }

        // 可领取时间判断
        if (time() <= $this->couponType->get_start_time || time() >= $this->couponType->get_end_time) {
            return $this->addError($attribute, '优惠券不在可领取时间内');
        }

        // 验证可领取数量
        if ($this->couponType->max_fetch > 0 && Yii::$app->tinyShopService->marketingCoupon->findCountById($this->id,
                $this->member_id) >= $this->couponType->max_fetch) {
            return $this->addError($attribute, '不能在领取了');
        }
    }
}