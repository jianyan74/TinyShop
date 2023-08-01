<?php

namespace addons\TinyShop\api\modules\v1\controllers\member;

use Yii;
use api\controllers\UserAuthController;
use common\models\member\Address;
use common\helpers\ResultHelper;

/**
 * 地址
 *
 * Class AddressController
 * @package addons\TinyShop\api\controllers\member
 * @author jianyan74 <751393839@qq.com>
 */
class AddressController extends UserAuthController
{
    /**
     * @var Address
     */
    public $modelClass = Address::class;

    /**
     * @return array|Address|mixed|\yii\db\ActiveRecord
     */
    public function actionCreate()
    {
        /* @var $model Address */
        $model = new $this->modelClass();
        $model->attributes = Yii::$app->request->post();
        $model->member_id = Yii::$app->user->identity->member_id;
        $model->merchant_id = Yii::$app->user->identity->merchant_id;
        list($province_id, $city_id, $area_id) = Yii::$app->services->provinces->getParentIdsByAreaId($model->area_id);
        $model->province_id = $province_id;
        $model->city_id = $city_id;
        $model->area_id = $area_id;
        if (!$model->save()) {
            return ResultHelper::json(422, $this->getError($model));
        }

        return $model;
    }

    /**
     * @param $id
     * @return array|mixed|\yii\db\ActiveRecord
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->attributes = Yii::$app->request->post();
        list($province_id, $city_id, $area_id) = Yii::$app->services->provinces->getParentIdsByAreaId($model->area_id);
        $model->province_id = $province_id;
        $model->city_id = $city_id;
        $model->area_id = $area_id;

        if (!$model->save()) {
            return ResultHelper::json(422, $this->getError($model));
        }

        return $model;
    }

    /**
     * @return array|\yii\db\ActiveRecord|null
     */
    public function actionDefault()
    {
        return Yii::$app->services->memberAddress->findDefaultByMemberId(Yii::$app->user->identity->member_id);
    }
}