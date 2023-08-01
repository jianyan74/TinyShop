<?php

namespace addons\TinyShop\common\forms;

use Yii;
use yii\base\Model;
use common\enums\StatusEnum;
use addons\TinyShop\common\models\marketing\CouponType;

/**
 * Class CouponTypeForm
 * @package addons\TinyShop\common\forms
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
            $this->addError($attribute, '找不到该优惠券');
            return false;
        }

        if ($this->couponType->status != StatusEnum::ENABLED) {
            $this->addError($attribute, '优惠券已失效');
            return false;
        }

        // 可领取时间判断
        if ($this->couponType->get_start_time > time() || $this->couponType->get_end_time < time()) {
            $this->addError($attribute, '优惠券不在可领取时间内');
            return false;
        }

        // 验证每日可领取数量
        if (
            $this->couponType->max_day_fetch > 0 &&
            Yii::$app->tinyShopService->marketingCoupon->findCountById($this->id, $this->member_id, strtotime(date('Y-m-d'))) >= $this->couponType->max_day_fetch
        ) {
            $this->addError($attribute, '今日不能再领取了');
            return false;
        }

        // 验证可领取数量
        if (
            $this->couponType->max_fetch > 0 &&
            Yii::$app->tinyShopService->marketingCoupon->findCountById($this->id, $this->member_id) >= $this->couponType->max_fetch
        ) {
            $this->addError($attribute, '不能再领取了');
            return false;
        }
    }
}
