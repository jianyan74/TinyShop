<?php

namespace addons\TinyShop\merchant\modules\common\controllers;

use Yii;
use common\traits\MerchantCurd;
use common\enums\StatusEnum;
use common\models\base\SearchModel;
use addons\TinyShop\common\models\common\Opinion;
use addons\TinyShop\merchant\controllers\BaseController;

/**
 * Class OpinionController
 * @package addons\TinyShop\merchant\modules\common\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class OpinionController extends BaseController
{
    use MerchantCurd;

    /**
     * @var Opinion
     */
    public $modelClass = Opinion::class;

    /**
     * 首页
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionIndex()
    {
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => ['title'], // 模糊查询
            'defaultOrder' => [
                'sort' => SORT_ASC,
                'id' => SORT_DESC,
            ],
            'pageSize' => $this->pageSize,
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams);
        $dataProvider->query
            ->andWhere(['status' => StatusEnum::ENABLED])
            ->andWhere(['merchant_id' => $this->getMerchantId()])
            ->with('member');

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }
}
