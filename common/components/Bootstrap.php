<?php

namespace addons\TinyShop\common\components;

use Yii;
use common\enums\AppEnum;
use common\enums\StatusEnum;
use common\interfaces\AddonWidget;
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
     */
    public function run($addon)
    {
        // 名称
        $setting = Yii::$app->tinyShopService->config->setting();
        if (
            in_array(Yii::$app->id, AppEnum::api()) &&
            $setting->site_status == StatusEnum::DISABLED &&
            (Yii::$app->request->isPost || Yii::$app->request->isPut || Yii::$app->request->isDelete)
        ) {
            throw new UnprocessableEntityHttpException($setting->site_close_explain);
        }

        Yii::$app->params['store_id'] = '';

        if (!empty(Yii::$app->cache->get('tinyShopBootstrap')) && !YII_DEBUG) {
            return false;
        } else {
            Yii::$app->cache->set('tinyShopBootstrap', 'tiny-shop', 5);
        }

        try {
            // 自动收货
            Yii::$app->tinyShopService->orderBatch->signAll();
            // 完成订单
            Yii::$app->tinyShopService->orderBatch->finalizeAll($setting);
            // 关闭订单
            Yii::$app->tinyShopService->orderBatch->closeAll();
            // 关闭优惠券
            Yii::$app->tinyShopService->marketingCoupon->closeAll();
            // 自动评价
            Yii::$app->tinyShopService->productEvaluate->autoEvaluate();
        } catch (\Exception $e) {
            // 记录行为日志
            Yii::$app->services->log->push(500, 'autoDisposeOrder', Yii::$app->services->base->getErrorInfo($e));
            Yii::error($e->getMessage());
        }
    }
}
