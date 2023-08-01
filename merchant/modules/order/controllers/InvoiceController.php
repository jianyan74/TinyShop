<?php

namespace addons\TinyShop\merchant\modules\order\controllers;

use common\enums\AuditStatusEnum;
use Yii;
use addons\TinyShop\common\models\order\Invoice;
use common\traits\MerchantCurd;
use common\enums\StatusEnum;
use common\models\base\SearchModel;
use addons\TinyShop\merchant\controllers\BaseController;

/**
 * Class InvoiceController
 * @package addons\TinyShop\merchant\modules\order\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class InvoiceController extends BaseController
{
    use MerchantCurd;

    /**
     * @var string
     */
    public $modelClass = Invoice::class;

    /**
     * 首页
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionIndex()
    {
        $auditStatus = Yii::$app->request->get('audit_status', AuditStatusEnum::DISABLED);

        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => ['order_sn'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC,
            ],
            'pageSize' => $this->pageSize,
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams);
        $dataProvider->query
            ->andWhere(['>=', 'status', StatusEnum::DISABLED])
            ->andWhere(['audit_status' => $auditStatus])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->with(['order']);

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'auditStatus' => $auditStatus,
        ]);
    }
}
