<?php

namespace addons\TinyShop\merchant\modules\product\controllers;

use addons\TinyShop\common\enums\DecimalReservationEnum;
use Yii;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;
use common\enums\StatusEnum;
use common\models\base\SearchModel;
use common\helpers\ResultHelper;
use addons\TinyShop\common\models\product\Product;
use addons\TinyShop\merchant\forms\ProductForm;
use addons\TinyShop\merchant\controllers\BaseController;
use addons\TinyShop\common\models\product\VirtualType;
use addons\TinyShop\common\enums\VirtualProductGroupEnum;
use addons\TinyShop\common\models\SettingForm;

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
            ->andWhere(['product_status' => $product_status])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->with(['cate', 'virtualType']);

        // 库存报警
        $stock_warning && $dataProvider->query->andWhere("warning_stock > stock");

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'cates' => Yii::$app->tinyShopService->productCate->getMapList(),
            'product_status' => $product_status,
            'stock_warning' => $stock_warning,
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
        $virtual_group = Yii::$app->request->get('virtual_group');

        $model = $this->findFormModel($id);
        $model->tags = !empty($model->tags) ? explode(',', $model->tags) : [];
        $model->covers = unserialize($model->covers);
        $model->defaultMemberDiscount = Yii::$app->tinyShopService->productMemberDiscount->getLevelListByProductId($id);
        $model->member_level_decimal_reservation = $model->defaultMemberDiscount[0]['decimal_reservation_number'] ?? DecimalReservationEnum::DEFAULT;

        /** @var VirtualType $virtualType 虚拟商品 */
        $virtualType = $model->virtualType;
        if (!$virtualType && $virtual_group) {
            $virtualType = new VirtualType();
            $virtualType = $virtualType->loadDefaultValues();
            $virtualType->group = $virtual_group;
            $virtualType->value = [];
        }

        // 分销配置
        $commissionRate = Yii::$app->tinyShopService->productCommissionRate->findModelByProductId($id);

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
                $model->memberDiscount = $data[$model->formName()]['memberDiscount'] ?? [];
                $model->ladderPreferentialData = $data[$model->formName()]['ladderPreferentialData'] ?? [];
                // 分销载入
                $commissionRate->load($data);

                !empty($model->covers) && $model->covers = serialize($model->covers);
                !empty($model->tags) && $model->tags = implode(',', $model->tags);

                // 分销开启状态
                $model->is_open_commission = $commissionRate->status;
                if (!$model->save()) {
                    throw new NotFoundHttpException($this->getError($model));
                }

                // 分销
                $commissionRate->product_id = $model->id;
                if (!$commissionRate->save()) {
                    throw new NotFoundHttpException($this->getError($commissionRate));
                }

                // 虚拟商品
                if ($virtualType) {
                    $virtualType->load($data);
                    $virtualType->product_id = $model->id;
                    $virtual = VirtualProductGroupEnum::getModel($virtualType->group, $data);
                    if (!$virtual->validate()) {
                        throw new NotFoundHttpException($this->getError($virtual));
                    }

                    $virtual = ArrayHelper::toArray($virtual);
                    unset($virtual['period'], $virtual['confine_use_number']);
                    $virtualType->value = $virtual;

                    if (!$virtualType->save()) {
                        throw new NotFoundHttpException($this->getError($virtualType));
                    }
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
        // 阶梯折扣
        $model->ladderPreferentialData = $model->ladderPreferential;
        empty($model->ladderPreferentialData) && $model->ladderPreferentialData = [[]];
        $model->ladderPreferentialData = array_reverse($model->ladderPreferentialData);

        // 配置
        $setting = new SettingForm();
        $setting->attributes = $this->getConfig();

        return $this->render($this->action->id, [
            'model' => $model,
            'cates' => Yii::$app->tinyShopService->productCate->getMapList(),
            'brands' => Yii::$app->tinyShopService->productBrand->getMapList(),
            'tags' => Yii::$app->tinyShopService->productTag->getMapByList($model->tags),
            'supplier' => Yii::$app->tinyShopService->baseSupplier->getMapList(),
            'companys' => Yii::$app->tinyShopService->expressCompany->getMapList(), // 快递物流
            'skus' => Yii::$app->tinyShopService->productSku->findByProductId($id),
            'baseAttribute' => Yii::$app->tinyShopService->baseAttribute->getMapList(), // 基础类型
            'attributeValue' => $attributeValue,
            'specValue' => $specValue,
            'specValuejsData' => $specValuejsData,
            'productStatusExplain' => Product::$productStatusExplain,
            'commissionRate' => $commissionRate,
            'virtualType' => $virtualType,
            'virtual_group' => $virtual_group,
            'setting' => $setting,
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
            return ResultHelper::json(422, '请选择数据进行操作');
        }

        $product_status = $state == StatusEnum::ENABLED ? Product::PRODUCT_STATUS_PUTAWAY : Product::PRODUCT_STATUS_SOLD_OUT;

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
            return $this->message("删除成功", $this->redirect(['index']));
        }

        return $this->message("删除失败", $this->redirect(['index']), 'error');
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
            return $this->message("找不到数据", $this->redirect(['recycle']), 'error');
        }

        $model->status = StatusEnum::DELETE;
        if ($model->save()) {
            return $this->message("删除成功", $this->redirect(['recycle']));
        }

        return $this->message("删除失败", $this->redirect(['recycle']), 'error');
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
            return ResultHelper::json(422, '请选择数据进行操作');
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
            return $this->message("还原成功", $this->redirect(['recycle']));
        }

        return $this->message("还原失败", $this->redirect(['recycle']), 'error');
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
            return ResultHelper::json(422, '请选择数据进行操作');
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