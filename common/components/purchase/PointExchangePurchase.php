<?php

namespace addons\TinyShop\common\components\purchase;

use addons\TinyShop\common\enums\PointExchangeTypeEnum;
use addons\TinyShop\common\models\order\OrderProduct;
use addons\TinyShop\common\models\product\Product;
use common\enums\PayTypeEnum;
use addons\TinyShop\common\models\forms\PreviewForm;
use yii\web\UnprocessableEntityHttpException;

/**
 * 积分下单
 *
 * Class PointExchangePurchase
 * @package addons\TinyShop\common\components\purchase
 * @author jianyan74 <751393839@qq.com>
 */
class PointExchangePurchase extends BuyNowPurchase
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

        /** @var OrderProduct $product */
        $product = $form->orderProducts[0];
        /** @var Product $product */
        $defaultProduct = $form->defaultProducts[0];
        // 所需积分
        $form->point = $defaultProduct['point_exchange'] * $form->product_count;

        // 只支持积分兑换
        if (in_array($product['point_exchange_type'], [PointExchangeTypeEnum::INTEGRAL, PointExchangeTypeEnum::INTEGRAL_OR_MONEY])) {
            $form->product_money = $product->price = $product->product_money = 0;
        }

        if ($this->isNewRecord && $product['point_exchange_type'] == PointExchangeTypeEnum::NOT_EXCHANGE) {
            throw new UnprocessableEntityHttpException('不可使用积分下单');
        }

        return $form;
    }

    /**
     * 下单类型
     *
     * @return string
     */
    public static function getType(): string
    {
        return 'pointExchange';
    }
}