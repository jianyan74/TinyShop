<?php

namespace addons\TinyShop\merchant\modules\common\controllers;

use Yii;
use addons\TinyShop\common\enums\ProtocolNameEnum;
use addons\TinyShop\common\models\common\Protocol;
use addons\TinyShop\merchant\controllers\BaseController;

/**
 * 协议管理
 *
 * Class ProtocolController
 * @package addons\TinyShop\merchant\modules\common\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class ProtocolController extends BaseController
{
    /**
     * @var Protocol
     */
    public $modelClass = Protocol::class;

    /**
     * @return string
     */
    public function actionIndex()
    {
        return $this->render($this->action->id, [
            'protocolNameMap' => ProtocolNameEnum::getMap()
        ]);
    }

    /**
     * 编辑/创建
     *
     * @return mixed
     */
    public function actionEdit()
    {
        $name = Yii::$app->request->get('name', null);
        $model = $this->findModelByName($name);
        $model->title = ProtocolNameEnum::getValue($name);
        $model->name = $name;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->referrer();
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
    protected function findModelByName($name)
    {
        /* @var $model \yii\db\ActiveRecord */
        if (empty($name) || empty($model = $this->modelClass::find()
                ->where(['name' => $name])
                ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
                ->one())
        ) {
            $model = new $this->modelClass;
            return $model->loadDefaultValues();
        }

        return $model;
    }
}
