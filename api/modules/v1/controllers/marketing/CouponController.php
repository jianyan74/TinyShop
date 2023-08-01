<?php

namespace addons\TinyShop\api\modules\v1\controllers\marketing;

use Yii;
use api\controllers\OnAuthController;
use yii\web\UnprocessableEntityHttpException;

/**
 * Class CouponController
 * @package addons\TinyShop\api\modules\v1\controllers\marketing
 */
class CouponController extends OnAuthController
{
    /**
     * @var string
     */
    public $modelClass = '';

    /**
     * 兑换码兑换
     *
     * @throws \yii\web\UnprocessableEntityHttpException
     */
    public function actionConversion()
    {
        $code = Yii::$app->request->post('code');
        $member_id = Yii::$app->user->identity->member_id;

        if (empty($code)) {
            throw new UnprocessableEntityHttpException('请填写兑换码');
        }

        $model = Yii::$app->tinyShopService->marketingCoupon->conversion($code, $member_id);

        return $model;
    }
}
