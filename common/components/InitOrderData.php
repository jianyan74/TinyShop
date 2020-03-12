<?php

namespace addons\TinyShop\common\components;

use yii\web\UnprocessableEntityHttpException;
use addons\TinyShop\common\enums\PreviewTypeEnum;
use addons\TinyShop\common\models\forms\PreviewForm;
use addons\TinyShop\common\components\purchase\CartPurchase;
use addons\TinyShop\common\components\purchase\BuyNowPurchase;
use addons\TinyShop\common\components\purchase\PresellBuyPurchase;

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
     * @var array
     */
    public $rule = [];

    /**
     * 是否创建订单
     *
     * @var bool
     */
    public $create = false;

    /**
     * @var array
     */
    protected $handlers = [
        PreviewTypeEnum::CART => CartPurchase::class, // 购物车
        PreviewTypeEnum::BUY_NOW => BuyNowPurchase::class, // 立即下单
        PreviewTypeEnum::POINT_EXCHANGE => PresellBuyPurchase::class, // 积分
    ];

    /**
     * 必须保证返回有产品信息不然报错
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
        $previewForm = $class->execute($previewForm);
        if (!$previewForm->orderProducts || !$previewForm->sku) {
            throw new UnprocessableEntityHttpException('找不到可用的产品');
        }

        // 触发后置行为
        $previewForm = $class->afterExecute($previewForm, $class::getType(), $this->create);
        // 记录被触发的规则
        $this->rule = $class->rule;

        return $previewForm;
    }
}