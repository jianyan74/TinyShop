<?php

namespace addons\TinyShop\merchant\modules\statistics\controllers;

use Yii;
use common\helpers\ResultHelper;
use addons\TinyShop\merchant\controllers\BaseController;

/**
 * Class ProductHotController
 * @package addons\TinyShop\merchant\modules\statistics\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class ProductHotController extends BaseController
{
    /**
     * 商品热卖
     *
     * 热卖商品金额TOP30
     * 热卖商品数量TOP30
     */
    public function actionIndex()
    {
        return $this->render($this->action->id, [
        ]);
    }

    /**
     * 下单量
     *
     * @return array|mixed
     */
    public function actionCountData()
    {
        $type = Yii::$app->request->get('type');
        $data = Yii::$app->tinyShopService->orderProduct->getMaxCountMoney($type, 30);

        return ResultHelper::json(200, '获取成功', $data);
    }

    /**
     * 下单金额
     *
     * @return array|mixed
     */
    public function actionMoneyData()
    {
        $type = Yii::$app->request->get('type');
        $data = Yii::$app->tinyShopService->orderProduct->getMaxCountMoney($type, 30, 'price');

        return ResultHelper::json(200, '获取成功', $data);
    }
}