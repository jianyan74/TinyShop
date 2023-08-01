<?php

namespace addons\TinyShop\api\modules\v1\controllers\member;

use Yii;
use yii\web\NotFoundHttpException;
use common\helpers\ResultHelper;
use common\models\member\Member;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use api\controllers\OnAuthController;
use addons\TinyShop\common\enums\OrderStatusEnum;
use addons\TinyShop\api\modules\v1\forms\MobileBindingForm;
use addons\TinyShop\api\modules\v1\forms\MobileResetForm;
use addons\TinyShop\api\modules\v1\forms\UpPayPwdForm;

/**
 * 个人信息
 *
 * Class MemberController
 * @package addons\TinyShop\api\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class MemberController extends OnAuthController
{
    /**
     * @var Member
     */
    public $modelClass = Member::class;

    /**
     * 个人中心
     *
     * @return array|null|\yii\data\ActiveDataProvider|\yii\db\ActiveRecord
     */
    public function actionIndex()
    {
        $member_id = Yii::$app->user->identity->member_id;

        $data = [];
        $data['member'] = $this->modelClass::find()
            ->where(['id' => $member_id])
            ->with(['account', 'memberLevel'])
            ->asArray()
            ->one();

        // 优惠券数量
        $data['couponNum'] = Yii::$app->tinyShopService->marketingCoupon->findCountByMemberId($member_id);
        // 购物车数量
        $data['cartNum'] = Yii::$app->tinyShopService->memberCartItem->findCountByMemberId($member_id);
        // 订单数量统计
        $data['orderNum'] = Yii::$app->tinyShopService->order->getOrderStatusCountByMemberId($member_id);
        // 消息数量
        $data['notifyNum'] = Yii::$app->tinyShopService->notifyMember->unReadCount($member_id);;
        // 开启分销商
        $data['promoter'] = '';
        $data['promoterAccount'] = '';
        // 开启签到
        $data['signOpen'] = StatusEnum::DISABLED;
        // 判断是否会员
        $data['memberCard'] = [];

        return $data;
    }

    /**
     * 更新
     *
     * @param $id
     * @return bool|mixed
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $data = Yii::$app->request->post();
        $data = ArrayHelper::filter($data, [
            'nickname',
            'head_portrait',
            'realname',
            'birthday',
            'province_id',
            'city_id',
            'area_id',
            'address',
            'qq',
            'email',
            'gender',
            'bg_image',
            'description',
        ]);

        $model = $this->findModel($id);
        $model->attributes = $data;
        if (!$model->save()) {
            return ResultHelper::json(422, $this->getError($model));
        }

        return 'ok';
    }

    /**
     * 手机号码重置
     *
     * @return array|mixed
     * @throws \yii\base\Exception
     */
    public function actionMobileReset()
    {
        $model = new MobileResetForm();
        $model->attributes = Yii::$app->request->post();
        if ($model->validate()) {
            $member = $model->getUser();
            $member->mobile_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
            $member->save();

            return [
                'mobile_reset_token' => $member->mobile_reset_token
            ];
        }

        // 返回数据验证失败
        return ResultHelper::json(422, $this->getError($model));
    }

    /**
     * 手机号码绑定
     *
     * @return array|mixed|\yii\db\ActiveRecord|null
     */
    public function actionMobileBinding()
    {
        $member_id = Yii::$app->user->identity->member_id;
        $member = Yii::$app->services->member->findById($member_id);

        $model = new MobileBindingForm();
        $model->attributes = Yii::$app->request->post();
        $model->user = $member;
        if ($model->validate()) {
            $member->mobile_reset_token = '';
            $member->mobile = $model->mobile;
            $member->save();

            return $model->user;
        }

        return ResultHelper::json(422, $this->getError($model));
    }

    /**
     * 修改支付密码
     *
     * @return array|mixed
     * @throws \yii\base\Exception
     */
    public function actionUpdatePayPassword()
    {
        $model = new UpPayPwdForm();
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
     * 注销
     *
     * @return array|mixed|string
     */
    public function actionCancel()
    {
        $member_id = Yii::$app->user->identity->member_id;
        $member = Yii::$app->services->member->findById($member_id);

        // 余额判断
        $account = Yii::$app->services->memberAccount->findByMemberId($member_id);
        if($account->user_money > 0) {
            return ResultHelper::json(422, '账户还有余额，无法注销');
        }

        // 订单判断
        $orderStatus = Yii::$app->tinyShopService->order->getOrderCountGroupByStatus(['buyer_id' => $member_id]);
        foreach ($orderStatus as $key => $count) {
            if (in_array($key, [OrderStatusEnum::PAY, OrderStatusEnum::SHIPMENTS, -1]) && $count > 0) {
                return ResultHelper::json(422, '还存在未完成订单, 无法注销');
            }
        }

        // 注销
        Yii::$app->services->memberCancel->create($member);

        return ResultHelper::json(200, '注销成功');
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
        if (in_array($action, ['delete'])) {
            throw new \yii\web\BadRequestHttpException('权限不足');
        }
    }

    /**
     * @param $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (empty($id) || !($model = Member::find()->where(['id' => Yii::$app->user->identity->member_id])->one())) {
            throw new NotFoundHttpException('请求的数据不存在或您的权限不足.');
        }

        return $model;
    }
}
