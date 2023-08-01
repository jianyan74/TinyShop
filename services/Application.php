<?php

namespace addons\TinyShop\services;

use common\components\Service;

/**
 * Class Application
 *
 * @package addons\TinyShop\services
 *
 * 公用
 * @property \addons\TinyShop\services\ConfigService $config 配置
 * @property \addons\TinyShop\services\common\CollectService $collect 收藏
 * @property \addons\TinyShop\services\common\TransmitService $transmit 分享
 * @property \addons\TinyShop\services\common\NiceService $nice 点赞
 * @property \addons\TinyShop\services\common\HelperService $helper 站点帮助
 * @property \addons\TinyShop\services\common\AdvService $adv 广告
 * @property \addons\TinyShop\services\common\NavService $nav 导航
 * @property \addons\TinyShop\services\common\NotifyService $notify 通知
 * @property \addons\TinyShop\services\common\NotifyMemberService $notifyMember 用户通知
 * @property \addons\TinyShop\services\common\NotifyAnnounceService $notifyAnnounce 公告
 * @property \addons\TinyShop\services\common\NotifySubscriptionConfigService $notifySubscriptionConfig 通知配置
 * @property \addons\TinyShop\services\common\SearchHistoryService $searchHistory 搜索
 * @property \addons\TinyShop\services\common\ProductServiceMapService $productServiceMap 商品服务
 * @property \addons\TinyShop\services\common\ExpressCompanyService $expressCompany 快递物流
 * @property \addons\TinyShop\services\common\ExpressFeeService $expressFee 物流模版
 * @property \addons\TinyShop\services\common\SpecService $spec 系统规格
 * @property \addons\TinyShop\services\common\SpecTemplateService $specTemplate 系统规格模板
 * @property \addons\TinyShop\services\common\SpecValueService $specValue 系统规格值
 * @property \addons\TinyShop\services\common\SupplierService $supplier 供货商
 * @property \addons\TinyShop\services\common\AttributeService $attribute 系统商品类型
 * @property \addons\TinyShop\services\common\AttributeValueService $attributeValue 系统商品类型属性
 * @property \addons\TinyShop\services\common\CashAgainstAreaService $cashAgainstArea 货到付款地区
 * @property \addons\TinyShop\services\common\LocalAreaService $localArea 同城配送地区
 * @property \addons\TinyShop\services\common\LocalConfigService $localConfig 同城配送配置
 *
 * 商品
 * @property \addons\TinyShop\services\product\ProductService $product 商品
 * @property \addons\TinyShop\services\product\CateService $productCate 商品分类
 * @property \addons\TinyShop\services\product\CateMapService $productCateMap 商品分类映射
 * @property \addons\TinyShop\services\product\BrandService $productBrand 商品品牌
 * @property \addons\TinyShop\services\product\TagService $productTag 商品标签
 * @property \addons\TinyShop\services\product\SpecService $productSpec 商品规格
 * @property \addons\TinyShop\services\product\SpecValueService $productSpecValue 商品规格值
 * @property \addons\TinyShop\services\product\AttributeValueService $productAttributeValue 商品属性
 * @property \addons\TinyShop\services\product\SkuService $productSku 商品sku
 * @property \addons\TinyShop\services\product\EvaluateService $productEvaluate 商品评价
 * @property \addons\TinyShop\services\product\EvaluateStatService $productEvaluateStat 商品评价统计
 *
 * 用户
 * @property \addons\TinyShop\services\member\MemberService $member 用户
 * @property \addons\TinyShop\services\member\FootprintService $memberFootprint 足迹
 * @property \addons\TinyShop\services\member\CartItemService $memberCartItem 购物车
 *
 * 订单
 * @property \addons\TinyShop\services\order\OrderService $order 订单

 * @property \addons\TinyShop\services\order\OrderBatchService $orderBatch 订单批量操作

 * @property \addons\TinyShop\services\order\InvoiceService $orderInvoice 订单发票记录
 * @property \addons\TinyShop\services\order\ProductService $orderProduct 订单商品
 * @property \addons\TinyShop\services\order\ProductExpressService $orderProductExpress 订单发货记录
 * @property \addons\TinyShop\services\order\StoreService $orderStore 订单自提
 * @property \addons\TinyShop\services\order\RechargeService $orderRecharge 充值订单
 * @property \addons\TinyShop\services\order\MarketingDetailService $orderMarketingDetail 营销记录
 *
 * 营销
 * @property \addons\TinyShop\services\marketing\MarketingService $marketing 营销
 * @property \addons\TinyShop\services\marketing\MarketingStatService $marketingStat 营销统计
 * @property \addons\TinyShop\services\marketing\MarketingCateService $marketingCate 营销关联分类
 * @property \addons\TinyShop\services\marketing\MarketingProductService $marketingProduct 营销商品
 * @property \addons\TinyShop\services\marketing\MarketingProductSkuService $marketingProductSku 营销商品规格
 * @property \addons\TinyShop\services\marketing\PointConfigService $marketingPointConfig 积分抵扣
 * @property \addons\TinyShop\services\marketing\FullMailService $marketingFullMail 营销包邮
 * @property \addons\TinyShop\services\marketing\CouponService $marketingCoupon 优惠券
 * @property \addons\TinyShop\services\marketing\CouponTypeService $marketingCouponType 优惠券类型
 * @property \addons\TinyShop\services\marketing\CouponTypeMapService $marketingCouponTypeMap 优惠券关联
 * @property \addons\TinyShop\services\marketing\RechargeConfigService $marketingRechargeConfig 充值套餐
 *
 * @author jianyan74 <751393839@qq.com>
 */
