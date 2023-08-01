<?php

namespace addons\TinyShop\merchant\modules\statistics\controllers;

use Yii;
use common\helpers\ResultHelper;
use common\enums\UseStateEnum;
use addons\TinyShop\merchant\controllers\BaseController;

/**
 * Class CouponController
 * @package addons\TinyShop\merchant\modules\statistics\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class CouponController extends BaseController
{
    /**
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index', [
            'groupCount' => Yii::$app->tinyShopService->marketingCoupon->findCountByState()
        ]);
    }

    /**
     * @param $type
     * @return array|mixed
     */
    public function actionGetCount($type)
    {
        $data = Yii::$app->tinyShopService->marketingCoupon->getBetweenCountStatToEchant($type, UseStateEnum::GET);

        return ResultHelper::json(200, '获取成功', $data);
    }

    /**
     * @param $type
     * @return array|mixed
     */
    public function actionUnsedCount($type)
    {
        $data = Yii::$app->tinyShopService->marketingCoupon->getBetweenCountStatToEchant($type, UseStateEnum::USE);

        return ResultHelper::json(200, '获取成功', $data);
    }

    /**
     * @param $type
     * @return array|mixed
     */
    public function actionPastDueCount($type)
    {
        $data = Yii::$app->tinyShopService->marketingCoupon->getBetweenCountStatToEchant($type, UseStateEnum::PAST_DUE);

        return ResultHelper::json(200, '获取成功', $data);
    }
}
