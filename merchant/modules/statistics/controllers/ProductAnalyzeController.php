<?php

namespace addons\TinyShop\merchant\modules\statistics\controllers;

use Yii;
use common\helpers\ResultHelper;
use addons\TinyShop\merchant\controllers\BaseController;

/**
 * Class ProductAnalyzeController
 * @package addons\TinyShop\merchant\modules\statistics\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class ProductAnalyzeController extends BaseController
{
    /**
     * 销售排行榜
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render($this->action->id, [
            'models' => Yii::$app->tinyShopService->product->getRank(),
        ]);
    }

    /**
     * 商品构成比率
     *
     * @return array|mixed
     */
    public function actionProductType()
    {
        $data = Yii::$app->tinyShopService->product->getGroupVirtual();

        return ResultHelper::json(200, '获取成功', $data);
    }

    /**
     * 商品售出分析
     */
    public function actionSusRes($type = '')
    {
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->tinyShopService->order->getBetweenProductCountAndCountStatToEchant($type);

            return ResultHelper::json(200, '获取成功', $data);
        }

        return $this->render($this->action->id, [
            'total' => Yii::$app->tinyShopService->order->getStatByTime(0),
        ]);
    }
}