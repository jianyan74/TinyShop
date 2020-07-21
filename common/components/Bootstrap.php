<?php

namespace addons\TinyShop\common\components;

use common\enums\AppEnum;
use common\enums\StatusEnum;
use Yii;
use common\helpers\AddonHelper;
use common\interfaces\AddonWidget;
use addons\TinyShop\common\models\SettingForm;
use yii\web\UnprocessableEntityHttpException;

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
        $setting = new SettingForm();
        $setting->attributes = $config;

        // 名称
        Yii::$app->params['tinyShopName'] = !empty($setting->app_name) ? "【" . $setting->app_name . "】":  "【微商城】";

        if (
            in_array(Yii::$app->id, AppEnum::api()) &&
            $setting->is_open_site == StatusEnum::DISABLED &&
            (Yii::$app->request->isPost || Yii::$app->request->isPut)
        ) {
            throw new UnprocessableEntityHttpException($setting->close_site_explain);
        }

        try {
            $merchant_id = Yii::$app->services->merchant->getId();
            Yii::$app->tinyShopService->order->signAll($config, $merchant_id); // 自动收货
            Yii::$app->tinyShopService->order->finalizeAll($config, $merchant_id); // 完成订单
            Yii::$app->tinyShopService->order->closeAll($merchant_id); // 关闭订单
            // 关闭优惠券
            Yii::$app->tinyShopService->marketingCoupon->closeAll();
            // 自动评价
            if (!empty($setting->evaluate_day)) {
                Yii::$app->tinyShopService->productEvaluate->autoEvaluate($setting->evaluate_day, $setting->evaluate);
            }
        } catch (\Exception $e) {
            Yii::error($e->getMessage());
        }
    }
}