<?php

namespace addons\TinyShop\merchant\modules\statistics\controllers;

use Yii;
use common\helpers\ResultHelper;
use common\enums\StatusEnum;
use common\models\base\SearchModel;
use addons\TinyShop\merchant\controllers\BaseController;
use addons\TinyShop\common\models\common\SearchHistory;

/**
 * Class SearchController
 * @package addons\TinyShop\merchant\modules\statistics\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class SearchController extends BaseController
{
    public function actionIndex()
    {
        return $this->render($this->action->id, [

        ]);
    }

    public function actionRecord()
    {
        $searchModel = new SearchModel([
            'model' => SearchHistory::class,
            'scenario' => 'default',
            'partialMatchAttributes' => ['keyword', 'search_date'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC,
            ],
            'pageSize' => $this->pageSize,
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams);
        $dataProvider->query
            ->andWhere(['>=', 'status', StatusEnum::DISABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()]);

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * @param $type
     * @return array|mixed
     */
    public function actionData($type)
    {
        $data = Yii::$app->tinyShopService->searchHistory->getBetweenCountStat($type);

        return ResultHelper::json(200, '获取成功', $data);
    }
}