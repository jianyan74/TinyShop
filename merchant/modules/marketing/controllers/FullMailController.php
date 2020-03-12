<?php

namespace addons\TinyShop\merchant\modules\marketing\controllers;

use Yii;
use addons\TinyShop\common\models\marketing\FullMail;
use addons\TinyShop\merchant\controllers\BaseController;

/**
 * Class MarketingFullMailController
 * @package addons\TinyShop\merchant\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class FullMailController extends BaseController
{
    /**
     * 编辑/创建
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $model = $this->findModel();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->message('修改成功', $this->redirect(['index']));
        }

        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     * 返回模型
     *
     * @param $id
     * @return \yii\db\ActiveRecord
     */
    protected function findModel()
    {
        /* @var $model \yii\db\ActiveRecord */
        if (empty(($model = FullMail::find()->andFilterWhere(['merchant_id' => $this->getMerchantId()])->one()))) {
            $model = new FullMail();

            return $model->loadDefaultValues();
        }

        return $model;
    }
}