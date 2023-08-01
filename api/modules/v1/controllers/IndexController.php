<?php

namespace addons\TinyShop\api\modules\v1\controllers;

use Yii;
use common\enums\StatusEnum;
use api\controllers\OnAuthController;
use addons\TinyShop\common\forms\ProductSearchForm;
use addons\TinyShop\common\enums\AdvLocalEnum;

/**
 * Class IndexController
 * @package addons\TinyShop\api\modules\v1\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class IndexController extends OnAuthController
{
    /**
     * @var string
     */
    public $modelClass = '';

    /**
     * 不用进行登录验证的方法
     *
     * 例如： ['index', 'update', 'create', 'view', 'delete']
     * 默认全部需要验证
     *
     * @var array
     */
    protected $authOptional = ['index', 'custom', 'preview', 'integral', 'address-to-location', 'location-to-address'];

    /**
     * @return array
     */
    public function actionIndex()
    {
        $setting = Yii::$app->tinyShopService->config->setting();
        $member_id = !Yii::$app->user->isGuest ? Yii::$app->user->identity->member_id : '';

        // 热门
        $productHot = new ProductSearchForm();
        $productHot->is_hot = StatusEnum::ENABLED;
        // 推荐
        $productRecommend = new ProductSearchForm();
        $productRecommend->is_recommend = StatusEnum::ENABLED;
        // 新品
        $productNew = new ProductSearchForm();
        $productNew->is_new = StatusEnum::ENABLED;

        $coupons = empty($member_id) ? [] : Yii::$app->tinyShopService->marketingCoupon->getReadByMemberId($member_id);

        // 猜你喜欢
        $cateIds = Yii::$app->tinyShopService->memberFootprint->findCateIdsByMemberId($member_id);

        $guessYouLike = new ProductSearchForm();
        $guessYouLike->current_level = Yii::$app->tinyShopService->member->getCurrentLevel($member_id);
        $guessYouLike->cate_id = implode(',', $cateIds);

        $productHot->member_id = $member_id;

        return [
            'search' => [
                'hot_search_default' => $setting['hot_search_default'] ?? '', // 默认搜索框内容
                'hot_search_list' => !empty($setting['hot_search_list']) ? explode(',', $setting['hot_search_list']) : [] // 热门搜索
            ],
            'adv' => Yii::$app->tinyShopService->adv->getListByLocals([
                AdvLocalEnum::INDEX_TOP,
                AdvLocalEnum::INDEX_HOT,
                AdvLocalEnum::INDEX_NEW,
                AdvLocalEnum::INDEX_RECOMMEND,
            ]), // 广告
            'popup_adv' => [], // 弹出广告
            'cate' => Yii::$app->tinyShopService->productCate->findByRecommend(), // 首页推荐分类
            'announce' => Yii::$app->tinyShopService->notifyAnnounce->findByCustom(), // 公告
            'product_hot' => Yii::$app->tinyShopService->product->getListBySearch($productHot), // 热门
            'product_recommend' => Yii::$app->tinyShopService->product->getListBySearch($productRecommend), // 推荐
            'product_new' => Yii::$app->tinyShopService->product->getListBySearch($productNew), // 新品
            'guess_you_like' => Yii::$app->tinyShopService->product->getListBySearch($guessYouLike), // 猜你喜欢
            'coupons' => $coupons,
            'copyright' => [
                'web_site_icp' => $setting['web_site_icp'] ?? '',
                'copyright_company_name' => $setting['copyright_company_name'] ?? '',
                'copyright_url' => $setting['copyright_url'] ?? '',
                'copyright_desc' => $setting['copyright_desc'] ?? '',
            ],
            'share' => [
                'share_title' => $setting['share_title'],
                'share_cover' => $setting['share_cover'],
                'share_desc' => $setting['share_desc'],
                'share_link' => $setting['share_link'],
            ],
        ];
    }

    /**
     * 地址转经纬度
     *
     * @param $address
     * @return bool|false|string[]
     * @throws \Exception
     */
    public function actionAddressToLocation($address)
    {
        return Yii::$app->services->extendMap->aMapAddressToLocation($address);
    }

    /**
     * 经纬度转地址
     *
     * @param $location
     * @return array|bool|mixed
     * @throws \Exception
     */
    public function actionLocationToAddress($location)
    {
        return Yii::$app->services->extendMap->aMapLocationToAddress($location);
    }
}
