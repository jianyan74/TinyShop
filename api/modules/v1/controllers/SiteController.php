<?php

namespace addons\TinyShop\api\modules\v1\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use common\helpers\ResultHelper;
use common\helpers\ArrayHelper;
use common\models\member\Member;
use common\models\extend\SmsLog;
use common\enums\StatusEnum;
use api\controllers\OnAuthController;
use addons\TinyShop\api\modules\v1\forms\UpPwdForm;
use addons\TinyShop\api\modules\v1\forms\LoginForm;
use addons\TinyShop\api\modules\v1\forms\RefreshForm;
use addons\TinyShop\api\modules\v1\forms\MobileLogin;
use addons\TinyShop\api\modules\v1\forms\SmsCodeForm;
use addons\TinyShop\api\modules\v1\forms\RegisterForm;
use addons\TinyShop\api\modules\v1\forms\RegisterEmailForm;
use addons\TinyShop\api\modules\v1\forms\EmailCodeForm;
use addons\TinyShop\common\enums\AccessTokenGroupEnum;
use yii\web\UnprocessableEntityHttpException;

/**
 * Class SiteController
 * @package addons\TinyShop\api\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class SiteController extends OnAuthController
{
    public $modelClass = '';

    /**
     * 不用进行登录验证的方法
     *
     * 例如： ['index', 'update', 'create', 'view', 'delete']
     * 默认全部需要验证
     *
     * @var array
     */
    protected $authOptional = ['login', 'refresh', 'mobile-login', 'sms-code', 'register', 'up-pwd', 'verify-access-token'];

    /**
     * @var Member
     */
    protected $member;

    /**
     * @param $action
     * @return bool
     * @throws UnprocessableEntityHttpException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\BadRequestHttpException
     * @throws \yii\web\ForbiddenHttpException
     */
    public function beforeAction($action)
    {
        $config = Yii::$app->tinyShopService->config->setting();
        switch ($action->id) {
            case 'login' :
            case 'mobile-login' :
                if ($config['member_login'] == StatusEnum::DISABLED) {
                    throw new UnprocessableEntityHttpException('账号密码/手机验证码登录已关闭');
                }
                break;
            case 'register' :
                if ($config['member_register'] == StatusEnum::DISABLED) {
                    throw new UnprocessableEntityHttpException('会员注册已关闭');
                }
                break;
        }

        return parent::beforeAction($action);
    }

    /**
     * 登录根据用户信息返回accessToken
     *
     * @return array|bool
     * @throws NotFoundHttpException
     * @throws \yii\base\Exception
     */
    public function actionLogin()
    {
        $model = new LoginForm();
        $model->attributes = Yii::$app->request->post();
        if ($model->validate()) {
            $this->member = $model->getUser();

            return $this->regroupMember(Yii::$app->services->apiAccessToken->getAccessToken($model->getUser(), $model->group));
        }

        // 返回数据验证失败
        return ResultHelper::json(422, $this->getError($model));
    }

    /**
     * 手机验证码登录
     *
     * @return array|mixed
     * @throws \yii\base\Exception
     */
    public function actionMobileLogin()
    {
        $model = new MobileLogin();
        $model->attributes = Yii::$app->request->post();
        if (!$model->validate()) {
            return ResultHelper::json(422, $this->getError($model));
        }

        // 已有用户
        if (!empty($model->getUser())) {
            $this->member = $model->getUser();

            return $this->regroupMember(Yii::$app->services->apiAccessToken->getAccessToken($model->getUser(), $model->group));
        }

        $setting = Yii::$app->tinyShopService->config->setting();
        if ($setting->member_mobile_login_be_register == StatusEnum::DISABLED) {
            throw new UnprocessableEntityHttpException('找不到用户');
        }

        return $this->register($model);
    }

    /**
     * 登出
     *
     * @return array|mixed
     */
    public function actionLogout()
    {
        if (Yii::$app->services->apiAccessToken->disableByAccessToken(Yii::$app->user->identity->access_token)) {
            return ResultHelper::json(200, '退出成功');
        }

        return ResultHelper::json(422, '退出失败');
    }

    /**
     * 重置令牌
     *
     * @param $refresh_token
     * @return array
     * @throws NotFoundHttpException
     * @throws \yii\base\Exception
     */
    public function actionRefresh()
    {
        $model = new RefreshForm();
        $model->attributes = Yii::$app->request->post();
        if (!$model->validate()) {
            return ResultHelper::json(422, $this->getError($model));
        }

        return $this->regroupMember(Yii::$app->services->apiAccessToken->getAccessToken($model->getUser(), $model->group));
    }

    /**
     * 获取验证码
     *
     * @return int|mixed
     * @throws \yii\web\UnprocessableEntityHttpException
     */
    public function actionSmsCode()
    {
        $setting = Yii::$app->tinyShopService->config->setting();

        $model = new SmsCodeForm();
        $model->attributes = Yii::$app->request->post();
        $model->member_mobile_login_be_register = $setting->member_mobile_login_be_register;
        if (!$model->validate()) {
            return ResultHelper::json(422, $this->getError($model));
        }

        // 测试
        if (YII_DEBUG) {
            $code = rand(1000, 9999);
            $log = new SmsLog();
            $log = $log->loadDefaultValues();
            $log->attributes = [
                'mobile' => $model->mobile,
                'code' => $code,
                'member_id' => 0,
                'usage' => $model->usage,
                'error_code' => 200,
                'error_msg' => 'ok',
                'error_data' => '',
            ];
            $log->save();

            return $code;
        }

        return $model->send();
    }

    /**
     * 注册
     *
     * @return array|mixed
     * @throws \yii\base\Exception
     */
    public function actionRegister()
    {
        $model = new RegisterForm();
        $model->attributes = Yii::$app->request->post();
        if (!$model->validate()) {
            return ResultHelper::json(422, $this->getError($model));
        }

        return $this->register($model);
    }

    /**
     * 邮箱注册
     *
     * @return array|mixed
     * @throws \yii\base\Exception
     */
    protected function actionRegisterEmail()
    {
        $model = new RegisterEmailForm();
        $model->attributes = Yii::$app->request->post();
        if (!$model->validate()) {
            return ResultHelper::json(422, $this->getError($model));
        }

        return $this->register($model);
    }

    /**
     * 获取邮箱验证码
     *
     * @return int|mixed
     * @throws \yii\web\UnprocessableEntityHttpException
     */
    protected function actionEmailCode()
    {
        $setting = Yii::$app->tinyShopService->config->setting();

        $model = new EmailCodeForm();
        $model->attributes = Yii::$app->request->post();
        $model->member_mobile_login_be_register = $setting->member_mobile_login_be_register;
        if (!$model->validate()) {
            return ResultHelper::json(422, $this->getError($model));
        }

        return $model->send();
    }

    /**
     * 密码重置
     *
     * @return array|mixed
     * @throws \yii\base\Exception
     */
    public function actionUpPwd()
    {
        $model = new UpPwdForm();
        $model->attributes = Yii::$app->request->post();
        if (!$model->validate()) {
            return ResultHelper::json(422, $this->getError($model));
        }

        $member = $model->getUser();
        $member->password_hash = Yii::$app->security->generatePasswordHash($model->password);
        if (!$member->save()) {
            return ResultHelper::json(422, $this->getError($member));
        }

        return $this->regroupMember(Yii::$app->services->apiAccessToken->getAccessToken($member, $model->group));
    }

    /**
     * 校验token有效性
     *
     * @return bool[]
     */
    public function actionVerifyAccessToken()
    {
        $token = Yii::$app->request->post('token');
        if (!$token || !($apiAccessToken = Yii::$app->services->apiAccessToken->findByAccessToken($token))) {
            return [
                'token' => false
            ];
        }

        // 判断验证token有效性是否开启
        if (Yii::$app->params['user.accessTokenValidity'] === true) {
            $timestamp = (int)substr($token, strrpos($token, '_') + 1);
            $expire = Yii::$app->params['user.accessTokenExpire'];

            // 验证有效期
            if ($timestamp + $expire <= time()) {
                return [
                    'token' => true
                ];
            }
        }

        return [
            'token' => true
        ];
    }

    /**
     * 注册
     *
     * @param RegisterForm|RegisterEmailForm|MobileLogin $model
     * @param Member $parent
     * @return array|mixed
     * @throws NotFoundHttpException
     * @throws \yii\base\Exception
     * @throws \yii\web\UnprocessableEntityHttpException
     */
    protected function register($model)
    {
        $parent = $model->getParent();

        $member = new Member();
        $member->attributes = ArrayHelper::toArray($model);
        $member->promoter_code = '';
        $member->source = AccessTokenGroupEnum::relevance($model->group);
        $member->merchant_id = !empty($this->getMerchantId()) ? $this->getMerchantId() : 0;
        isset($model->password) && $member->password_hash = Yii::$app->security->generatePasswordHash($model->password);
        // 未开启分销商不支持绑定上下级关系
        $member->pid = 0;
        if (!$member->save()) {
            return ResultHelper::json(422, $this->getError($member));
        }

        return $this->regroupMember(Yii::$app->services->apiAccessToken->getAccessToken($member, $model->group));
    }

    /**
     * 重组数据
     *
     * @param $data
     * @return mixed
     */
    protected function regroupMember($data)
    {
        // 优惠券数量
        $data['couponNum'] = Yii::$app->tinyShopService->marketingCoupon->findCountByMemberId($data['member']['id']);
        // 订单数量统计
        $data['orderNum'] = Yii::$app->tinyShopService->order->getOrderStatusCountByMemberId($data['member']['id']);
        // 购物车数量
        $data['cartNum'] = Yii::$app->tinyShopService->memberCartItem->findCountByMemberId($data['member']['id']);
        // 开启分销商
        $data['promoter'] = '';
        $data['promoterAccount'] = '';

        // 记录登录时间次数
        !empty($this->member) && Yii::$app->services->member->lastLogin($this->member);

        return $data;
    }

    /**
     * 权限验证
     *
     * @param string $action 当前的方法
     * @param null $model 当前的模型类
     * @param array $params $_GET变量
     * @throws \yii\web\BadRequestHttpException
     */
    public function checkAccess($action, $model = null, $params = [])
    {
        // 方法名称
        if (in_array($action, ['index', 'view', 'update', 'create', 'delete'])) {
            throw new \yii\web\BadRequestHttpException('权限不足');
        }
    }
}
