<?php

namespace addons\TinyShop\merchant\modules\base\controllers;

use Yii;
use yii\data\Pagination;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use common\helpers\ResultHelper;
use common\traits\MerchantCurd;
use addons\TinyShop\common\models\express\Fee;
use addons\TinyShop\merchant\controllers\BaseController;

/**
 * 运费模板
 *
 * Class ExpressFeeController
 * @package addons\TinyShop\merchant\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class ExpressFeeController extends BaseController
{
    use MerchantCurd;

    /**
     * @var Fee
     */
    public $modelClass = Fee::class;

    /**
     * 物流快递id
     *
     * @var integer
     */
    public $company_id;

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();

        $this->company_id = Yii::$app->request->get('company_id');
    }

    /**
     * 首页
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $data = $this->modelClass::find()
            ->where(['>=', 'status', StatusEnum::DISABLED])
            ->andWhere(['company_id' => $this->company_id])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()]);
        $pages = new Pagination(['totalCount' => $data->count(), 'pageSize' => $this->pageSize]);
        $models = $data->offset($pages->offset)
            ->orderBy('is_default desc, id desc')
            ->limit($pages->limit)
            ->asArray()
            ->all();

        foreach ($models as &$model) {
            $ids = ArrayHelper::merge(explode(',', $model['province_ids']), explode(',', $model['city_ids']));
            $model['region'] = ArrayHelper::itemsMerge(Yii::$app->services->provinces->findByIds($ids), 0, 'id', 'pid', 'child');
        }

        return $this->render($this->action->id, [
            'models' => $models,
            'pages' => $pages,
            'company_id' => $this->company_id,
        ]);
    }

    /**
     * 编辑/创建
     *
     * @return mixed
     */
    public function actionEdit()
    {
        $request = Yii::$app->request;
        $id = $request->get('id', null);
        $model = $this->findModel($id);
        if ($model->load($request->post()) && $model->save()) {
            return $this->redirect(['index', 'company_id' => $this->company_id]);
        }

        // 设定禁用默认地区
        if (($default = Yii::$app->tinyShopService->expressFee->findDefaultFee($this->company_id)) && $default->id != $model->id) {
            $model->is_default = 0;
        }

        // 不可选省市区数据
        list ($allProvinceIds, $allCityIds, $allAreaIds) = Yii::$app->tinyShopService->expressFee->getNotChoose($this->company_id);

        return $this->render($this->action->id, [
            'model' => $model,
            'company_id' => $this->company_id,
            'allProvinceIds' => $allProvinceIds,
            'allCityIds' => $allCityIds,
            'allAreaIds' => $allAreaIds,
        ]);
    }

    /**
     * 伪删除
     *
     * @param $id
     * @return mixed
     */
    public function actionDestroy($id)
    {
        if (!($model = $this->modelClass::findOne($id))) {
            return $this->message("找不到数据", $this->redirect(['index', 'company_id' => $this->company_id]), 'error');
        }

        $model->status = StatusEnum::DELETE;
        if ($model->save()) {
            return $this->message("删除成功", $this->redirect(['index', 'company_id' => $this->company_id]));
        }

        return $this->message("删除失败", $this->redirect(['index', 'company_id' => $this->company_id]), 'error');
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
            return ResultHelper::json(422, '请选择需要操作的记录');
        }

        $this->modelClass::updateAll(['status' => StatusEnum::DELETE],
            ['and', ['in', 'id', $ids], ['merchant_id' => $this->getMerchantId()]]);

        return ResultHelper::json(200, '批量删除成功');
    }

    /**
     * 返回模型
     *
     * @param $id
     * @return \yii\db\ActiveRecord
     */
    protected function findModel($id)
    {
        /* @var $model \yii\db\ActiveRecord */
        if (empty($id) || empty(($model = $this->modelClass::find()->where(['id' => $id])->andWhere(['merchant_id' => $this->getMerchantId()])->one()))) {
            $model = new $this->modelClass;
            $model->company_id = $this->company_id;

            return $model->loadDefaultValues();
        }

        return $model;
    }
}