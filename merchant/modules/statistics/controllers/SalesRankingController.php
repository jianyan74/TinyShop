<?php

namespace addons\TinyShop\merchant\modules\statistics\controllers;

use Yii;
use common\enums\StatusEnum;
use common\models\base\SearchModel;
use addons\TinyShop\common\models\product\Product;
use addons\TinyShop\merchant\controllers\BaseController;

/**
 * Class SalesRankingController
 * @package addons\TinyShop\merchant\modules\statistics\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class SalesRankingController extends BaseController
{
    /**
     * 商品分析
     *
     * 近30天下单商品数
     * 近30天下单金额
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionIndex()
    {
        $searchModel = new SearchModel([
            'model' => Product::class,
            'scenario' => 'default',
            'partialMatchAttributes' => ['name'], // 模糊查询
            'defaultOrder' => [
                'sort' => SORT_ASC,
                'id' => SORT_DESC,
            ],
            'pageSize' => $this->pageSize,
        ]);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query
            ->andWhere(['status' => StatusEnum::ENABLED])
            ->andWhere(['product_status' => StatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->with(['cate'])
            ->asArray();

        $dataProvider->setModels(Yii::$app->tinyShopService->orderProduct->getCountMoneyStat($dataProvider->getModels(),
            time() - 60 * 60 * 24 * 30));

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'cates' => Yii::$app->tinyShopService->productCate->getMapList(),
        ]);
    }
}