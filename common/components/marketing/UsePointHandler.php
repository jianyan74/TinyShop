<?php

namespace addons\TinyShop\common\components\marketing;

use addons\TinyShop\common\enums\ProductMarketingEnum;
use Yii;
use common\enums\StatusEnum;
use common\enums\PayTypeEnum;
use addons\TinyShop\common\models\forms\PreviewForm;
use addons\TinyShop\common\components\PreviewInterface;

/**
 * 积分抵现
 *
 * Class UsePointHandler
 * @package addons\TinyShop\common\components
 * @author jianyan74 <751393839@qq.com>
 */
class UsePointHandler extends PreviewInterface
{
    /**
     * @param PreviewForm $form
     * @return PreviewForm
     */
    public function execute(PreviewForm $form): PreviewForm
    {
        // 积分下单不可使用抵现
        if ($form->payment_type == PayTypeEnum::INTEGRAL) {
            return $form;
        }

        // 积分抵现
        $form->point_money = 0;
        // 所需积分
        $form->point = 0;
        // 积分抵现开启状态
        $pointConfig = Yii::$app->tinyShopService->marketingPointConfig->one();
        if ($pointConfig['is_open'] == StatusEnum::ENABLED && $form->use_point > 0) {
            $form->point = $form->use_point;
            $form->point_money = $form->use_point * $pointConfig['convert_rate'];
            // 触发营销
            $form->marketingDetails[] = [
                'marketing_id' => $pointConfig['id'],
                'marketing_type' => ProductMarketingEnum::GIVE_POINT,
                'marketing_condition' => '积分抵扣' . $form->point_money . '元',
                'discount_money' => $form->point_money,
            ];
        }

        // 成功触发
        return $this->success($form);
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
        return 'usePoint';
    }
}