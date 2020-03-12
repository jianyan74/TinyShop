<?php

namespace addons\TinyShop\merchant\modules\statistics\controllers;

use Yii;
use common\helpers\ResultHelper;
use addons\TinyShop\merchant\controllers\BaseController;

/**
 * Class SusResController
 * @package addons\TinyShop\merchant\modules\statistics\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class SusResController extends BaseController
{
    /**
     * 运营报告
     *
     * 24小时/周/月下单商品数
     * 24小时/周/月下单金额
     */
    public function actionIndex()
    {
        return $this->render($this->action->id, [
            'total' => Yii::$app->tinyShopService->order->getStatByTime(0),
        ]);
    }

    /**
     * @return array|mixed
     */
    public function actionData()
    {
        $type = Yii::$app->request->get('type');
        $data = Yii::$app->tinyShopService->order->getBetweenProductMoneyAndCountStatToEchant($type);

        return ResultHelper::json(200, '获取成功', $data);
    }
}