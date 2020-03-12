<?php

namespace addons\TinyShop\merchant\modules\statistics\controllers;

use Yii;
use common\helpers\ResultHelper;
use addons\TinyShop\merchant\controllers\BaseController;

/**
 * Class GeneralController
 * @package addons\TinyShop\merchant\modules\statistics\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class GeneralController extends BaseController
{
    /**
     * 销售分析
     *
     * 近30天下单金额
     * 近30天下单会员数
     * 近30天下单量
     * 近30天下单商品数
     * 近30天平均客单价
     * 近30天平均价格
     *
     * @return array|string
     */
    public function actionIndex()
    {
        $orderStat = Yii::$app->tinyShopService->order->getStatByTime(time() - 60 * 60 * 24 * 30, ['count(distinct buyer_id) as member_count']);
        // 客单价
        $orderStat['customer_money'] = $orderStat['pay_money'] > 0 ? round($orderStat['pay_money'] / $orderStat['member_count'], 2) : 0;
        // 平均价格
        $orderStat['average_money'] = $orderStat['pay_money'] > 0 ? round($orderStat['pay_money'] / $orderStat['product_count'], 2) : 0;

        return $this->render($this->action->id, [
            'orderStat' => $orderStat,
        ]);
    }

    /**
     * @return array|mixed
     */
    public function actionData()
    {
        $type = Yii::$app->request->get('type');
        $data = Yii::$app->tinyShopService->order->getBetweenCountStatToEchant($type);

        return ResultHelper::json(200, '获取成功', $data);
    }
}