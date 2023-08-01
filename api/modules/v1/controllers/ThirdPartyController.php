<?php

namespace addons\TinyShop\api\modules\v1\controllers;

use Yii;
use yii\helpers\Json;
use yii\web\UnprocessableEntityHttpException;
use api\controllers\OnAuthController;
use api\modules\v1\forms\MiniProgramLoginForm;
use api\modules\v1\forms\MiniProgramDecodeForm;
use api\modules\v1\forms\ByteDanceMicroLoginForm;
use common\models\member\Auth;
use common\helpers\ResultHelper;
use common\enums\WhetherEnum;
use common\models\member\Member;
use common\enums\GenderEnum;
use common\helpers\StringHelper;
use common\helpers\FileHelper;
use common\helpers\RegularHelper;
use common\enums\MemberTypeEnum;
use common\enums\StatusEnum;
use addons\TinyShop\api\modules\v1\forms\AppleLoginForm;
use addons\TinyShop\common\enums\AccessTokenGroupEnum;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

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
    protected $authOptional = [
        'wechat',
        'apple',
        'wechat-mp',
        'wechat-mini',
        'wechat-mini-mobile',
        'byte-dance-mini',
        'wechat-mp-js-sdk',
        'wechat-mini-qr-code',
    ];

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
            case 'wechat' :
            case 'apple' :
            case 'wechat-mp' :
            case 'wechat-mini' :
            case 'byte-dance-mini' :
                if ($config['member_login'] == StatusEnum::DISABLED) {
                    throw new UnprocessableEntityHttpException('授权登录已关闭');
                }
                break;
        }

        return parent::beforeAction($action);
    }

    /**
     * 微信公众号登录
     *
     * @return array|mixed
     * @throws \yii\base\Exception
     */
    public function actionWechatMp()
    {
        if (empty($code = Yii::$app->request->get('code'))) {
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

        return $this->getMember($authModel, AccessTokenGroupEnum::WECHAT_MP);
    }

    /**
     * 微信小程序登录
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
        if ($user['nickName'] == '微信用户') {
            $user['nickName'] = StringHelper::random(6);
        }

        $authModel = new Auth();
        $authModel->unionid = $model->getUnionId();
        $authModel->oauth_client = AccessTokenGroupEnum::relevance(AccessTokenGroupEnum::WECHAT_MINI);
        $authModel->oauth_client_user_id = $model->getOpenid();
        $authModel->nickname = $user['nickName'];
        $authModel->head_portrait = $user['avatarUrl'];
        $authModel->gender = $user['gender'];

        return $this->getMember($authModel, AccessTokenGroupEnum::WECHAT_MINI);
    }

    /**
     * 微信小程序手机号码绑定
     *
     * @return array|mixed
     * @throws \EasyWeChat\Kernel\Exceptions\DecryptException
     * @throws \yii\base\Exception
     * @throws \Exception
     */
    public function actionWechatMiniMobile()
    {
        $model = new MiniProgramDecodeForm();
        $model->attributes = Yii::$app->request->post();
        if (!$model->validate()) {
            return ResultHelper::json(422, $this->getError($model));
        }

        $info = $model->getUser();
        if (!Yii::$app->user->isGuest) {
            // 判断手机号是否已绑定用户
            if (Yii::$app->services->member->findByCondition(['mobile' => $info['purePhoneNumber'], 'type' => MemberTypeEnum::MEMBER])) {
                throw new UnprocessableEntityHttpException('该手机号码已绑定账号');
            }

            // 更新用户手机号码
            Member::updateAll(['mobile' => $info['purePhoneNumber']], ['id' => Yii::$app->user->identity->member_id]);
        }

        return $info;
    }

    /**
     * 字节跳动小程序登录
     *
     * @return array|mixed
     * @throws \EasyWeChat\Kernel\Exceptions\DecryptException
     * @throws \yii\base\Exception
     * @throws \Exception
     */
    public function actionByteDanceMini()
    {
        $model = new ByteDanceMicroLoginForm();
        $model->attributes = Yii::$app->request->post();

        if (!$model->validate()) {
            return ResultHelper::json(422, $this->getError($model));
        }

        $user = $model->getUser();
        $authModel = new Auth();
        $authModel->unionid = $model->getUnionId();
        $authModel->oauth_client = AccessTokenGroupEnum::relevance(AccessTokenGroupEnum::BYTEDANCE_MINI);
        $authModel->oauth_client_user_id = $model->getOpenid();
        $authModel->nickname = $user['nickName'];
        $authModel->head_portrait = $user['avatarUrl'];
        $authModel->gender = $user['gender'];

        return $this->getMember($authModel, AccessTokenGroupEnum::BYTEDANCE_MINI);
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
            // Yii::$app->services->extendOpenPlatform->apple($model->user, $model->identityToken);
            // $familyName = $model->familyName['familyName'] ?? '';
            // $giveName = $model->familyName['giveName'] ?? '';
            $nickname = StringHelper::random(5) . '_' . StringHelper::random(4, true);

            $authModel = new Auth();
            $authModel->oauth_client = AccessTokenGroupEnum::relevance(AccessTokenGroupEnum::APPLE);;
            $authModel->oauth_client_user_id = $model->user;
            $authModel->nickname = $nickname;
            $authModel->gender = GenderEnum::UNKNOWN;

            return $this->getMember($authModel, AccessTokenGroupEnum::IOS);
        } catch (\Exception $e) {
            if (YII_DEBUG) {
                return ResultHelper::json(422, $e->getMessage());
            }

            return ResultHelper::json(422, '用户验证失败请重新授权');
        }
    }

    /**
     * 微信开放平台登录
     *
     * @return array|mixed
     * @throws \yii\base\Exception
     */
    public function actionWechat()
    {
        if (!($code = Yii::$app->request->get('code'))) {
            return ResultHelper::json(422, '请传递 code');
        }

        if (!($group = Yii::$app->request->get('group')) && !in_array($group, AccessTokenGroupEnum::getMap())) {
            return ResultHelper::json(422, '请传递有效的组别');
        }

        $original = Yii::$app->services->extendOpenPlatform->wechat($code);

        $authModel = new Auth();
        $authModel->unionid = $original['unionid'] ?? '';
        $authModel->oauth_client = AccessTokenGroupEnum::relevance(AccessTokenGroupEnum::WECHAT);;
        $authModel->oauth_client_user_id = $original['openid'];
        $authModel->nickname = $original['nickname'];
        $authModel->head_portrait = $original['headimgurl'];
        $authModel->gender = $original['sex'];

        return $this->getMember($authModel, $group);
    }

    /**
     * 生成小程序码
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    public function actionWechatMiniQrCode($id, $marketing_id = '', $marketing_type = '', $marketing_product_id = '')
    {
        if (!($product = Yii::$app->tinyShopService->product->findById($id))) {
            return ResultHelper::json(422, '找不到商品');
        }

        $path = "pages/product/product?id=$id&marketingType=$marketing_type&marketingId=$marketing_id&marketingProductId=$marketing_product_id";
        empty($marketing_type) && $marketing_type = 'default';
        $prefix = "/mini_program/product/$marketing_type/";

        $promoCode = 0;
        if (!Yii::$app->user->isGuest) {
            $member = Yii::$app->services->member->findById(Yii::$app->user->identity->member_id);
            $promoCode = $member['promoter_code'];
            $path = $path . '&promoter_code=' . $promoCode;
        }

        $uploadDrive = new Local(Yii::getAlias('@attachment'), FILE_APPEND);
        $filesystem = new Filesystem($uploadDrive);
        $directory = Yii::getAlias('@attachment') . $prefix;
        FileHelper::mkdirs($directory);

        $filename = $id . '_' . $marketing_id . '_' . $marketing_product_id . '_' . $promoCode;
        if (!$filesystem->has($prefix . $filename . '.png')) {
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

            $content = $response->getBody()->getContents();

            // 保存小程序码到文件
            if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
                $response->saveAs($directory, $filename . '.png');
            }
        }

        $url = Yii::getAlias('@attachurl') . $prefix . $filename . '.png';
        if (!RegularHelper::verify('url', $url)) {
            $url = Yii::$app->request->hostInfo . $url;
        }

        return [
            'url' => $url
        ];
    }

    /*
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function actionWechatMpJsSdk()
    {
        $url = Yii::$app->request->post('url');
        $apis = Yii::$app->request->post('jsApiList');
        $debug = Yii::$app->request->post('debug', false);

        $apis = !empty($apis) ? Json::decode($apis) : [];

        Yii::$app->wechat->app->jssdk->setUrl($url);

        return Yii::$app->wechat->app->jssdk->buildConfig($apis, $debug, $beta = false, $json = false);
    }

    /**
     * @param Auth $auth
     * @return array
     */
    protected function getMember(Auth $authModel, $group)
    {
        // 查询授权绑定
        $auth = Yii::$app->services->memberAuth->findOauthClient($authModel->oauth_client, $authModel->oauth_client_user_id);
        if ($auth && $auth->member) {
            return [
                'login' => true,
                'user_info' => $this->getData($auth, $group),
            ];
        }

        // 查询唯一绑定
        if ($authModel->unionid && ($auth = Yii::$app->services->memberAuth->findByUnionId($authModel->unionid)) && $auth->member) {
            $authModel->member_id = $auth->member->id;
            $authModel->head_portrait = $auth->member->head_portrait;
            Yii::$app->services->memberAuth->create($authModel->toArray());

            return [
                'login' => true,
                'user_info' => $this->getData($auth, $group),
            ];
        }

        // 判断是否强制注册
        if ($this->isConstraintRegister() === true) {
            return [
                'login' => false,
                'user_info' => $authModel->toArray()
            ];
        }

        $member = $this->createMember($authModel->head_portrait, $authModel->gender, $authModel->nickname, $authModel->oauth_client, Yii::$app->request->get('promoter_code'));
        $authModel->member_id = $member['id'];
        $authModel->head_portrait = $member['head_portrait'];
        $auth = Yii::$app->services->memberAuth->create($authModel->toArray());

        return [
            'login' => true,
            'user_info' => $this->getData($auth, $group)
        ];
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
    protected function createMember($head_portrait, $gender, $nickname, $source, $promoCode = '')
    {
        // 下载头像
        $baseInfo = Yii::$app->services->extendUpload->downloadByUrl($head_portrait);

        // 注册新账号
        $member = new Member();
        $member = $member->loadDefaultValues();
        $member->merchant_id = Yii::$app->services->merchant->getNotNullId();
        $member->source = $source;
        $member->pid = 0;
        $member->attributes = [
            'gender' => $gender,
            'nickname' => $nickname,
            'head_portrait' => $baseInfo['url'] ?? '',
        ];

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
        $data['couponNum'] = Yii::$app->tinyShopService->marketingCoupon->findCountByMemberId($data['member']['id']);
        // 购物车数量
        $data['cartNum'] = Yii::$app->tinyShopService->memberCartItem->findCountByMemberId($data['member']['id']);
        // 订单数量统计
        $data['orderNum'] = Yii::$app->tinyShopService->order->getOrderStatusCountByMemberId($data['member']['id']);
        // 分销商
        $data['promoter'] = '';
        $data['promoterAccount'] = '';

        // 记录登录时间次数
        Yii::$app->services->member->lastLogin($auth->member);

        return $data;
    }

    /**
     * @param $promoCode
     * @return array|bool|\yii\db\ActiveRecord
     * @throws UnprocessableEntityHttpException
     */
    protected function getParent($promoCode)
    {
        if (empty($promoCode) || $promoCode == 'undefined') {
            return false;
        }

        $parent = Yii::$app->services->member->findByPromoterCode($promoCode);
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
        $setting = Yii::$app->tinyShopService->config->setting();
        if ($setting->member_third_party_binding_type == WhetherEnum::ENABLED) {
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
