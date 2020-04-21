<?php

namespace addons\TinyShop\common\components;

use Yii;
use common\helpers\AddonHelper;
use common\interfaces\AddonWidget;

/**
 * Bootstrap
 *
 * Class Bootstrap
 * @package addons\TinyShop\common\config
 */
class Bootstrap implements AddonWidget
{
    /**
     * @param $addon
     * @return mixed|void
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\NotFoundHttpException
     * @throws \yii\web\UnprocessableEntityHttpException
     */
    public function run($addon)
    {
        // TODO 临时测试
        $config = AddonHelper::getConfig();
        $merchant_id = Yii::$app->services->merchant->getId();
        Yii::$app->tinyShopService->order->signAll($config, $merchant_id); // 自动收货
        Yii::$app->tinyShopService->order->finalizeAll($config, $merchant_id); // 完成订单
        Yii::$app->tinyShopService->order->closeAll($config, $merchant_id); // 关闭订单
        // 关闭优惠券
        Yii::$app->tinyShopService->marketingCoupon->closeAll();
        // 关闭失效的虚拟商品卡卷
        // Yii::$app->tinyShopService->orderProductVirtual->closeAll();
    }
}