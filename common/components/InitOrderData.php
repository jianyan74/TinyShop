<?php

namespace addons\TinyShop\common\components;

use yii\web\UnprocessableEntityHttpException;
use addons\TinyShop\common\enums\MarketingEnum;
use addons\TinyShop\common\forms\PreviewForm;
use addons\TinyShop\common\components\purchase\CartPurchase;
use addons\TinyShop\common\components\purchase\BuyNowPurchase;
use addons\TinyShop\common\components\purchase\BuyAgainPurchase;

/**
 * 初始化订单数据
 *
 * Class InitOrderData
 * @package addons\TinyShop\common\components\purchase
 * @author jianyan74 <751393839@qq.com>
 */
class InitOrderData
{
    /**
     * 创建记录
     *
     * @var bool
     */
    public $isNewRecord = false;

    /**
     * 下单方式
     *
     * @var array
     */
    protected $handlers = [
        MarketingEnum::CART => CartPurchase::class, // 购物车
        MarketingEnum::BUY_NOW => BuyNowPurchase::class, // 立即下单
        MarketingEnum::BUY_AGAIN => BuyAgainPurchase::class, // 再次下单
    ];

    /**
     * 必须保证返回有商品信息不然报错
     *
     * @param PreviewForm $previewForm
     * @param $type
     * @return PreviewForm|mixed
     * @throws \yii\web\NotFoundHttpException
     * @throws \yii\web\UnprocessableEntityHttpException
     */
    public function execute(PreviewForm $previewForm, $type): PreviewForm
    {
        if (!isset($this->handlers[$type])) {
            throw new UnprocessableEntityHttpException('下单类型错误');
        }

        /** @var InitOrderDataInterface $class */
        $class = new $this->handlers[$type]();
        $class->isNewRecord = $this->isNewRecord;
        $previewForm = $class->execute($previewForm);
        if (!$previewForm->orderProducts || !$previewForm->sku) {
            throw new UnprocessableEntityHttpException('找不到可用的商品');
        }

        $previewForm = $class->afterExecute($previewForm, $class::getType());

        return $previewForm;
    }
}
