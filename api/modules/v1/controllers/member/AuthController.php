<?php

namespace addons\TinyShop\api\modules\v1\controllers\member;

use Yii;
use common\enums\StatusEnum;
use common\helpers\ResultHelper;
use common\models\member\Auth;
use api\modules\v1\forms\MiniProgramLoginForm;
use addons\TinyShop\common\enums\AccessTokenGroupEnum;

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
     * 微信公众号授权
     *
     * @return array|mixed
     * @throws \yii\base\Exception
     */
    public function actionWechatMp()
    {
        if (empty($code = Yii::$app->request->post('code'))) {
            return ResultHelper::json(422, '请传递 code');
        }

        $user = Yii::$app->wechat->app->oauth->userFromCode($code);
        // 用户信息
        $original = $user->getRaw();
        $authModel = new Auth();
        $authModel->unionid = $original['unionid'] ?? '';
        $authModel->oauth_client = AccessTokenGroupEnum::relevance(AccessTokenGroupEnum::WECHAT_MP);
        $authModel->oauth_client_user_id = $user->getId();
        $authModel->nickname = $user->getNickname();
        $authModel->head_portrait = $user->getAvatar();
        $authModel->member_id = Yii::$app->user->identity->member_id;
        $authModel->member_type = Yii::$app->user->identity->member_type;

        // 查询授权绑定
        $auth = Yii::$app->services->memberAuth->findOauthClient($authModel->oauth_client, $authModel->oauth_client_user_id);
        if ($auth && $auth->member) {
            return ResultHelper::json(422, '已绑定微信小程序，请先解绑');
        }

        $authModel->save();

        return $authModel;
    }

    /**
     * 微信小程序绑定
     *
     * @return array|mixed
     * @throws \EasyWeChat\Kernel\Exceptions\DecryptException
     * @throws \yii\base\Exception
     * @throws \Exception
     */
    public function actionWechatMini()
    {
        $model = new MiniProgramLoginForm();
        $model->attributes = Yii::$app->request->post();

        if (!$model->validate()) {
            return ResultHelper::json(422, $this->getError($model));
        }

        $user = $model->getUser();
        $authModel = new Auth();
        $authModel->unionid = $model->getUnionId();
        $authModel->oauth_client = AccessTokenGroupEnum::relevance(AccessTokenGroupEnum::WECHAT_MINI);
        $authModel->oauth_client_user_id = $model->getOpenid();
        $authModel->nickname = $user['nickName'];
        $authModel->head_portrait = $user['avatarUrl'];
        $authModel->gender = $user['gender'];
        $authModel->member_id = Yii::$app->user->identity->member_id;
        $authModel->member_type = Yii::$app->user->identity->member_type;

        // 查询授权绑定
        $auth = Yii::$app->services->memberAuth->findOauthClient($authModel->oauth_client, $authModel->oauth_client_user_id);
        if ($auth && $auth->member) {
            return ResultHelper::json(422, '已绑定微信小程序，请先解绑');
        }

        $authModel->save();

        return $authModel;
    }

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
