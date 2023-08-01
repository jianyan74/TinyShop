<?php

namespace addons\TinyShop\merchant\modules\product\controllers;

use Yii;
use common\traits\MerchantCurd;
use common\helpers\ResultHelper;
use addons\TinyShop\common\models\product\Cate;
use addons\TinyShop\merchant\controllers\BaseController;

/**
 * 商品分类
 *
 * Class ProductCateController
 * @package addons\TinyShop\merchant\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class CateController extends BaseController
{
    use MerchantCurd;

    /**
     * @var Cate
     */
    public $modelClass = Cate::class;

    /**
     * Lists all Tree models.
     * @return mixed
     */
    public function actionIndex()
    {
        $models = Cate::find()
            ->where(['merchant_id' => Yii::$app->services->merchant->getNotNullId()])
            ->orderBy('sort asc, id desc')
            ->asArray()
            ->all();

        return $this->render('index', [
            'models' => $models,
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
        $model->pid = Yii::$app->request->get('pid', null) ?? $model->pid; // 父id
        if ($model->load(Yii::$app->request->post())) {
           if (!$model->save()) {
               return ResultHelper::json(422, $this->getError($model));
           }

            return ResultHelper::json(200, '修改成功', $model);
        }

        $map = ['0' => '顶级分类'];
        if ($model->pid && $parent = Yii::$app->tinyShopService->productCate->findById($model->pid)) {
            $map = [$parent['id'] => $parent['title']];
        }

        return $this->renderAjax($this->action->id, [
            'model' => $model,
            'map' => $map,
        ]);
    }

    /**
     * 移动
     *
     * @param $id
     * @param int $pid
     */
    public function actionMove($id, $pid = 0)
    {
        $model = $this->findModel($id);
        $model->pid = $pid;
        $model->save();
    }

    /**
     * 删除
     *
     * @param $id
     * @return mixed
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        if ($this->findModel($id)->delete()) {
            return ResultHelper::json(200, '删除成功');
        }

        return ResultHelper::json(422, '删除失败');
    }

    /**
     * @param $level
     * @return array|mixed
     */
    public function actionSelect($pid)
    {
        $data = Yii::$app->tinyShopService->productCate->findByPId($pid);
        $list = [];
        foreach ($data as $item) {
            $list[] = [
                'id' => $item['id'],
                'title' => $item['title'],
            ];
        }

        return ResultHelper::json(200, '获取成功', $list);
    }
}