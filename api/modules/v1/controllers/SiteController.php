<?php

namespace addons\TinyShop\api\modules\v1\controllers;

use common\helpers\HashidsHelper;
use Yii;
use yii\web\NotFoundHttpException;
use common\helpers\ResultHelper;
use common\helpers\ArrayHelper;
use common\models\member\Member;
use common\models\common\SmsLog;
use common\enums\StatusEnum;
use common\helpers\AddonHelper;
use api\controllers\OnAuthController;
use addons\TinyShop\api\modules\v1\forms\UpPwdForm;
use addons\TinyShop\api\modules\v1\forms\LoginForm;
use addons\TinyShop\api\modules\v1\forms\RefreshForm;
use addons\TinyShop\api\modules\v1\forms\MobileLogin;
use addons\TinyShop\api\modules\v1\forms\SmsCodeForm;
use addons\TinyShop\api\modules\v1\forms\RegisterForm;
use addons\TinyShop\common\models\SettingForm;

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
    protected $authOptional = ['login', 'refresh', 'mobile-login', 'sms-code', 'register', 'up-pwd'];

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
            return $this->regroupMember(Yii::$app->services->apiAccessToken->getAccessToken($model->getUser(), $model->group));
        }

        // 返回数据验证失败
        return ResultHelper::json(422, $this->getError($model));
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
     * 手机验证码登录
     *
     * @return array|mixed
     * @throws \yii\base\Exception
     */
    public function actionMobileLogin()
    {
        $model = new MobileLogin();
        $model->attributes = Yii::$app->request->post();
        if ($model->validate()) {
            return $this->regroupMember(Yii::$app->services->apiAccessToken->getAccessToken($model->getUser(), $model->group));
        }

        // 返回数据验证失败
        return ResultHelper::json(422, $this->getError($model));
    }

    /**
     * 获取验证码
     *
     * @return int|mixed
     * @throws \yii\web\UnprocessableEntityHttpException
     */
    public function actionSmsCode()
    {
        $model = new SmsCodeForm();
        $model->attributes = Yii::$app->request->post();
        if (!$model->validate()) {
            return ResultHelper::json(422, $this->getError($model));
        }

        // 测试
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
        // return $model->send();
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

        $parent = $model->getParent();

        $member = new Member();
        $member->attributes = ArrayHelper::toArray($model);
        $member->promo_code = '';
        $member->merchant_id = !empty($this->getMerchantId()) ? $this->getMerchantId() : 0;
        $member->password_hash = Yii::$app->security->generatePasswordHash($model->password);
        $member->pid = $parent ? $parent->id : 0;
        if (!$member->save()) {
            return ResultHelper::json(422, $this->getError($member));
        }

        return $this->regroupMember(Yii::$app->services->apiAccessToken->getAccessToken($member, $model->group));
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
     * 重组数据
     *
     * @param $data
     * @return mixed
     */
    protected function regroupMember($data)
    {
        // 优惠券数量
        $data['member']['coupon_num'] = Yii::$app->tinyShopService->marketingCoupon->findCountByMemberId($data['member']['id']);
        // 订单数量统计
        $data['member']['order_synthesize_num'] = Yii::$app->tinyShopService->order->getOrderCountGroupByMemberId($data['member']['id']);
        // 购物车数量
        $data['member']['cart_num'] = Yii::$app->tinyShopService->memberCartItem->count($data['member']['id']);
        $data['promoter'] = '';
        // 开启分销商
        $setting = new SettingForm();
        $setting->attributes = AddonHelper::getConfig();
        $member['is_open_commission'] = $setting->is_open_commission;
        if ($setting->is_open_commission == StatusEnum::ENABLED) {
            $data['promoter'] = Yii::$app->tinyDistributionService->promoter->findByMemberId($data['member']['id']);
        }

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