class Application extends Service
{
    /**
     * @var array
     */
    public $childService = [
        // ------------------------ 商品 ------------------------ //
        'product' => 'addons\TinyShop\services\product\ProductService',
        'productCate' => 'addons\TinyShop\services\product\CateService',
        'productCateMap' => 'addons\TinyShop\services\product\CateMapService',
        'productBrand' => 'addons\TinyShop\services\product\BrandService',
        'productTag' => 'addons\TinyShop\services\product\TagService',
        'productSku' => 'addons\TinyShop\services\product\SkuService',
        'productSpec' => 'addons\TinyShop\services\product\SpecService',
        'productSpecValue' => 'addons\TinyShop\services\product\SpecValueService',
        'productAttributeValue' => 'addons\TinyShop\services\product\AttributeValueService',
        'productEvaluate' => 'addons\TinyShop\services\product\EvaluateService',
        'productEvaluateStat' => 'addons\TinyShop\services\product\EvaluateStatService',
        // ------------------------ 订单 ------------------------ //
        'order' => 'addons\TinyShop\services\order\OrderService',
        'orderStat' => 'addons\TinyShop\services\order\OrderStatService',
        'orderBatch' => 'addons\TinyShop\services\order\OrderBatchService',
        'orderPreview' => 'addons\TinyShop\services\order\PreviewService',
        'orderPreSell' => 'addons\TinyShop\services\order\PreSellService',
        'orderInvoice' => 'addons\TinyShop\services\order\InvoiceService',
        'orderProduct' => 'addons\TinyShop\services\order\ProductService',
        'orderAfterSale' => 'addons\TinyShop\services\order\AfterSaleService',
        'orderProductExpress' => 'addons\TinyShop\services\order\ProductExpressService',
        'orderMarketingDetail' => 'addons\TinyShop\services\order\MarketingDetailService',
        'orderStore' => 'addons\TinyShop\services\order\StoreService',
        'orderRecharge' => 'addons\TinyShop\services\order\RechargeService',

        // ------------------------ 会员 ------------------------ //
        'member' => 'addons\TinyShop\services\member\MemberService',
        'memberFootprint' => 'addons\TinyShop\services\member\FootprintService',
        'memberCartItem' => [
            'class' => 'addons\TinyShop\services\member\CartItemService',
            'drive' => 'mysql',
        ],
        // ------------------------ 营销 ------------------------ //
        'marketing' => 'addons\TinyShop\services\marketing\MarketingService',
        'marketingStat' => 'addons\TinyShop\services\marketing\MarketingStatService',
        'marketingCate' => 'addons\TinyShop\services\marketing\MarketingCateService',
        'marketingProduct' => 'addons\TinyShop\services\marketing\MarketingProductService',
        'marketingProductSku' => 'addons\TinyShop\services\marketing\MarketingProductSkuService',
        'marketingPointConfig' => 'addons\TinyShop\services\marketing\PointConfigService',
        'marketingFullMail' => 'addons\TinyShop\services\marketing\FullMailService',
        'marketingCoupon' => 'addons\TinyShop\services\marketing\CouponService',
        'marketingCouponType' => 'addons\TinyShop\services\marketing\CouponTypeService',
        'marketingCouponTypeMap' => 'addons\TinyShop\services\marketing\CouponTypeMapService',
        'marketingRechargeConfig' => 'addons\TinyShop\services\marketing\RechargeConfigService',
        // ------------------------ 公用 ------------------------ //
        'config' => 'addons\TinyShop\services\ConfigService',
        'spec' => 'addons\TinyShop\services\common\SpecService',
        'specTemplate' => 'addons\TinyShop\services\common\SpecTemplateService',
        'specValue' => 'addons\TinyShop\services\common\SpecValueService',
        'attribute' => 'addons\TinyShop\services\common\AttributeService',
        'attributeValue' => 'addons\TinyShop\services\common\AttributeValueService',
        'supplier' => 'addons\TinyShop\services\common\SupplierService',
        'cashAgainstArea' => 'addons\TinyShop\services\common\CashAgainstAreaService',
        'localArea' => 'addons\TinyShop\services\common\LocalAreaService',
        'localConfig' => 'addons\TinyShop\services\common\LocalConfigService',
        'expressCompany' => 'addons\TinyShop\services\common\ExpressCompanyService',
        'expressFee' => 'addons\TinyShop\services\common\ExpressFeeService',
        'collect' => 'addons\TinyShop\services\common\CollectService',
        'transmit' => 'addons\TinyShop\services\common\TransmitService',
        'nice' => 'addons\TinyShop\services\common\NiceService',
        'helper' => 'addons\TinyShop\services\common\HelperService',
        'adv' => 'addons\TinyShop\services\common\AdvService',
        'nav' => 'addons\TinyShop\services\common\NavService',
        'notify' => 'addons\TinyShop\services\common\NotifyService',
        'notifyAnnounce' => 'addons\TinyShop\services\common\NotifyAnnounceService',
        'notifyMember' => 'addons\TinyShop\services\common\NotifyMemberService',
        'notifySubscriptionConfig' => 'addons\TinyShop\services\common\NotifySubscriptionConfigService',
        'searchHistory' => 'addons\TinyShop\services\common\SearchHistoryService',
        'productServiceMap' => 'addons\TinyShop\services\common\ProductServiceMapService',
    ];
}
