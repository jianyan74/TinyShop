<?php

namespace addons\TinyShop\api\modules\v1\controllers;

use Yii;
use common\helpers\AddonHelper;
use common\enums\StatusEnum;
use api\controllers\OnAuthController;
use addons\TinyShop\common\enums\AdvLocalEnum;
use addons\TinyShop\common\models\forms\ProductSearch;

/**
 * 首页相关
 *
 * Class IndexController
 * @package addons\TinyShop\api\modules\v1\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class IndexController extends OnAuthController
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
    protected $authOptional = ['index'];

    /**
     * @return array|\yii\data\ActiveDataProvider
     */
    public function actionIndex()
    {
        $config = AddonHelper::getConfig();
        $member_id = !Yii::$app->user->isGuest ? Yii::$app->user->identity->member_id : '';

        // 热门
        $product_hot = new ProductSearch();
        $product_hot->is_hot = StatusEnum::ENABLED;
        // 推荐
        $product_recommend = new ProductSearch();
        $product_recommend->is_recommend = StatusEnum::ENABLED;
        // 新品
        $product_new = new ProductSearch();
        $product_new->is_new = StatusEnum::ENABLED;

        return [
            'search' => [
                'hot_search_default' => $config['hot_search_default'] ?? '',
                // 默认搜索框内容
                'hot_search_list' => !empty($config['hot_search_list']) ? explode(',', $config['hot_search_list']) : []
                // 热门搜索
            ],
            'adv' => Yii::$app->tinyShopService->adv->getListByLocals([
                AdvLocalEnum::INDEX_TOP,
                AdvLocalEnum::INDEX_HOT,
                AdvLocalEnum::INDEX_NEW,
                AdvLocalEnum::INDEX_RECOMMEND
            ]), // 广告
            'cate' => Yii::$app->tinyShopService->productCate->findIndexBlock(), // 首页推荐分类
            'product_hot' => Yii::$app->tinyShopService->product->getListBySearch($product_hot), // 热门
            'product_recommend' => Yii::$app->tinyShopService->product->getListBySearch($product_recommend), // 推荐
            'product_new' => Yii::$app->tinyShopService->product->getListBySearch($product_new), // 新品
            'guess_you_like' => Yii::$app->tinyShopService->product->getGuessYouLike($member_id), // 猜你喜欢
            'config' => [
                'web_site_icp' => $config['web_site_icp'] ?? '',
                'copyright_companyname' => $config['copyright_companyname'] ?? '',
                'copyright_url' => $config['copyright_url'] ?? '',
                'copyright_desc' => $config['copyright_desc'] ?? '',
            ]
        ];
    }
}