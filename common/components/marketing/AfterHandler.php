<?php

namespace addons\TinyShop\common\components\marketing;

use Yii;
use common\helpers\ArrayHelper;
use addons\TinyShop\common\traits\CalculatePriceTrait;
use addons\TinyShop\common\models\order\OrderProduct;
use addons\TinyShop\common\models\forms\PreviewForm;
use addons\TinyShop\common\components\PreviewInterface;
use addons\TinyShop\common\enums\ProductMarketingEnum;

/**
 * 统一处理数据
 *
 * Class AfterHandler
 * @package addons\TinyShop\common\components\marketing
 * @author jianyan74 <751393839@qq.com>
 */
class AfterHandler extends PreviewInterface
{
    use CalculatePriceTrait;

    /**
     * @param PreviewForm $form
     * @return PreviewForm|mixed
     */
    public function execute(PreviewForm $form): PreviewForm
    {
        // 自动计算价格
        $this->calculatePrice($form);

        // 赠送优惠券
        $giveCouponTypeIds = ArrayHelper::getColumn($form->marketingDetails, 'give_coupon_type_id');

        // 赠送积分
        $givePoint = ArrayHelper::getColumn($form->marketingDetails, 'give_point');
        $form->give_point = array_sum($givePoint);

        foreach ($form->marketingDetails as &$detail) {
            $detail['marketing_name'] = ProductMarketingEnum::getValue($detail['marketing_type']);
        }

        return $form;
    }

    /**
     * 排斥营销
     *
     * @return array
     */
    public function rejectNames()
    {
        return [];
    }

    /**
     * 营销名称
     *
     * @return string
     */
    public static function getName(): string
    {
        return 'give';
    }
}