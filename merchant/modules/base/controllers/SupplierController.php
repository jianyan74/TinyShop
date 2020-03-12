<?php

namespace addons\TinyShop\merchant\modules\base\controllers;

use Yii;
use common\models\base\SearchModel;
use common\traits\MerchantCurd;
use common\enums\StatusEnum;
use addons\TinyShop\common\models\base\Supplier;
use addons\TinyShop\backend\controllers\BaseController;

/**
 * 供应商
 *
 * Class SupplierController
 * @package addons\TinyShop\merchant\modules\base\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class SupplierController extends BaseController
{
    use MerchantCurd;

    /**
     * @var Supplier
     */
    public $modelClass = Supplier::class;

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
            'partialMatchAttributes' => ['name', 'linkman_tel'], // 模糊查询
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
            ->andWhere(['merchant_id' => $this->getMerchantId()]);

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }
}