<?php

namespace addons\TinyShop\merchant\modules\statistics\controllers;

use common\helpers\ResultHelper;
use Yii;
use addons\TinyShop\merchant\controllers\BaseController;

/**
 * Class TransactionAnalyzeController
 * @package addons\TinyShop\merchant\modules\statistics\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class TransactionAnalyzeController extends BaseController
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
    public function actionOrderMoney()
    {
        $type = Yii::$app->request->get('type');
        $data = Yii::$app->tinyShopService->order->getBetweenProductMoneyAndCountStatToEchant($type);

        return ResultHelper::json(200, '获取成功', $data);
    }

    /**
     * @return array|mixed
     */
    public function actionOrderCreateCount()
    {
        $type = Yii::$app->request->get('type');
        $data = Yii::$app->tinyShopService->order->getOrderCreateCountStat($type);

        return ResultHelper::json(200, '获取成功', $data);
    }

    /**
     * 订单来源
     *
     * @return array|mixed
     */
    public function actionOrderFrom()
    {
        $type = Yii::$app->request->get('type');
        $data = Yii::$app->tinyShopService->order->getFormStat($type);

        return ResultHelper::json(200, '获取成功', $data);
    }

    /**
     * 订单类型
     *
     * @return array|mixed
     */
    public function actionOrderType()
    {
        $type = Yii::$app->request->get('type');
        $data = Yii::$app->tinyShopService->order->getOrderTypeStat($type);

        return ResultHelper::json(200, '获取成功', $data);
    }
}