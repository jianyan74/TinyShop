<?php

namespace addons\TinyShop\merchant\modules\marketing\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use common\models\base\SearchModel;
use common\traits\MerchantCurd;
use common\enums\StatusEnum;
use addons\TinyShop\common\enums\MarketingEnum;
use addons\TinyShop\common\models\marketing\RechargeConfig;
use addons\TinyShop\merchant\controllers\BaseController;
use addons\TinyShop\merchant\modules\marketing\forms\MarketingCouponTypeForm;

/**
 * Class RechargeConfig
 * @package addons\TinyShop\merchant\modules\marketing\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class RechargeConfigController extends BaseController
{
    use MerchantCurd;

    /**
     * @var RechargeConfig
     */
    public $modelClass = RechargeConfig::class;

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
                'price' => SORT_ASC
            ],
            'pageSize' => $this->pageSize
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams);
        $dataProvider->query
            ->andWhere(['>=', 'status', StatusEnum::DISABLED])
            ->andWhere(['merchant_id' => Yii::$app->services->merchant->getNotNullId()]);

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * 编辑/创建
     *
     * @return mixed
     */
    public function actionEdit()
    {
        $id = Yii::$app->request->get('id', null);
        $model = $this->findModel($id);
        $marketingCouponType = new MarketingCouponTypeForm();
        if ($model->load(Yii::$app->request->post())) {
            // 事务
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if (!$model->save()) {
                    throw new NotFoundHttpException($this->getError($model));
                }

                // 优惠券
                $marketingCouponType->load(Yii::$app->request->post());
                $marketingCouponType->marketing_id = $model->id;
                $marketingCouponType->marketing_type = MarketingEnum::MEMBER_RECHARGE_CONFIG;
                if (!$marketingCouponType->validate()) {
                    throw new NotFoundHttpException($this->getError($marketingCouponType));
                }
                $marketingCouponType->create();

                $transaction->commit();

                return $this->referrer();
            } catch (\Exception $e) {
                $transaction->rollBack();
                return $this->referrer();
            }
        }

        $marketingCouponType->couponTypes = Yii::$app->tinyShopService->marketingCouponTypeMap->regroup($model->id, MarketingEnum::MEMBER_RECHARGE_CONFIG);

        return $this->render($this->action->id, [
            'model' => $model,
            'marketingCouponType' => $marketingCouponType,
        ]);
    }
}
