<?php

namespace addons\TinyShop\api\modules\v1\controllers\member;

use Yii;
use common\enums\StatusEnum;
use common\helpers\ResultHelper;
use common\models\member\Auth;

/**
 * Class AuthController
 * @package addons\TinyShop\api\modules\v1\controllers\member
 * @author jianyan74 <751393839@qq.com>
 */
class AuthController extends \api\modules\v1\controllers\member\AuthController
{
    /**
     * 不用进行登录验证的方法
     * 例如： ['index', 'update', 'create', 'view', 'delete']
     * 默认全部需要验证
     *
     * @var array
     */
    protected $authOptional = ['binding-equipment'];

    /**
     * 绑定设备进行 app 推送
     */
    public function actionBindingEquipment()
    {
        $oauthClient = Yii::$app->request->post('oauth_client');
        $oauthClientUserId = Yii::$app->request->post('oauth_client_user_id');
        $token = Yii::$app->request->post('token');
        if (!in_array($oauthClient, ['ios', 'android'])) {
            return false;
        }

        if (!$token || !($apiAccessToken = Yii::$app->services->apiAccessToken->findByAccessToken($token))) {
            return false;
        }

        /** @var Auth $model */
        if (!($model = Yii::$app->services->memberAuth->findOauthClientByApp($oauthClient, $oauthClientUserId))) {
            $model = new $this->modelClass();
            $model = $model->loadDefaultValues();
            $model->attributes = Yii::$app->request->post();
        }

        $model->oauth_client = $oauthClient;
        $model->oauth_client_user_id = $oauthClientUserId;
        $model->member_id = $apiAccessToken->member_id;
        $model->status = StatusEnum::DISABLED;
        if (!$model->save()) {
            return ResultHelper::json(422, $this->getError($model));
        }

        return $model;
    }
}