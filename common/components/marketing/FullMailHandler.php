<?php

namespace addons\TinyShop\common\components\marketing;

use Yii;
use addons\TinyShop\common\enums\MarketingEnum;
use addons\TinyShop\common\forms\PreviewForm;
use addons\TinyShop\common\components\PreviewInterface;

/**
 * 满额包邮
 *
 * Class FullMailHandler
 * @package addons\TinyShop\common\components
 * @author jianyan74 <751393839@qq.com>
 */
class FullMailHandler extends PreviewInterface
{
    /**
     * @param PreviewForm $form
     * @return PreviewForm
     */
    public function execute(PreviewForm $form): PreviewForm
    {
        // 满减送包邮成功触发
        if ($form->is_full_mail == true) {
            return $this->success($form);
        }

        $fullMail = Yii::$app->tinyShopService->marketingFullMail->postage($form->product_money, $form->address, $form->merchant_id);
        // 满包邮成功触发
        if ($fullMail) {
            $form->is_full_mail = true;
            // 触发营销
            $form->marketingDetails[] = [
                'marketing_id' => $fullMail['id'],
                'marketing_type' => MarketingEnum::FULL_MAIL,
                'marketing_condition' => '满' . $fullMail['full_mail_money'] . '元包邮',
                'free_shipping' => 1,
                'discount_money' => $form->shipping_money,
            ];

            return $this->success($form);
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
        return 'fullMail';
    }
}
