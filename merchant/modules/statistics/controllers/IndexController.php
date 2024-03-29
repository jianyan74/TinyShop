<?php

namespace addons\TinyShop\merchant\modules\statistics\controllers;

use common\helpers\ArrayHelper;
use Yii;
use common\helpers\DateHelper;
use common\helpers\ResultHelper;
use addons\TinyShop\merchant\controllers\BaseController;

/**
 * Class IndexController
 * @package addons\TinyShop\merchant\modules\statistics\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class IndexController extends BaseController
{
    /**
     * 首页
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionIndex()
    {
        $thisMonth = DateHelper::thisMonth();
        $yesterday = DateHelper::yesterday();
        $today = DateHelper::today();

        $yesterdayData = $todayData = [
            'product_count' => 0,
            'pay_money' => 0.00,
            'count' => 0,
        ];

        $dayStatByTime = Yii::$app->tinyShopService->orderStat->getDayStatByTime($yesterday['start']);
        foreach ($dayStatByTime as $item) {
            $time = strtotime($item['day']);

            if ($time >= $yesterday['start'] && $time <= $yesterday['end']) {
                $yesterdayData['product_count'] += (int)$item['product_count'];
                $yesterdayData['pay_money'] += (double)$item['pay_money'];
                $yesterdayData['count'] += $item['count'];
            }

            if ($time >= $today['start'] && $time <= $today['end']) {
                $todayData['product_count'] += (int)$item['product_count'];
                $todayData['pay_money'] += (double)$item['pay_money'];
                $todayData['count'] += $item['count'];
            }
        }

        return $this->render($this->action->id, [
            'orderTotalList' => ArrayHelper::map(Yii::$app->tinyShopService->order->getOrderCountGroupByStatus(), 'order_status', 'count'),
            'orderCount' => Yii::$app->tinyShopService->order->findCount(),
            'orderThisMouthStat' => Yii::$app->tinyShopService->orderStat->getStatByTime($thisMonth['start']),
            'productWarningStockCount' => Yii::$app->tinyShopService->product->getWarningStockCount(),
            'productSellCount' => Yii::$app->tinyShopService->product->findSellCount(),
            'productWarehouseCount' => Yii::$app->tinyShopService->product->findWarehouseCount(),
            'productEvaluateCount' => Yii::$app->tinyShopService->productEvaluate->getCount(),
            'productRank' => Yii::$app->tinyShopService->product->getRank(),
            'yesterdayData' => $yesterdayData,
            'todayData' => $todayData,
        ]);
    }

    /**
     * 运营报告
     *
     * 24小时/周/月下单商品数
     * 24小时/周/月下单金额
     */
    public function actionSusRes($type = '')
    {
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->tinyShopService->orderStat->getBetweenProductMoneyAndCountStatToEchant($type);

            return ResultHelper::json(200, '获取成功', $data);
        }

        return $this->render($this->action->id, [
            'total' => Yii::$app->tinyShopService->order->getStatByTime(0),
        ]);
    }

    /**
     * 订单指定时间内数量
     *
     * @param $type
     * @return array
     */
    public function actionOrderBetweenCount($type)
    {
        $data = Yii::$app->tinyShopService->orderStat->getBetweenCountStatToEchant($type);

        return ResultHelper::json(200, '获取成功', $data);
    }
}
