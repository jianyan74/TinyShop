<?php

namespace addons\TinyShop\merchant\modules\marketing\controllers;

use Yii;
use common\traits\MerchantCurd;
use common\enums\StatusEnum;
use common\models\base\SearchModel;
use addons\TinyShop\merchant\forms\CouponTypeForm;
use addons\TinyShop\merchant\controllers\BaseController;
use addons\TinyShop\merchant\forms\CouponTypeGiveForm;
use addons\TinyShop\common\models\marketing\CouponType;
use yii\web\NotFoundHttpException;

/**
 * Class MarketingCouponTypeController
 * @package addons\TinyShop\merchant\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class CouponTypeController extends BaseController
{
    use MerchantCurd;

    /**
     * @var CouponTypeForm
     */
    public $modelClass = CouponTypeForm::class;

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
                'id' => SORT_DESC,
            ],
            'pageSize' => $this->pageSize,
        ]);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query
            ->andWhere(['>=', 'status', StatusEnum::DISABLED])
            ->andWhere(['merchant_id' => $this->getMerchantId()]);

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionSelect()
    {
        $this->layout = '@backend/views/layouts/default';

        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => ['name'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC,
            ],
            'pageSize' => $this->pageSize,
        ]);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query
            ->andWhere(['status' => StatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()]);

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * 赠送优惠券
     *
     * @return mixed|string
     * @throws NotFoundHttpException
     * @throws \yii\web\UnprocessableEntityHttpException
     */
    public function actionGive()
    {
        $request = Yii::$app->request;
        $coupon_type_id = $request->get('coupon_type_id', null);
        /** @var CouponType $coupon */
        $coupon = $this->findModel($coupon_type_id);

        $model = new CouponTypeGiveForm();
        $model->title = $coupon->title;
        $model->coupon_type_id = $coupon_type_id;


        if ($model->load($request->post())) {
            if ($model->member_id > 0) {
                try {
                    for ($i = 0;$i < $model->num; $i++) {
                        Yii::$app->tinyShopService->marketingCoupon->give($coupon, $model->member_id);
                    }

                    return $this->message('赠送成功', $this->redirect(Yii::$app->request->referrer));
                } catch (\Exception $e) {
                    return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
                }
            }

            return $this->message('找不到可赠送的用户信息', $this->redirect(Yii::$app->request->referrer), 'error');
        }

        $model->num = 1;

        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }
}