<?php

namespace addons\TinyShop\api\modules\v1\controllers\product;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use common\helpers\ResultHelper;
use common\enums\StatusEnum;
use common\helpers\AddonHelper;
use addons\TinyShop\common\models\product\Product;
use api\controllers\OnAuthController;
use addons\TinyShop\common\models\forms\ProductSearch;
use addons\TinyShop\common\enums\PointExchangeTypeEnum;
use addons\TinyShop\common\models\SettingForm;

/**
 * 产品
 *
 * Class ProductController
 * @package addons\TinyShop\api\modules\v1\controllers\product
 * @author jianyan74 <751393839@qq.com>
 */
class ProductController extends OnAuthController
{
    /**
     * @var Product
     */
    public $modelClass = Product::class;

    /**
     * 不用进行登录验证的方法
     * 例如： ['index', 'update', 'create', 'view', 'delete']
     * 默认全部需要验证
     *
     * @var array
     */
    protected $authOptional = ['index', 'view', 'guess-you-like'];

    /**
     * @return array|ActiveDataProvider|\yii\db\ActiveRecord[]
     */
    public function actionIndex()
    {
        $model = new ProductSearch();
        $model->attributes = Yii::$app->request->get();

        return Yii::$app->tinyShopService->product->getListBySearch($model);
    }

    /**
     * @param $id
     * @return array|null|\yii\db\ActiveRecord
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionView($id)
    {
        // 判断用户
        $member_id = !Yii::$app->user->isGuest ? Yii::$app->user->identity->member_id : '';

        $model = Yii::$app->tinyShopService->product->findViewById($id, $member_id);
        if (!$model) {
            return ResultHelper::json(422, '产品找不到了或者已下架');
        }

        // 销量
        $model['sales'] = $model['sales'] + $model['real_sales'];
        unset($model['real_sales']);
        // 标签
        $model['tags'] = !empty($model['tags']) ? explode(',', $model['tags']) : [];
        // 浏览量 + 1
        Product::updateAllCounters(['view' => 1], ['id' => $id]);
        // 足迹
        !empty($member_id) && Yii::$app->tinyShopService->memberFootprint->create($model, $member_id);
        // 评论
        if (!empty($model['evaluate'])) {
            foreach ($model['evaluate'] as &$datum) {
                empty($datum['again_covers']) && $datum['again_covers'] = [];
                !is_array($datum['again_covers']) && $datum['again_covers'] = Json::decode($datum['again_covers']);
                empty($datum['covers']) && $datum['covers'] = [];
                !is_array($datum['covers']) && $datum['covers'] = Json::decode($datum['covers']);
                // 匿名
                if ($datum['is_anonymous'] == StatusEnum::ENABLED) {
                    $datum['member_id'] = '';
                    $datum['member_nickname'] = '';
                    $datum['member_head_portrait'] = '';
                }
            }
        }

        // 查询开启分销 (非积分兑换才可以)
        $model['commissionRate'] = [];
        $setting = new SettingForm();
        $setting->attributes = AddonHelper::getConfig();
        if (
            $setting->is_open_commission == StatusEnum::ENABLED &&
            !PointExchangeTypeEnum::isIntegralBuy($model['point_exchange_type']) &&
            $model['is_open_commission'] == StatusEnum::ENABLED
        ) {
            $model['commissionRate'] = Yii::$app->tinyShopService->productCommissionRate->findByProductId($model['id']);
        }

        // 营销
        $model['marketing'] = [];
        if (!empty($model['marketing_id']) && !empty($model['marketing_type'])) {
            $model['marketing'] = Yii::$app->tinyShopService->marketing->findByIdAndType($model);
        }

        // 可领优惠券
        $canReceiveCoupon = Yii::$app->tinyShopService->marketingCouponType->getCanReceiveCouponByProductId($id, $member_id, $model['merchant_id']);
        foreach ($canReceiveCoupon as &$item) {
            $item = Yii::$app->tinyShopService->marketingCouponType->regroupShow($item);
        }

        $model['canReceiveCoupon'] = $canReceiveCoupon;

        // 反转阶梯优惠，因为默认是递减
        $model['ladderPreferential'] = array_reverse($model['ladderPreferential']);

        // 限购判断：是否可购买
        $model['is_buy'] = StatusEnum::ENABLED;
        $model['purchased'] = 0;
        if ($model['max_buy'] > 0 && !empty($member_id)) {
            $model['purchased'] = Yii::$app->tinyShopService->orderProduct->getSumByMember($id, $member_id);

            if ($model['purchased'] >= $model['max_buy']) {
                $model['is_buy'] = StatusEnum::DISABLED;
            }
        }

        // 积分抵现
        $model['pointConfig'] = Yii::$app->tinyShopService->marketingPointConfig->findOne($model['merchant_id']);
        // 满包邮
        $model['fullMail'] = Yii::$app->tinyShopService->marketingFullMail->findOne($model['merchant_id']);
        $model['fullGive'] = [];
        $model['combination'] = [];

        return $model;
    }

    /**
     * 猜你喜欢
     *
     * @return mixed|\yii\db\ActiveRecord
     */
    public function actionGuessYouLike()
    {
        $member_id = !Yii::$app->user->isGuest ? Yii::$app->user->identity->member_id : '';

        return Yii::$app->tinyShopService->product->getGuessYouLike($member_id);
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
        if (in_array($action, ['delete', 'update', 'create'])) {
            throw new \yii\web\BadRequestHttpException('权限不足');
        }
    }
}