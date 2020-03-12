<?php


namespace addons\TinyShop\merchant\modules\statistics\controllers;

use Yii;
use addons\TinyShop\merchant\controllers\BaseController;

/**
 * Class SalesRankingController
 * @package addons\TinyShop\merchant\modules\statistics\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class SalesRankingController extends BaseController
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
}