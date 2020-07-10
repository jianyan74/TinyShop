<?php

namespace addons\TinyShop\api\modules\v1\controllers\member;

use common\helpers\ResultHelper;
use Yii;
use api\controllers\UserAuthController;
use common\models\member\Address;

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

    public function actionCreate()
    {
        /* @var $model Address */
        $model = new $this->modelClass();
        $model->attributes = Yii::$app->request->post();
        $model->member_id = Yii::$app->user->identity->member_id;
        $model->merchant_id = Yii::$app->user->identity->merchant_id;
        if (!$model->save()) {
            return ResultHelper::json(422, $this->getError($model));
        }

        if ($area = Yii::$app->services->provinces->findById($model->area_id)) {
            $tree = explode(' ', $area['tree']);
            foreach ($tree as $key => $item) {
                if ($key == 1) {
                    $data = explode('tr_', $item);
                    $model->province_id = $data[1];
                }

                if ($key == 2) {
                    $data = explode('tr_', $item);
                    $model->city_id = $data[1];
                }
            }
        }

        return $model;
    }

    /**
     * @return array|\yii\db\ActiveRecord|null
     */
    public function actionDefault()
    {
        return Yii::$app->tinyShopService->memberAddress->findDefaultByMemberId(Yii::$app->user->identity->member_id);
    }
}