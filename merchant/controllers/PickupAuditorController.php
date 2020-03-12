<?php

namespace addons\TinyShop\merchant\controllers;

use addons\TinyShop\common\models\product\Product;
use common\helpers\ResultHelper;
use Yii;
use addons\TinyShop\common\models\pickup\Auditor;
use common\enums\StatusEnum;
use common\models\base\SearchModel;
use common\traits\MerchantCurd;

/**
 * 门店自提人员管理
 *
 * Class PickupAuditorController
 * @package addons\TinyShop\merchant\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class PickupAuditorController extends BaseController
{
    use MerchantCurd;

    /**
     * @var Auditor
     */
    public $modelClass = Auditor::class;

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
            'partialMatchAttributes' => [], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC,
            ],
            'pageSize' => $this->pageSize,
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams);
        $dataProvider->query
            ->andWhere(['>=', 'status', StatusEnum::DISABLED])
            ->andWhere(['merchant_id' => $this->getMerchantId()])
        ->with(['member', 'pickupPoint']);

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'pickupPoints' => Yii::$app->tinyShopService->pickupPoint->getMap()
        ]);
    }

    /**
     * 批量删除 - 回收站
     *
     * @param $id
     * @return mixed
     */
    public function actionDeleteAll()
    {
        $ids = Yii::$app->request->post('ids', []);
        if (empty($ids)) {
            return ResultHelper::json(422, '请选择数据进行操作');
        }

        Product::deleteAll(['and', ['in', 'id', $ids], ['merchant_id' => $this->getMerchantId()]]);

        return ResultHelper::json(200, '批量操作成功');
    }
}