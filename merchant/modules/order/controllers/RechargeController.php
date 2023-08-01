<?php

namespace addons\TinyShop\merchant\modules\order\controllers;

use Yii;
use common\enums\StatusEnum;
use common\models\base\SearchModel;
use common\traits\MerchantCurd;
use addons\TinyShop\merchant\controllers\BaseController;
use addons\TinyShop\common\models\order\Recharge;

/**
 * Class RechargeController
 * @package addons\TinyShop\merchant\modules\order\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class RechargeController extends BaseController
{
    use MerchantCurd;

    /**
     * @var Recharge
     */
    public $modelClass = Recharge::class;

    /**
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionIndex()
    {
        $payStatus = Yii::$app->request->get('pay_status', StatusEnum::ENABLED);

        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'relations' => [],
            'partialMatchAttributes' => ['order_sn', 'out_trade_no'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC,
            ],
            'pageSize' => $this->pageSize,
        ]);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query
            ->andWhere(['>=', 'status', StatusEnum::DISABLED])
            ->andWhere(['pay_status' => $payStatus])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->with(['member']);

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'payStatus' => $payStatus,
        ]);
    }
}
