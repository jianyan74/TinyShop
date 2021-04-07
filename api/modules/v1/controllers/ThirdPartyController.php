<?php

namespace addons\TinyShop\api\modules\v1\controllers;

use Yii;
use yii\helpers\Json;
use yii\web\UnprocessableEntityHttpException;
use api\controllers\OnAuthController;
use api\modules\v1\forms\MiniProgramLoginForm;
use common\helpers\ResultHelper;
use common\models\member\Auth;
use common\enums\WhetherEnum;
use common\helpers\AddonHelper;
use common\models\member\Member;
use common\enums\GenderEnum;
use common\helpers\StringHelper;
use common\enums\StatusEnum;
use common\helpers\FileHelper;
use common\helpers\RegularHelper;
use common\helpers\UploadHelper;
use addons\TinyShop\api\modules\v1\forms\AppleLoginForm;
use addons\TinyShop\common\enums\AccessTokenGroupEnum;
use addons\TinyShop\common\enums\ProductMarketingEnum;
use addons\TinyShop\common\models\SettingForm;

/**
 * 第三方授权登录
 *
 * Class ThirdPartyController
 * @package addons\TinyShop\api\modules\v1\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class ThirdPartyController extends OnAuthController
{
    public $modelClass = '';

    /**
     * 不用进行登录验证的方法
     * 例如： ['index', 'update', 'create', 'view', 'delete']
     * 默认全部需要验证
     *
     * @var array
     */
    protected $authOptional = ['wechat', 'wechat-mp', 'wechat-open-platform', 'apple', 'wechat-js-sdk', 'qr-code'];

    /**
     * 微信登录
     *
     * @return array|mixed
     * @throws \yii\base\Exception
     */
    public function actionWechat()
    {
        if (!Yii::$app->request->get('code')) {
            return ResultHelper::json(422, '请传递 code');
        }

        $user = Yii::$app->wechat->app->oauth->user();
        // 用户信息
        $original = $user['original'];
        $unionid = $original['unionid'] ?? '';
        $auth = Yii::$app->services->memberAuth->findOauthClient(Auth::CLIENT_WECHAT, $user['id']);
        if ($auth && $auth->member) {
            return [
                'login' => true,
                'user_info' => $this->getData($auth, AccessTokenGroupEnum::WECHAT),
            ];
        }

        // 唯一id 关联
        if ($unionid && ($auth = Yii::$app->services->memberAuth->findByUnionId($unionid)) && $auth->member) {
            return [
                'login' => true,
                'user_info' => $this->getData($auth, AccessTokenGroupEnum::WECHAT),
            ];
        }

        // 判断是否强制注册
        if ($this->isConstraintRegister() === true) {
            return [
                'login' => false,
                'user_info' => $user
            ];
        }

        $member = $this->createMember($original['headimgurl'], $original['sex'], $original['nickname'], Yii::$app->request->get('promo_code'));
        $auth = Yii::$app->services->memberAuth->create([
            'oauth_client' => Auth::CLIENT_WECHAT,
            'unionid' => $original['unionid'] ?? '',
            'member_id' => $member['id'],
            'oauth_client_user_id' => $original['openid'],
            'gender' => $original['sex'],
            'nickname' => $original['nickname'],
            'head_portrait' => $member['head_portrait'],
            'country' => $original['country'],
            'province' => $original['province'],
            'city' => $original['city'],
            'language' => $original['language'],
        ]);

        return [
            'login' => true,
            'user_info' => $this->getData($auth, AccessTokenGroupEnum::WECHAT)
        ];
    }

    /**
     * 微信小程序登录
     *
     * @return array|mixed
     * @throws \EasyWeChat\Kernel\Exceptions\DecryptException
     * @throws \yii\base\Exception
     * @throws \Exception
     */
    public function actionWechatMp()
    {
        $model = new MiniProgramLoginForm();
        $model->attributes = Yii::$app->request->post();

        if (!$model->validate()) {
            return ResultHelper::json(422, $this->getError($model));
        }

        $user = $model->getUser();
        $unionid = $user['unionId'] ?? '';
        $auth = Yii::$app->services->memberAuth->findOauthClient(Auth::CLIENT_WECHAT_MP, $model->getOpenid());
        if ($auth && $auth->member) {
            $user_info = $this->getData($auth, AccessTokenGroupEnum::WECHAT_MP);
            unset($user_info['watermark']);

            return [
                'login' => true,
                'user_info' => $user_info,
            ];
        }

        // 唯一id 关联
        if ($unionid && ($auth = Yii::$app->services->memberAuth->findByUnionId($unionid)) && $auth->member) {
            return [
                'login' => true,
                'user_info' => $this->getData($auth, AccessTokenGroupEnum::WECHAT_MP),
            ];
        }

        // 判断是否强制注册
        if ($this->isConstraintRegister() === true) {
            return [
                'login' => false,
                'user_info' => $user
            ];
        }

        $member = $this->createMember($user['avatarUrl'], $user['gender'], $user['nickName'], Yii::$app->request->post('promo_code'));
        $auth = Yii::$app->services->memberAuth->create([
            'unionid' => $unionid,
            'member_id' => $member['id'],
            'oauth_client' => Auth::CLIENT_WECHAT_MP,
            'oauth_client_user_id' => $model->getOpenid(),
            'gender' => $user['gender'],
            'nickname' => $user['nickName'],
            'head_portrait' => $member['head_portrait'],
            'country' => $user['country'],
            'province' => $user['province'],
            'city' => $user['city'],
            'language' => $user['language'],
        ]);

        return [
            'login' => true,
            'user_info' => $this->getData($auth, AccessTokenGroupEnum::WECHAT_MP)
        ];
    }

    /**
     * 微信开放平台登录
     *
     * @return array|mixed
     * @throws \yii\base\Exception
     */
    public function actionWechatOpenPlatform()
    {
        if (!($code = Yii::$app->request->get('code'))) {
            return ResultHelper::json(422, '请传递 code');
        }

        if (!($group = Yii::$app->request->get('group')) && !in_array($group, AccessTokenGroupEnum::getMap())) {
            return ResultHelper::json(422, '请传递有效的组别');
        }

        $original = Yii::$app->services->openPlatform->wechat($code);
        $unionid = $original['unionid'] ?? '';
        $auth = Yii::$app->services->memberAuth->findOauthClient(Auth::CLIENT_WECHAT_OP, $original['openid']);
        if ($auth && $auth->member) {
            return [
                'login' => true,
                'user_info' => $this->getData($auth, $group),
            ];
        }

        // 唯一id 关联
        if ($unionid && ($auth = Yii::$app->services->memberAuth->findByUnionId($unionid)) && $auth->member) {
            return [
                'login' => true,
                'user_info' => $this->getData($auth, $group),
            ];
        }

        // 判断是否强制注册
        if ($this->isConstraintRegister() === true) {
            return [
                'login' => false,
                'user_info' => $original
            ];
        }

        $member = $this->createMember($original['headimgurl'], $original['sex'], $original['nickname'], Yii::$app->request->get('promo_code'));
        $auth = Yii::$app->services->memberAuth->create([
            'oauth_client' => Auth::CLIENT_WECHAT_OP,
            'unionid' => $unionid,
            'member_id' => $member['id'],
            'oauth_client_user_id' => $original['openid'],
            'gender' => $original['sex'],
            'nickname' => $original['nickname'],
            'head_portrait' => $member['head_portrait'],
            'country' => $original['country'],
            'province' => $original['province'],
            'city' => $original['city'],
            'language' => $original['language'],
        ]);

        return [
            'login' => true,
            'user_info' => $this->getData($auth, $group)
        ];
    }

    /**
     * apple 登录
     *
     * @return array|mixed
     */
    public function actionApple()
    {
        $model = new AppleLoginForm();
        $model->attributes = Yii::$app->request->post();
        if (!$model->validate()) {
            return ResultHelper::json(422, $this->getError($model));
        }

        try {
            // Yii::$app->services->openPlatform->apple($model->user, $model->identityToken);

            $auth = Yii::$app->services->memberAuth->findOauthClient(Auth::CLIENT_APPLE, $model->user);
            if ($auth && $auth->member) {
                return [
                    'login' => true,
                    'user_info' => $this->getData($auth, AccessTokenGroupEnum::IOS),
                ];
            }

            // 判断是否强制注册
            if ($this->isConstraintRegister() === true) {
                return [
                    'login' => false,
                    'user_info' => $model
                ];
            }

            $nickname = StringHelper::random(5) . '_' . StringHelper::random(4, true);
            $member = $this->createMember('', GenderEnum::UNKNOWN, $nickname, Yii::$app->request->post('promo_code'));

            $familyName = $model->familyName['familyName'] ?? '';
            $giveName = $model->familyName['giveName'] ?? '';
            $auth = Yii::$app->services->memberAuth->create([
                'oauth_client' => Auth::CLIENT_APPLE,
                'member_id' => $member['id'],
                'oauth_client_user_id' => $model->user,
                'gender' => GenderEnum::UNKNOWN,
                'nickname' => $familyName . $giveName,
            ]);

            return [
                'login' => true,
                'user_info' => $this->getData($auth, AccessTokenGroupEnum::IOS)
            ];
        } catch (\Exception $e) {
            if (YII_DEBUG) {
                return ResultHelper::json(422, $e->getMessage());
            }

            return ResultHelper::json(422, '用户验证失败请重新授权');
        }
    }

    /**
     * 生成小程序码
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    public function actionQrCode($id)
    {
        if (!Yii::$app->tinyShopService->product->findById($id)) {
            return ResultHelper::json(422, '找不到商品');
        }

        $path = 'pages/product/product?id=' . $id;
        $prefix = '/mini_program/product/default/';

        $uploadDrive = Yii::$app->uploadDrive->local([
            'superaddition' => true
        ]);
        $filesystem = $uploadDrive->entity();

        $directory = Yii::getAlias('@attachment') . $prefix;
        FileHelper::mkdirs($directory);

        if (!$filesystem->has($prefix . $id . '.png')) {
            // 指定颜色
            // $response 成功时为 EasyWeChat\Kernel\Http\StreamResponse 实例，失败时为数组或者你指定的 API 返回格式
            $response = Yii::$app->wechat->miniProgram->app_code->get($path, [
                'width' => 300,
                // 'line_color' => [
                //     'r' => 105,
                //     'g' => 166,
                //     'b' => 134,
                // ],
            ]);

            // 保存小程序码到文件
            if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
                $filename = $response->saveAs($directory, $id . '.png');
            }
        }

        $url = Yii::getAlias('@attachurl') . $prefix . $id . '.png';
        if (!RegularHelper::verify('url', $url)) {
            $url = Yii::$app->request->hostInfo . $url;
        }

        return [
            'url' => $url
        ];
    }

    /**
     * 微信jssdk
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function actionWechatJsSdk()
    {
        $url = Yii::$app->request->post('url');
        $apis = Yii::$app->request->post('jsApiList');
        $debug = Yii::$app->request->post('debug', false);

        $apis = !empty($apis) ? Json::decode($apis) : [];

        $app = Yii::$app->wechat->app;
        $app->jssdk->setUrl($url);

        return $app->jssdk->buildConfig($apis, $debug, $beta = false, $json = false);
    }

    /**
     * 创建用户
     *
     * @param $head_portrait
     * @param $gender
     * @param $nickname
     * @param string $promoCode
     * @return Member
     * @throws UnprocessableEntityHttpException
     * @throws \League\Flysystem\FileExistsException
     * @throws \League\Flysystem\FileNotFoundException
     * @throws \yii\web\NotFoundHttpException
     */
    protected function createMember($head_portrait, $gender, $nickname, $promoCode = '')
    {
        if ($head_portrait) {
            // 下载图片
            $upload = new UploadHelper(['writeTable' => StatusEnum::DISABLED], 'images');
            $imgData = $upload->verifyUrl($head_portrait);
            $upload->save($imgData);
            $baseInfo = $upload->getBaseInfo();
        }

        // 注册新账号
        $member = new Member();
        $member = $member->loadDefaultValues();
        $member->merchant_id = Yii::$app->services->merchant->getNotNullId();
        $member->pid = 0;
        $member->attributes = [
            'gender' => $gender,
            'nickname' => $nickname,
            'head_portrait' => $baseInfo['url'] ?? '',
        ];
        // 推广员
        if ($promoCode) {
            $parent = $this->getParent($promoCode);
            $member->pid = $parent->id;
        }
        $member->save();

        return $member;
    }

    /**
     * @param $auth
     * @return array
     * @throws \yii\base\Exception
     */
    protected function getData($auth, $group = AccessTokenGroupEnum::ANDROID)
    {
        $data = Yii::$app->services->apiAccessToken->getAccessToken($auth->member, $group);
        // 优惠券数量
        $data['member']['coupon_num'] = Yii::$app->tinyShopService->marketingCoupon->findCountByMemberId($data['member']['id']);
        // 订单数量统计
        $data['member']['order_synthesize_num'] = Yii::$app->tinyShopService->order->getOrderCountGroupByMemberId($data['member']['id']);

        return $data;
    }

    /**
     * @param $promoCode
     * @return array|bool|\yii\db\ActiveRecord
     * @throws UnprocessableEntityHttpException
     */
    protected function getParent($promoCode)
    {
        if (empty($promoCode)) {
            return false;
        }

        $parent = Yii::$app->services->member->findByPromoCode($promoCode);
        if (!$parent) {
            throw new UnprocessableEntityHttpException('找不到推广员');
        }

        return $parent;
    }

    /**
     * 强制注册
     *
     * @return bool
     */
    protected function isConstraintRegister()
    {
        // 判断非强制性登录
        $setting = new SettingForm();
        $setting->attributes = AddonHelper::getConfig();
        if ($setting->third_party_register == WhetherEnum::ENABLED) {
            return false;
        }

        return true;
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