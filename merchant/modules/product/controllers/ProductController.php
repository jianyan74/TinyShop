<?php

namespace addons\TinyShop\merchant\modules\product\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use common\helpers\ArrayHelper;
use common\enums\StatusEnum;
use common\models\base\SearchModel;
use common\helpers\ResultHelper;
use addons\TinyShop\common\models\product\Product;
use addons\TinyShop\merchant\forms\ProductForm;
use addons\TinyShop\merchant\controllers\BaseController;
use addons\TinyShop\common\models\product\Sku;
use addons\TinyShop\merchant\forms\ProductInfoForm;
use addons\TinyShop\merchant\forms\ProductSearchForm;
use addons\TinyShop\common\models\SettingForm;
use yii\web\UnprocessableEntityHttpException;

/**
 * Class ProductController
 * @package addons\TinyShop\merchant\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class ProductController extends BaseController
{
    /**
     * 首页
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionIndex()
    {
        $product_status = Yii::$app->request->get('product_status', 1);
        $stock_warning = Yii::$app->request->get('stock_warning', '');

        $search = new ProductSearchForm();
        $search->attributes = Yii::$app->request->get();

        $searchModel = new SearchModel([
            'model' => ProductForm::class,
            'scenario' => 'default',
            'partialMatchAttributes' => [], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC,
            ],
            'pageSize' => $this->pageSize,
        ]);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query
            ->andWhere(['status' => StatusEnum::ENABLED])
            ->andWhere(['product_status' => $product_status])
            ->andFilterWhere($search->marketing())
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->andFilterWhere(['is_virtual' => $search->is_virtual])
            ->andFilterWhere(['in', 'cate_id', $search->cateIds()])
            ->andFilterWhere(['supplier_id' => $search->supplier_id])
            ->andFilterWhere($search->recommend())
            ->andFilterWhere($search->betweenSales())
            ->andFilterWhere($search->integral())
            ->andFilterWhere(['like', 'name', $search->name])
            ->with(['cate']);

        // 库存报警
        $stock_warning && $dataProvider->query->andWhere("warning_stock > stock");

        // 获取正在参与的营销
        $models = $dataProvider->getModels();

        // 配置
        $setting = new SettingForm();
        $setting->attributes = $this->getConfig();

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'h5Url' => $setting->h5_url,
            'cates' => Yii::$app->tinyShopService->productCate->getMapList(),
            'product_status' => $product_status,
            'stock_warning' => $stock_warning,
            'search' => $search,
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
        $model->tags = !empty($model->tags) ? explode(',', $model->tags) : [];
        $model->covers = unserialize($model->covers);

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            $data = Yii::$app->request->post();
            // 开启事务
            $transaction = Yii::$app->db->beginTransaction();
            try {
                // 载入数据并验证
                $model->skuData = $data['skus'] ?? [];
                $model->attributeValueData = $data['attributeValue'] ?? [];
                $model->specValueData = $data['specValue'] ?? [];
                $model->specValueFieldData = $data['specValueFieldData'] ?? [];
                !empty($model->covers) && $model->covers = serialize($model->covers);
                !empty($model->tags) && $model->tags = implode(',', $model->tags);
                if (!$model->save()) {
                    throw new NotFoundHttpException($this->getError($model));
                }

                $transaction->commit();

                return ResultHelper::json(200, '操作成功');
            } catch (\Exception $e) {
                $transaction->rollBack();

                return ResultHelper::json(422, $e->getMessage());
            }
        }

        // 获取参数、规格和规格值、已选规格值
        list($attributeValue, $specValue, $specValuejsData) = Yii::$app->tinyShopService->product->getSpecValueAttribute($model);

        // 配置
        $setting = new SettingForm();
        $setting->attributes = $this->getConfig();


        return $this->render($this->action->id, [
            'model' => $model,
            'cate' => Yii::$app->tinyShopService->productCate->findById($model->cate_id),
            'cates' => Yii::$app->tinyShopService->productCate->getList(),
            'brands' => Yii::$app->tinyShopService->productBrand->getMapList(),
            'tags' => Yii::$app->tinyShopService->productTag->getMapByList($model->tags),
            'supplier' => Yii::$app->tinyShopService->baseSupplier->getMapList(),
            'companys' => Yii::$app->tinyShopService->expressCompany->getMapList(), // 快递物流
            'skus' => Yii::$app->tinyShopService->productSku->findEditByProductId($id),
            'baseAttribute' => Yii::$app->tinyShopService->baseAttribute->getMapList(), // 基础类型
            'attributeValue' => $attributeValue,
            'specValue' => $specValue,
            'specValuejsData' => $specValuejsData,
            'productStatusExplain' => Product::$productStatusExplain,
            'setting' => $setting,
            'referrer' => Yii::$app->request->referrer,
        ]);
    }

    /**
     * 批量上下架
     *
     * @param $id
     * @return mixed
     */
    public function actionStateAll($state)
    {
        $ids = Yii::$app->request->post('ids', []);
        if (empty($ids)) {
            return ResultHelper::json(422, '请至少选择一个商品');
        }

        $product_status = $state == StatusEnum::ENABLED ? Product::PRODUCT_STATUS_PUTAWAY : Product::PRODUCT_STATUS_SOLD_OUT;
        // 下架
        if ($product_status == Product::PRODUCT_STATUS_SOLD_OUT) {
            Yii::$app->tinyShopService->memberCartItem->loseByProductIds($ids);
        }

        Product::updateAll(['product_status' => $product_status], ['and', ['in', 'id', $ids], ['merchant_id' => $this->getMerchantId()]]);

        return ResultHelper::json(200, '批量操作成功');
    }

    /**
     * 删除 - 回收站
     *
     * @param $id
     * @return mixed
     * @throws \Throwable
     * @throws yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->status = StatusEnum::DISABLED;
        if ($model->save()) {
            return $this->message("删除成功", $this->redirect(Yii::$app->request->referrer));
        }

        return $this->message("删除失败", $this->redirect(Yii::$app->request->referrer), 'error');
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
            return ResultHelper::json(422, '请至少选择一个商品');
        }

        Product::updateAll(['status' => StatusEnum::DISABLED],
            ['and', ['in', 'id', $ids], ['merchant_id' => $this->getMerchantId()]]);

        return ResultHelper::json(200, '批量操作成功');
    }

    /**
     * 伪删除 - 隐藏
     *
     * @param $id
     * @return mixed
     */
    public function actionDestroy($id)
    {
        if (!($model = $this->findModel($id))) {
            return $this->message("找不到数据", $this->redirect(Yii::$app->request->referrer), 'error');
        }

        $model->status = StatusEnum::DELETE;
        if ($model->save()) {
            return $this->message("删除成功", $this->redirect(Yii::$app->request->referrer));
        }

        return $this->message("删除失败", $this->redirect(Yii::$app->request->referrer), 'error');
    }

    /**
     * 批量伪删除 - 隐藏
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

        Product::updateAll(['status' => StatusEnum::DELETE],
            ['and', ['in', 'id', $ids], ['merchant_id' => $this->getMerchantId()]]);

        return ResultHelper::json(200, '批量操作成功');
    }

    /**
     * 获取规格属性
     *
     * @param $model_id
     * @return array
     */
    public function actionBaseSpecAttribute($base_attribute_id)
    {
        // 属性值
        $data = Yii::$app->tinyShopService->baseAttribute->getDataById($base_attribute_id);
        $value = $data['value'] ?? [];
        foreach ($value as &$item) {
            $item['config'] = !empty($item['value']) ? explode(',', $item['value']) : [];
            $item['value'] = '';
        }

        // 规格
        $spec_ids = explode(',', $data['spec_ids']);
        $spec_ids = $spec_ids ?? [];
        $spec = Yii::$app->tinyShopService->baseSpec->getListWithValueByIds($spec_ids);

        return ResultHelper::json(200, '获取成功', [
            'attributeValue' => $value,
            'spec' => $spec ?? [],
        ]);
    }

    /**
     * 批量上下架
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

        Product::updateAll([
            'is_hot' => $is_hot,
            'is_recommend' => $is_recommend,
            'is_new' => $is_new,
        ], [
            'and',
            ['in', 'id', $ids],
            ['merchant_id' => $this->getMerchantId()]
        ]);

        return ResultHelper::json(200, '批量操作成功');
    }

    /**
     * 批量修改商品价格和库存
     *
     * @param $id
     * @return mixed
     */
    public function actionUpdateInfo()
    {
        $ids = Yii::$app->request->post('ids');
         if (empty($ids)) {
            return ResultHelper::json(422, '请至少选择一个商品');
        }

        $ids = explode(',', $ids);

        $form = new ProductInfoForm();
        $form->attributes = Yii::$app->request->post();
        if (!$form->validate()) {
            return ResultHelper::json(422, $this->getError($form));
        }

        list($fields, $where) = $form->getFields();
        if (empty($fields)) {
            return ResultHelper::json(200, '更新成功');
        }

        // 更新数据
        $transaction = Yii::$app->db->beginTransaction();
        try {
            foreach ($ids as $id) {
                // 更新产品
                if (!Product::updateAllCounters($fields, ArrayHelper::merge($where, [
                    ['id' => $id],
                    ['merchant_id' => $this->getMerchantId()]
                ]))) {
                    throw new UnprocessableEntityHttpException('修改减少商品的价格/库存过大');
                }

                // 更新sku
                if (!Sku::updateAllCounters($fields, ArrayHelper::merge($where, [
                    ['product_id' => $id],
                    ['merchant_id' => $this->getMerchantId()]
                ]))) {
                    throw new UnprocessableEntityHttpException('修改减少商品的价格/库存过大');
                }

                // 更新总库存
                Product::updateAll(['stock' => Yii::$app->tinyShopService->productSku->getStockByProductId($id)], [
                        'id' => $id,
                        'merchant_id' => $this->getMerchantId()
                    ]
                );
            }

            $transaction->commit();

            return ResultHelper::json(200, '更新成功');
        } catch (\Exception $e) {
            $transaction->rollBack();

            return ResultHelper::json(422, $e->getMessage());
        }
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
        if (!($model = $this->findModel($id))) {
            return ResultHelper::json(422, '找不到数据');
        }

        Product::updateAll(['name' => $name], ['id' => $id]);

        return ResultHelper::json(200, '操作成功');
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
        $model = $this->findModel($id);
        $model->status = StatusEnum::ENABLED;
        if ($model->save()) {
            return $this->message("还原成功", $this->redirect(Yii::$app->request->referrer));
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

        Product::updateAll(['status' => StatusEnum::ENABLED],
            ['and', ['in', 'id', $ids], ['merchant_id' => $this->getMerchantId()]]);

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
            ->andWhere(['status' => StatusEnum::DISABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->with(['cate']);

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'cates' => Yii::$app->tinyShopService->productCate->getMapList(),
        ]);
    }

    /**
     * 更新排序/状态字段
     *
     * @param $id
     * @return array
     */
    public function actionAjaxUpdate($id)
    {
        if (!($model = Product::findOne($id))) {
            return ResultHelper::json(404, '找不到数据');
        }

        $model->attributes = ArrayHelper::filter(Yii::$app->request->get(), ['sort']);

        if (!$model->save()) {
            return ResultHelper::json(422, $this->getError($model));
        }

        return ResultHelper::json(200, '修改成功');
    }

    /**
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionSelect()
    {
        $this->layout = '@backend/views/layouts/default';
        $multiple = Yii::$app->request->get('multiple');
        $is_virtual = Yii::$app->request->get('is_virtual');
        $is_virtual == StatusEnum::ENABLED && $is_virtual = '';

        $searchModel = new SearchModel([
            'model' => ProductForm::class,
            'scenario' => 'default',
            'partialMatchAttributes' => ['name'], // 模糊查询
            'defaultOrder' => [
                'sort' => SORT_ASC,
                'id' => SORT_DESC,
            ],
            'pageSize' => $this->pageSize,
        ]);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query
            ->andWhere(['status' => StatusEnum::ENABLED])
            ->andWhere(['product_status' => StatusEnum::ENABLED])
            ->andFilterWhere(['is_virtual' => $is_virtual])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->with(['cate']);

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
            'cates' => Yii::$app->tinyShopService->productCate->getMapList(),
        ]);
    }

    /**
     * 选择颜色
     *
     * @param $value
     * @return string
     */
    public function actionSelectColor()
    {
        $this->layout = '@backend/views/layouts/default';

        $value = Yii::$app->request->get('value');
        !$value && $value = '000000';
        $value = '#' . $value;

        return $this->render($this->action->id, [
            'value' => $value,
        ]);
    }

    /**
     * 创建商品页面
     *
     * @return string
     */
    public function actionCreate()
    {
        return $this->renderAjax($this->action->id, []);
    }

    /**
     * 返回模型
     *
     * @param $id
     * @return ProductForm|array|\yii\db\ActiveRecord|null
     */
    protected function findModel($id)
    {
        if (empty($id) || empty(($model = Product::find()->where(['id' => $id])->andWhere(['merchant_id' => $this->getMerchantId()])->one()))) {
            $model = new ProductForm();
            $model->merchant_id = $this->getMerchantId();
            $model->loadDefaultValues();
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