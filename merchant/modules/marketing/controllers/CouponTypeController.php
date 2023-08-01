<?php

namespace addons\TinyShop\merchant\modules\marketing\controllers;

use Yii;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;
use common\traits\MerchantCurd;
use common\enums\StatusEnum;
use common\helpers\ResultHelper;
use common\models\base\SearchModel;
use addons\TinyShop\merchant\controllers\BaseController;
use addons\TinyShop\common\enums\MarketingEnum;
use addons\TinyShop\common\models\marketing\CouponType;
use addons\TinyShop\common\enums\CouponGetTypeEnum;
use addons\TinyShop\common\enums\RangeTypeEnum;
use addons\TinyShop\merchant\modules\marketing\forms\CouponTypeForm;
use addons\TinyShop\merchant\modules\marketing\forms\CouponTypeGiveForm;

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
            'model' => CouponType::class,
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
        if ($model->load(Yii::$app->request->post())) {
            // 事务
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $model->products = Json::decode($model->products);
                if (!$model->save()) {
                    throw new NotFoundHttpException($this->getError($model));
                }

                $transaction->commit();

                return ResultHelper::json(200, '保存成功');
            } catch (\Exception $e) {
                $transaction->rollBack();
                return ResultHelper::json(422, $e->getMessage());
            }
        }

        $model->products = Yii::$app->tinyShopService->marketingProduct->regroup($id, $model->range_type == RangeTypeEnum::ASSIGN_PRODUCT ? MarketingEnum::COUPON_IN : MarketingEnum::COUPON_NOT_IN);
        $model->cateIds = Yii::$app->tinyShopService->marketingCate->getCateIdsByMarketing($id, $model->range_type == RangeTypeEnum::ASSIGN_CATE ? MarketingEnum::COUPON_IN : MarketingEnum::COUPON_NOT_IN);

        return $this->render($this->action->id, [
            'model' => $model,
            'cates' => Yii::$app->tinyShopService->productCate->getList(),
            'referrer' => Yii::$app->request->referrer,
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
                        Yii::$app->tinyShopService->marketingCoupon->giveByNewRecord($coupon, $model->member_id, 0, CouponGetTypeEnum::MANAGER);
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

    /**
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionSelect()
    {
        $this->layout = '@backend/views/layouts/blank';
        $multiple = Yii::$app->request->get('multiple');

        $searchModel = new SearchModel([
            'model' => CouponType::class,
            'scenario' => 'default',
            'partialMatchAttributes' => ['title'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC,
            ],
            'pageSize' => $this->pageSize,
        ]);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query
            ->andWhere(['status' => StatusEnum::ENABLED])
            ->andWhere(['merchant_id' => $this->getMerchantId()]);

        /** @var  $gridSelectType */
        $gridSelectType = [
            'class' => 'yii\grid\CheckboxColumn',
            'property' => 'checkboxOptions',
        ];

        if ($multiple == false) {
            $gridSelectType = [
                'class' => 'yii\grid\RadioButtonColumn',
                'property' => 'radioOptions',
            ];
        }

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'gridSelectType' => $gridSelectType,
        ]);
    }
}
