<?php

namespace addons\TinyShop\common\components\marketing;

use addons\TinyShop\common\enums\OrderTypeEnum;
use Yii;
use addons\TinyShop\common\models\forms\PreviewForm;
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
     * @return PreviewForm|mixed
     */
    public function execute(PreviewForm $form): PreviewForm
    {
        $form->is_full_mail = Yii::$app->tinyShopService->marketingFullMail->postage($form->product_money, $form->address);
        if ($form->is_full_mail == true) {
            // 成功触发
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
        return [
            PickupHandler::getName(), // 自提
        ];
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