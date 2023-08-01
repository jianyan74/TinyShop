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
        /** @var FullMail $model */
        $model = Yii::$app->tinyShopService->marketingFullMail->one($this->getMerchantId());
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->message('修改成功', $this->redirect(['index']));
        }

        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }
}