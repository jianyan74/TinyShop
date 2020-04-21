<?php

namespace addons\TinyShop\merchant\modules\base\controllers;

use Yii;
use addons\TinyShop\merchant\controllers\BaseController;
use addons\TinyShop\merchant\forms\CompanyAddressForm;
use common\helpers\ArrayHelper;
use common\traits\MerchantCurd;

/**
 * Class CompanyAddressController
 * @package addons\TinyShop\merchant\modules\base\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class CompanyAddressController extends BaseController
{
    use MerchantCurd;

    /**
     * @var CompanyAddressForm
     */
    public $modelClass = CompanyAddressForm::class;

    /**
     * 编辑/创建
     *
     * @return mixed
     */
    public function actionEdit()
    {
        $request = Yii::$app->request;
        $model = new CompanyAddressForm();
        $model->attributes = $this->getConfig();
        if ($model->load($request->post()) && $model->validate()) {
            $this->setConfig(ArrayHelper::toArray($model));

            return $this->message('修改成功', $this->redirect(['edit']));
        }

        return $this->render('edit', [
            'model' => $model,
        ]);
    }
}