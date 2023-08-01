<?php

namespace addons\TinyShop\api\modules\v1\controllers\product;

use common\helpers\BcHelper;
use Yii;
use yii\helpers\Json;
use yii\data\ActiveDataProvider;
use common\enums\StatusEnum;
use common\helpers\ResultHelper;
use api\controllers\OnAuthController;
use addons\TinyShop\common\models\product\Product;
use addons\TinyShop\common\forms\ProductSearchForm;
use addons\TinyShop\common\enums\MarketingEnum;
use addons\TinyShop\common\enums\ProductShippingTypeEnum;
use addons\TinyShop\common\enums\ShippingTypeEnum;

/**
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
    protected $authOptional = ['index', 'list', 'view', 'view', 'view-by-base', 'excellent', 'guess-you-like'];

    /**
     * @return array|ActiveDataProvider|\yii\db\ActiveRecord[]
     */
    public function actionIndex()
    {
        // 判断用户
        $member_id = !Yii::$app->user->isGuest ? Yii::$app->user->identity->member_id : '';

        $model = new ProductSearchForm();
        $model->attributes = Yii::$app->request->get();
        $model->member_id = $member_id;
        $model->current_level = Yii::$app->tinyShopService->member->getCurrentLevel($member_id);

        return Yii::$app->tinyShopService->product->getListBySearch($model);
    }

    /**
     * @param $id
     * @return array|null|\yii\db\ActiveRecord
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionView($id)
    {
        // 强制不获取营销信息
        $not_marketing = Yii::$app->request->get('not_marketing', StatusEnum::DISABLED);
        $marketing_id = Yii::$app->request->get('marketing_id');
        $marketing_type = Yii::$app->request->get('marketing_type');
        $marketing_product_id = Yii::$app->request->get('marketing_product_id');
        // 判断用户
        $member_id = !Yii::$app->user->isGuest ? Yii::$app->user->identity->member_id : '';

        $model = Yii::$app->tinyShopService->product->findViewById($id, $member_id);
        if (!$model) {
            return ResultHelper::json(422, '商品找不到了');
        }

        // 销量
        unset($model['real_sales'], $model['sales'], $model['cost_price']);
        // 品牌
        $model['brand'] = $model['brand_id'] > 0 ? Yii::$app->tinyShopService->productBrand->findById($model['brand_id']) : [];
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

        // 营销
        $model['marketing'] = [];
        $model['marketing_tags'] = [];

        // 限购判断
        $model['is_buy'] = StatusEnum::ENABLED;
        $model['purchased'] = 0;
        if ($model['max_buy'] > 0 && !empty($member_id)) {
            $model['purchased'] = Yii::$app->tinyShopService->orderProduct->findSumByMember($id, $member_id);
            $model['purchased'] >= $model['max_buy'] && $model['is_buy'] = StatusEnum::DISABLED;
        }

        // 配送
        if (!empty($model['delivery_type'])) {
            $deliveryType = [];
            foreach ($model['delivery_type'] as $value) {
                ShippingTypeEnum::getValue($value) && $deliveryType[] = [
                    'name' => ShippingTypeEnum::getValue($value),
                    'explain' => ShippingTypeEnum::getExplain($value),
                ];
            }
            $model['delivery_type'] = $deliveryType;
        }

        // 服务
        $model['service'] = Yii::$app->tinyShopService->productServiceMap->findByMerchantId($model['merchant_id']);
        // 微信小程序直播
        $model['live'] = Yii::$app->has('wechatMiniService') ? Yii::$app->wechatMiniService->live->findByCustom() : [];

        // 可领优惠券
        $canReceiveCoupon = Yii::$app->tinyShopService->marketingCouponType->getCanReceiveCouponByProductId($id, $model['cateIds'], $member_id, $model['merchant_id']);
        foreach ($canReceiveCoupon as &$item) {
            $item = Yii::$app->tinyShopService->marketingCouponType->regroupShow($item);
        }
        $model['canReceiveCoupon'] = $canReceiveCoupon;


        // ***************************** 标签 ***************************** //
        $model['is_hot'] == StatusEnum::ENABLED && $model['marketing_tags'][] = '热门';
        $model['is_recommend'] == StatusEnum::ENABLED && $model['marketing_tags'][] = '推荐';
        $model['is_new'] == StatusEnum::ENABLED && $model['marketing_tags'][] = '新品';
        $model['shipping_type'] == ProductShippingTypeEnum::FULL_MAIL && $model['marketing_tags'][] = '包邮';
        // 未包邮判断是否需要满包邮
        if (
            $model['shipping_type'] != ProductShippingTypeEnum::FULL_MAIL &&
            !empty($fullMail = Yii::$app->tinyShopService->marketingFullMail->findOne($model['merchant_id'])) &&
            $fullMail['status'] == StatusEnum::ENABLED &&
            $fullMail['full_mail_money'] > 0
        ) {
            $model['marketing_tags'][] = '满' . $fullMail['full_mail_money'] . '包邮';
        }
        $model['min_buy'] > 1 && $model['marketing_tags'][] = $model['min_buy'] . '件起';
        // $model['max_buy'] > 0 && $model['marketing_tags'][] = '限购' . $model['max_buy'] . '件';
        // 积分抵现
        if (
            !empty($member_id) &&
            $model['marketing_type'] != MarketingEnum::POINT_EXCHANGE &&
            !empty($memberAccount = Yii::$app->services->memberAccount->findByMemberId($member_id)) &&
            $memberAccount['user_integral'] > 0 &&
            !empty($pointMaxConfig = Yii::$app->tinyShopService->marketingPointConfig->getMaxConfig($model['minSkuPrice'], $memberAccount['user_integral']))
        ) {
            $pointTag = $pointMaxConfig['maxPoint'] . '积分可抵' . $pointMaxConfig['maxMoney'] . '元';
            $pointMaxConfig['minOrderMoney'] > 0 && $pointTag = '满' . $pointMaxConfig['minOrderMoney'] . '元' . $pointTag;
            $model['marketing_tags'][] = $pointTag;
        }

        empty($model['marketing_tags']) && $model['marketing_tags'] = ['普通商品'];
        $model['match_ratio'] = floatval(BcHelper::add($model['match_ratio'], 0));
        !empty($model['extend']) && $model['extend'] = Json::decode($model['extend']);

        return $model;
    }

    /**
     * 基础信息
     *
     * @param $id
     * @return array|mixed|\yii\db\ActiveRecord|null
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionViewByBase($id)
    {
        // 强制不获取营销信息
        $not_marketing = Yii::$app->request->get('not_marketing', StatusEnum::DISABLED);
        $marketing_id = Yii::$app->request->get('marketing_id');
        $marketing_type = Yii::$app->request->get('marketing_type');
        $marketing_product_id = Yii::$app->request->get('marketing_product_id');

        $member_id = !Yii::$app->user->isGuest ? Yii::$app->user->identity->member_id : '';
        $model = Yii::$app->tinyShopService->product->findViewById($id, $member_id, ['sku']);

        if (!$model) {
            return ResultHelper::json(422, '商品找不到了');
        }

        // 营销
        $model['marketing'] = [];

        // 限购判断
        $model['is_buy'] = StatusEnum::ENABLED;
        $model['purchased'] = 0;
        if ($model['max_buy'] > 0 && !empty($member_id)) {
            $model['purchased'] = Yii::$app->tinyShopService->orderProduct->findSumByMember($id, $member_id);
            $model['purchased'] >= $model['max_buy'] && $model['is_buy'] = StatusEnum::DISABLED;
        }

        return $model;
    }

    /**
     * 自定义装修可用
     *
     * @return array|ActiveDataProvider|\yii\db\ActiveRecord[]
     */
    public function actionList()
    {
        $model = new ProductSearchForm();
        $model->attributes = Yii::$app->request->get();
        $model->limit = $this->pageSize;

        list($list, $pages) = Yii::$app->tinyShopService->product->getListBySearch($model, true);

        return [
            'list' => $list,
            'pages' => [
                'totalCount' => $pages->totalCount,
                'pageSize' => $pages->pageSize,
            ]
        ];
    }

    /**
     * 猜你喜欢
     *
     * @return mixed|\yii\db\ActiveRecord
     */
    public function actionGuessYouLike()
    {
        $member_id = !Yii::$app->user->isGuest ? Yii::$app->user->identity->member_id : '';
        $cateIds = Yii::$app->tinyShopService->memberFootprint->findCateIdsByMemberId($member_id);

        $model = new ProductSearchForm();
        $model->current_level = Yii::$app->tinyShopService->member->getCurrentLevel($member_id);
        $model->cate_id = implode(',', $cateIds);

        return Yii::$app->tinyShopService->product->getListBySearch($model);
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
