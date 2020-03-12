<?php

namespace addons\TinyShop\api\modules\v1\controllers\member;

use Yii;
use api\controllers\UserAuthController;
use addons\TinyShop\common\models\order\ProductExpress;

/**
 * Class OrderProductExpressController
 * @package addons\TinyShop\api\modules\v1\controllers\member
 * @author jianyan74 <751393839@qq.com>
 */
class OrderProductExpressController extends UserAuthController
{
    /**
     * @var ProductExpress
     */
    public $modelClass = ProductExpress::class;

    /**
     * @return array
     */
    public function actionDetails()
    {
        $member_id = Yii::$app->user->identity->member_id;
        $order_id = Yii::$app->request->get('order_id');

        return Yii::$app->tinyShopService->orderProductExpress->getStatusByOrderId($order_id, $member_id);
    }
}