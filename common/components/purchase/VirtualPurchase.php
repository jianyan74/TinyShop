<?php

namespace addons\TinyShop\common\components\purchase;

use addons\TinyShop\common\models\forms\PreviewForm;
use addons\TinyShop\common\enums\OrderTypeEnum;

/**
 * 虚拟下单
 *
 * Class VirtualPurchase
 * @package addons\TinyShop\common\components\purchase
 * @author jianyan74 <751393839@qq.com>
 */
class VirtualPurchase extends BuyNowPurchase
{
    /**
     * @param PreviewForm $form
     * @return PreviewForm
     * @throws \yii\web\NotFoundHttpException
     * @throws \yii\web\UnprocessableEntityHttpException
     */
    public function execute(PreviewForm $form): PreviewForm
    {
        $form = parent::execute($form);
        $form->order_type = OrderTypeEnum::VIRTUAL;

        return $form;
    }
}