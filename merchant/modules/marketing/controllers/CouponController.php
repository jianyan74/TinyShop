<?php

namespace addons\TinyShop\merchant\modules\marketing\controllers;

use Yii;
use common\models\base\SearchModel;
use common\enums\UseStateEnum;
use addons\TinyShop\common\models\marketing\Coupon;
use addons\TinyShop\merchant\controllers\BaseController;

/**
 * Class MarketingCouponController
 * @package addons\TinyShop\merchant\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class CouponController extends BaseController
{
    /**
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionIndex()
    {
        $coupon_type_id = Yii::$app->request->get('coupon_type_id');
        $searchModel = new SearchModel([
            'model' => Coupon::class,
            'scenario' => 'default',
            'relations' => [],
            'partialMatchAttributes' => ['code'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC,
            ],
            'pageSize' => $this->pageSize,
        ]);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query
            ->andWhere(['merchant_id' => Yii::$app->services->merchant->getNotNullId()])
            ->andFilterWhere(['coupon_type_id' => $coupon_type_id])
            ->with(['couponType', 'member']);

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * @param $id
     * @return mixed|string
     */
    public function actionRevocation($id)
    {
        if (!($model = Coupon::findOne(['id' => $id, 'merchant_id' => $this->getMerchantId()]))) {
            return $this->message('找不到优惠券', $this->redirect(Yii::$app->request->referrer), 'error');
        }

        if ($model->state != UseStateEnum::GET) {
            return $this->message('优惠券已不是领取状态无法撤回', $this->redirect(Yii::$app->request->referrer), 'error');
        }

        Yii::$app->tinyShopService->marketingCoupon->revocation($model->id, $model->member_id);

        return $this->message('撤回成功', $this->redirect(Yii::$app->request->referrer));
    }
}
