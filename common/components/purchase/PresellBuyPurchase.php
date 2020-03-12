<?php

namespace addons\TinyShop\common\components\purchase;

use common\enums\PayTypeEnum;
use addons\TinyShop\common\models\forms\PreviewForm;

/**
 * 积分下单
 *
 * Class PresellBuyPurchase
 * @package addons\TinyShop\common\components\purchase
 * @author jianyan74 <751393839@qq.com>
 */
class PresellBuyPurchase extends BuyNowPurchase
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
        $form->payment_type = PayTypeEnum::INTEGRAL;

        // 产品信息
        $product = $form->defaultProducts[0];
        // 所需积分
        $form->point = $product['point_exchange'] * $form->product_count;
        $form->product_money = 0;

        return $form;
    }

    /**
     * 下单类型
     *
     * @return string
     */
    public static function getType(): string
    {
        return 'presellBuy';
    }
}