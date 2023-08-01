<?php

namespace addons\TinyShop\merchant\modules\product\controllers;

use Yii;
use yii\db\ActiveQuery;
use common\enums\StatusEnum;
use common\enums\AppEnum;
use common\helpers\ArrayHelper;
use common\helpers\ResultHelper;
use common\models\base\SearchModel;
use addons\TinyShop\common\models\product\Product;
use addons\TinyShop\common\models\product\Sku;
use addons\TinyShop\merchant\controllers\BaseController;
use addons\TinyShop\merchant\modules\product\forms\ProductSearchForm;
use addons\TinyShop\merchant\modules\product\forms\ProductForm;
use addons\TinyShop\merchant\modules\product\forms\MemberDiscountForm;
use addons\TinyShop\merchant\modules\product\forms\StockForm;
use addons\TinyShop\common\enums\MarketingEnum;
use addons\TinyShop\common\traits\ProductSelectTrait;
use addons\TinyShop\common\enums\ProductTypeEnum;
use addons\TinyShop\common\enums\ShippingTypeEnum;
use addons\TinyShop\common\enums\product\AuditStatusEnum;

/**
 * Class ProductController
 * @package addons\TinyShop\merchant\modules\product\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class ProductController extends BaseController
{
    use ProductSelectTrait;

    /**
     * 首页
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionIndex()
    {
        $merchant_id = Yii::$app->services->merchant->getNotNullId();
        $status = Yii::$app->request->get('status', StatusEnum::ENABLED);
        $auditStatus = Yii::$app->request->get('audit_status', AuditStatusEnum::ENABLED);
        $stockWarning = Yii::$app->request->get('stock_warning', '');
        $sellOut = Yii::$app->request->get('sell_out', '');

        $search = new ProductSearchForm();
        $search->attributes = Yii::$app->request->get();

        $searchModel = new SearchModel([
            'model' => Product::class,
            'scenario' => 'default',
            'partialMatchAttributes' => [], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC,
            ],
            'pageSize' => $this->pageSize,
        ]);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query
            ->alias('p')
            ->andWhere(['status' => $status])
            ->andFilterWhere(['audit_status' => $status == StatusEnum::DISABLED ? '' : $auditStatus])
            ->andFilterWhere($search->marketing())
            ->andFilterWhere(['p.merchant_id' => $this->getMerchantId()])
            ->andFilterWhere(['type' => $search->type])
            ->andFilterWhere(['in', 'cate_id', $search->cateIds()])
            ->andFilterWhere(['supplier_id' => $search->supplier_id])
            ->andFilterWhere(['stock_deduction_type' => $search->stock_deduction_type])
            ->andFilterWhere(['brand_id' => $search->brand_id])
            ->andFilterWhere(['p.merchant_id' => $search->merchant_id])
            ->andFilterWhere($search->recommend())
            ->andFilterWhere($search->betweenSales())
            ->andFilterWhere(['like', 'name', $search->name])
            ->with(['cate', 'merchant']);

        if (!empty($search->merchantCateIds())) {
            $dataProvider->query->joinWith(['cateMap as c' => function (ActiveQuery $query) use ($search) {
                return $query->andWhere(['in', 'c.cate_id', $search->merchantCateIds()]);
            }]);
        }

        // 库存报警 or 售罄
        $stockWarning && $dataProvider->query->andWhere("stock_warning_num > stock");
        $sellOut && $dataProvider->query->andWhere(['stock' => 0]);

        // 获取正在参与的营销
        $models = $dataProvider->getModels();
        $marketing = Yii::$app->tinyShopService->marketingProduct->getMarketingType(ArrayHelper::getColumn($models, 'id'));

        // 配置
        $setting = Yii::$app->tinyShopService->config->setting();

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'h5Url' => $setting->app_h5_url,
            'marketing' => $marketing,
            'status' => $status,
            'auditStatus' => $auditStatus,
            'stockWarning' => $stockWarning,
            'sellOut' => $sellOut,
            'search' => $search,
            'cates' => Yii::$app->tinyShopService->productCate->getMapList(0),
            'merchantCates' => Yii::$app->tinyShopService->productCate->getMapList($merchant_id),
            'auditStatusCount' => Yii::$app->tinyShopService->product->getAuditStatusCount(), // 审核状态
            'warningStockCount' => Yii::$app->tinyShopService->product->getWarningStockCount(),
            'brands' => Yii::$app->tinyShopService->productBrand->getMapList(),
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
        $model = $this->findFormModel($id);
        $model->isNewRecord && $model->type = Yii::$app->request->get('type', ProductTypeEnum::ENTITY);
        $setting = Yii::$app->tinyShopService->config->setting();

        if ($model->load(Yii::$app->request->post())) {
            // 开启事务
            $transaction = Yii::$app->db->beginTransaction();
            try {
                // 商品编辑审核
                $setting->product_audit_status == StatusEnum::ENABLED && $model->audit_status = StatusEnum::DISABLED;
                empty($model->delivery_type) && $model->delivery_type = [];
                !$model->save() && $this->error($model);


                $transaction->commit();
                return ResultHelper::json(200, '操作成功');
            } catch (\Exception $e) {
                $transaction->rollBack();

                return ResultHelper::json(422, $e->getMessage(), Yii::$app->services->base->getErrorInfo($e));
            }
        }

        list($spec, $pitchOn) = Yii::$app->tinyShopService->productSpec->getJsData($model->id);
        // 默认选择配送方式
        if (in_array($model->type, ProductTypeEnum::entity())) {
            $model->setScenario('entity');
            $model->isNewRecord && $model->delivery_type = [ShippingTypeEnum::LOGISTICS];
        }

        return $this->render($this->action->id, [
            'model' => $model,
            'cates' => Yii::$app->tinyShopService->productCate->getList(),
            'platformCates' => Yii::$app->tinyShopService->productCate->getList(0),
            'brands' => Yii::$app->tinyShopService->productBrand->getMapList(),
            'tags' => Yii::$app->tinyShopService->productTag->getMapByList($model->tags),
            'supplier' => Yii::$app->tinyShopService->supplier->getMap(),
            'sku' => Yii::$app->tinyShopService->productSku->getJsData($model->id, $pitchOn),
            'spec' => $spec,
            'pitchOn' => $pitchOn, // 选中的数据
            'attribute' => Yii::$app->tinyShopService->attribute->getMap(),
            'attributeValue' => Yii::$app->tinyShopService->productAttributeValue->findByProductId($model->id),
            'specTemplate' => Yii::$app->tinyShopService->specTemplate->getMap(),
            'company' => Yii::$app->tinyShopService->expressCompany->getMapList(), // 快递物流
            'referrer' => Yii::$app->request->referrer,
        ]);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function actionRefuse($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            if ($model->audit_status == AuditStatusEnum::DISABLED) {
                Product::updateAll(['audit_status' => AuditStatusEnum::DELETE, 'refusal_cause' => $model->refusal_cause], ['id' => $id]);
                return $this->message('拒绝成功', $this->redirect(Yii::$app->request->referrer));
            }

            return $this->message('拒绝失败', $this->redirect(Yii::$app->request->referrer));
        }

        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function actionRefuseCause($id)
    {
        return $this->renderAjax($this->action->id, [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * @param $id
     * @return mixed|string
     */
    public function actionPass($id)
    {
        $model = $this->findModel($id);
        if ($model->audit_status != AuditStatusEnum::DISABLED) {
            return $this->message('申请已经被处理', $this->redirect(Yii::$app->request->referrer), 'error');
        }

        Product::updateAll(['audit_status' => AuditStatusEnum::ENABLED], ['id' => $id]);

        return $this->message('申请通过', $this->redirect(Yii::$app->request->referrer));
    }

    /**
     * 更新名称
     *
     * @param $id
     * @param $name
     * @return array|mixed
     */
    public function actionUpdateName($id, $name)
    {
        if (!($model = $this->findFormModel($id))) {
            return ResultHelper::json(422, '找不到数据');
        }

        Product::updateAll(['name' => $name], ['id' => $id]);

        return ResultHelper::json(200, '操作成功');
    }

    /**
     * 会员价
     *
     * @param $id
     * @return string
     */
    public function actionMemberDiscount($id)
    {
        if ($data = Yii::$app->request->post('data')) {
            try {
                $form = new MemberDiscountForm();
                $form->attributes = $data;
                if ($form->save()) {
                    return ResultHelper::json(200, 'ok');
                }
            } catch (\Exception $e) {
                return ResultHelper::json(422, $e->getMessage());
            }
        }

        $model = $this->findModel($id);
        $levels = Yii::$app->services->memberLevel->findAllByEdit();
        $memberDiscount = Yii::$app->tinyShopService->productMemberDiscount->findByProductId($id);
        $memberDiscountMap = [];
        foreach ($memberDiscount as $value) {
            foreach ($value['discount'] as $discount) {
                $key = $discount['sku_id'] . '-' . $value['member_level'];
                $memberDiscountMap[$key] = $discount['discount'];
            }
        }

        $allData = [];
        foreach ($model->sku as $sku) {
            $data = [
                'id' => $sku['id'],
                'price' => $sku['price'],
                'name' => !empty($sku['name']) ? $sku['name'] : $model['name'],
                'discount' => 10,
                'check' => true,
                'level' => [],
            ];

            foreach ($levels as $level) {
                $key = $sku['id'] . '-' . $level['level'];

                $data['level'][] = [
                    'level' => $level['level'],
                    'default' => $level['discount'], // 默认折扣
                    'discount' => isset($memberDiscountMap[$key]) ? $memberDiscountMap[$key] : $level['discount'],
                ];
            }

            $allData[] = $data;
        }

        return $this->render($this->action->id, [
            'model' => $model,
            'levels' => ArrayHelper::toArray($levels),
            'allData' => $allData,
        ]);
    }

    /**
     * 库存
     *
     * @param $id
     * @return string
     */
    public function actionStock($id)
    {
        if ($data = Yii::$app->request->post('data')) {
            try {
                $form = new StockForm();
                $form->productId = $id;
                $form->skus = $data;
                if ($form->save()) {
                    return ResultHelper::json(200, 'ok');
                }

                return ResultHelper::json(200, 'ok');
            } catch (\Exception $e) {
                return ResultHelper::json(422, $e->getMessage());
            }
        }

        return $this->render($this->action->id, [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * 修改推荐状态
     *
     * @param $id
     * @return mixed
     */
    public function actionRecommend($is_hot, $is_recommend, $is_new)
    {
        $ids = Yii::$app->request->post('ids', []);
        if (empty($ids)) {
            return ResultHelper::json(422, '请至少选择一个商品');
        }

        if (Yii::$app->id == AppEnum::BACKEND) {
            Product::updateAll([
                'is_hot' => $is_hot,
                'is_recommend' => $is_recommend,
                'is_new' => $is_new,
            ], ['in', 'id', $ids]);
        } else {
            Product::updateAll([
                'is_hot' => $is_hot,
                'is_recommend' => $is_recommend,
                'is_new' => $is_new,
            ], [
                'and',
                ['in', 'id', $ids],
                ['merchant_id' => $this->getMerchantId()]
            ]);
        }

        return ResultHelper::json(200, '批量操作成功');
    }

    /**
     * 复制
     *
     * @param $id
     * @return mixed|string
     */
    public function actionCopy($id)
    {
        if (!($oldModel = $this->findFormModel($id))) {
            return $this->message('找不到数据', $this->redirect(Yii::$app->request->referrer), 'error');
        }

        if ($oldModel->audit_status == AuditStatusEnum::GET_OUT_OF_LINE) {
            return $this->message('违规下架商品无法复制', $this->redirect(Yii::$app->request->referrer), 'error');
        }

        // 事务
        $transaction = Yii::$app->db->beginTransaction();
        try {
            Yii::$app->tinyShopService->product->copy($oldModel);

            $transaction->commit();

            return $this->message('复制商品成功，请到已下架查看', $this->redirect(Yii::$app->request->referrer));
        } catch (\Exception $e) {
            $transaction->rollBack();

            return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
        }
    }

    /**
     * 批量上下架
     *
     * @param $id
     * @return mixed
     */
    public function actionStateAll($status)
    {
        $ids = Yii::$app->request->post('ids', []);
        if (empty($ids)) {
            return ResultHelper::json(422, '请至少选择一个商品');
        }

        // 下架
        if ($status == StatusEnum::DISABLED) {
            // 购物车失效
            Yii::$app->tinyShopService->memberCartItem->loseByProductIds($ids, true);
            // 营销失效
            Yii::$app->tinyShopService->marketingProduct->loseByProductId($ids, true);
        }

        // 总后台判断
        if (Yii::$app->id == AppEnum::BACKEND) {
            Product::updateAll(['status' => $status], ['in', 'id', $ids]);
            Sku::updateAll(['status' => $status], ['in', 'product_id', $ids]);
        } else {
            Product::updateAll(['status' => $status], ['and', ['in', 'id', $ids], ['merchant_id' => $this->getMerchantId()]]);
            Sku::updateAll(['status' => $status], ['and', ['in', 'product_id', $ids], ['merchant_id' => $this->getMerchantId()]]);
        }

        return ResultHelper::json(200, '批量操作成功');
    }

    /**
     * 违规批量上下架
     *
     * @param $id
     * @return mixed
     */
    public function actionPlatformStateAll($status = AuditStatusEnum::GET_OUT_OF_LINE)
    {
        $ids = Yii::$app->request->post('ids', []);
        if (empty($ids)) {
            return ResultHelper::json(422, '请至少选择一个商品');
        }

        // 下架
        if ($status == AuditStatusEnum::GET_OUT_OF_LINE) {
            // 购物车失效
            Yii::$app->tinyShopService->memberCartItem->loseByProductIds($ids, true);
            // 营销失效
            Yii::$app->tinyShopService->marketingProduct->loseByProductId($ids, true);
        }

        // 总后台判断
        if (Yii::$app->id == AppEnum::BACKEND) {
            Product::updateAll(['audit_status' => $status], ['in', 'id', $ids]);
        } else {
            Product::updateAll(['audit_status' => $status], ['and', ['in', 'id', $ids], ['merchant_id' => $this->getMerchantId()]]);
        }

        return ResultHelper::json(200, '批量操作成功');
    }

    /**
     * 回收站
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionRecycle()
    {
        $searchModel = new SearchModel([
            'model' => Product::class,
            'scenario' => 'default',
            'partialMatchAttributes' => ['name'], // 模糊查询
            'defaultOrder' => [
                'sort' => SORT_ASC,
                'id' => SORT_DESC,
            ],
            'pageSize' => $this->pageSize,
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams);
        $dataProvider->query
            ->andWhere(['status' => StatusEnum::DELETE])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->with(['cate']);

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'cates' => Yii::$app->tinyShopService->productCate->getMapList(),
        ]);
    }

    /**
     * 还原
     *
     * @param $id
     * @return mixed
     * @throws \Throwable
     * @throws yii\db\StaleObjectException
     */
    public function actionRestore($id)
    {
        if (empty($id)) {
            return ResultHelper::json(422, '请至少选择一个商品');
        }

        $model = $this->findModel($id);
        $model->status = StatusEnum::DISABLED;
        if ($model->save()) {
            return $this->message("还原成功，请到已下架查看", $this->redirect(Yii::$app->request->referrer));
        }

        return $this->message("还原失败", $this->redirect(Yii::$app->request->referrer), 'error');
    }

    /**
     * 批量还原
     *
     * @param $id
     * @return mixed
     */
    public function actionRestoreAll()
    {
        $ids = Yii::$app->request->post('ids', []);
        if (empty($ids)) {
            return ResultHelper::json(422, '请至少选择一个商品');
        }

        if (Yii::$app->id === AppEnum::BACKEND) {
            $where = ['in', 'id', $ids];
        } else {
            $where = ['and', ['in', 'id', $ids], ['merchant_id' => $this->getMerchantId()]];
        }

        Product::updateAll(['status' => StatusEnum::DISABLED], $where);

        return ResultHelper::json(200, '还原成功，请到已下架查看');
    }

    /**
     * 删除
     *
     * @param $id
     * @return mixed
     * @throws \Throwable
     * @throws yii\db\StaleObjectException
     */
    public function actionDestroy($id)
    {
        $model = $this->findModel($id);
        $model->status = StatusEnum::DELETE;
        if ($model->save()) {
            // 购物车失效
            Yii::$app->tinyShopService->memberCartItem->loseByProductIds([$id], true);
            // 营销失效
            Yii::$app->tinyShopService->marketingProduct->loseByProductId([$id], true);

            return $this->message("删除成功", $this->redirect(Yii::$app->request->referrer));
        }

        return $this->message("删除失败", $this->redirect(Yii::$app->request->referrer), 'error');
    }

    /**
     * 批量删除
     *
     * @param $id
     * @return mixed
     */
    public function actionDestroyAll()
    {
        $ids = Yii::$app->request->post('ids', []);
        if (empty($ids)) {
            return ResultHelper::json(422, '请至少选择一个商品');
        }

        if (Yii::$app->id === AppEnum::BACKEND) {
            $where = ['in', 'id', $ids];
        } else {
            $where = ['and', ['in', 'id', $ids], ['merchant_id' => $this->getMerchantId()]];
        }

        Product::updateAll(['status' => StatusEnum::DELETE], $where);
        // 购物车失效
        Yii::$app->tinyShopService->memberCartItem->loseByProductIds($ids, true);
        // 营销失效
        Yii::$app->tinyShopService->marketingProduct->loseByProductId($ids, true);

        return ResultHelper::json(200, '批量操作成功');
    }

    /**
     * 回收站删除
     *
     * @param $id
     * @return mixed
     * @throws \Throwable
     * @throws yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->status = -2;
        if ($model->save()) {
            return $this->message("删除成功", $this->redirect(Yii::$app->request->referrer));
        }

        return $this->message("删除失败", $this->redirect(Yii::$app->request->referrer), 'error');
    }

    /**
     * 回收站批量删除
     *
     * @param $id
     * @return mixed
     */
    public function actionDeleteAll()
    {
        $ids = Yii::$app->request->post('ids', []);
        if (empty($ids)) {
            return ResultHelper::json(422, '请至少选择一个商品');
        }

        if (Yii::$app->id === AppEnum::BACKEND) {
            $where = ['in', 'id', $ids];
        } else {
            $where = ['and', ['in', 'id', $ids], ['merchant_id' => $this->getMerchantId()]];
        }

        Product::updateAll(['status' => -2], $where);

        return ResultHelper::json(200, '批量操作成功');
    }

    /**
     * 返回模型
     *
     * @param $id
     * @return ProductForm|array|\yii\db\ActiveRecord|null
     */
    protected function findModel($id)
    {
        if (empty($id) || empty(($model = Product::find()->where(['id' => $id])->andFilterWhere(['merchant_id' => $this->getMerchantId()])->one()))) {
            return $this->message("找不到商品", $this->redirect(Yii::$app->request->referrer), 'error');
        }

        return $model;
    }

    /**
     * 返回模型
     *
     * @param $id
     * @return ProductForm|array|\yii\db\ActiveRecord|null
     */
    protected function findFormModel($id)
    {
        if (empty($id) || empty(($model = ProductForm::find()->where(['id' => $id])->andFilterWhere(['merchant_id' => $this->getMerchantId()])->one()))) {
            $model = new ProductForm();
            $model->merchant_id = $this->getMerchantId();
            $model->loadDefaultValues();
        }

        return $model;
    }
}
