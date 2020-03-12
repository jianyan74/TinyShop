<?php

namespace addons\TinyShop\merchant\modules\marketing\controllers;

use Yii;
use common\models\base\SearchModel;
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
            'relations' => ['member' => ['nickname']],
            'partialMatchAttributes' => ['code', 'member.nickname'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC,
            ],
            'pageSize' => $this->pageSize,
        ]);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query
            ->andWhere(['coupon_type_id' => $coupon_type_id])
            ->with(['couponType', 'member']);

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'stateExplain' => Coupon::$stateExplain,
        ]);
    }
}