<?php

namespace addons\TinyShop\backend\modules\common\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use common\traits\MerchantCurd;
use addons\TinyShop\common\models\common\Helper;
use addons\TinyShop\backend\controllers\BaseController;

/**
 * 站点帮助
 *
 * Class HelperController
 * @package addons\TinyShop\backend\modules\common\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class HelperController extends BaseController
{
    use MerchantCurd;

    /**
     * @var Helper
     */
    public $modelClass = Helper::class;

    /**
     * Lists all Tree models.
     * @return mixed
     */
    public function actionIndex()
    {
        $query = $this->modelClass::find()
            ->orderBy('sort asc, created_at asc')
            ->andWhere(['merchant_id' => $this->getMerchantId()]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * @return mixed|string|\yii\console\Response|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionEdit()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        $model = $this->findModel($id);
        $model->pid = $request->get('pid', null) ?? $model->pid; // 父id

        // ajax 验证
        if ($model->load(Yii::$app->request->post())) {
            return $model->save()
                ? $this->redirect(['index'])
                : $this->message($this->getError($model), $this->redirect(['index']), 'error');
        }

        return $this->render($this->action->id, [
            'model' => $model,
            'dropDownList' => Yii::$app->tinyShopService->helper->getDropDownForEdit($id),
        ]);
    }
}