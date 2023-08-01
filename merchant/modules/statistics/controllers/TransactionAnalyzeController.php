<?php

namespace addons\TinyShop\merchant\modules\statistics\controllers;

use Yii;
use common\helpers\ResultHelper;
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
            'total' => Yii::$app->tinyShopService->orderStat->getStatByTime(0),
        ]);
    }

    /**
     * @return array|mixed
     */
    public function actionOrderMoney()
    {
        $type = Yii::$app->request->get('type');
        $data = Yii::$app->tinyShopService->orderStat->getBetweenProductMoneyAndCountStatToEchant($type);

        return ResultHelper::json(200, '获取成功', $data);
    }

    /**
     * @return array|mixed
     */
    public function actionOrderCreateCount()
    {
        $type = Yii::$app->request->get('type');
        $data = Yii::$app->tinyShopService->orderStat->getOrderCreateCountStat($type);

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
        $data = Yii::$app->tinyShopService->orderStat->getFormStat($type);

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
        $data = Yii::$app->tinyShopService->orderStat->getOrderTypeStat($type);

        return ResultHelper::json(200, '获取成功', $data);
    }
}
