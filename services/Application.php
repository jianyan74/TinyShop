<?php

namespace addons\TinyShop\services;

use common\components\Service;

/**
 * Class Application
 *
 * @package addons\TinyShop\services
 * @property \addons\TinyShop\services\common\CollectService $collect 收藏
 * @property \addons\TinyShop\services\common\TransmitService $transmit 分享
 * @property \addons\TinyShop\services\common\NiceService $nice 点赞
 * @property \addons\TinyShop\services\common\HelperService $helper 站点帮助
 * @property \addons\TinyShop\services\common\AdvService $adv 广告
 * @property \addons\TinyShop\services\express\CompanyService $expressCompany 快递物流
 * @property \addons\TinyShop\services\express\FeeService $expressFee 物流模版
 * @property \addons\TinyShop\services\product\ProductService $product 产品
 * @property \addons\TinyShop\services\product\CateService $productCate 产品分类
 * @property \addons\TinyShop\services\product\BrandService $productBrand 产品品牌
 * @property \addons\TinyShop\services\product\TagService $productTag 产品标签
 * @property \addons\TinyShop\services\product\SpecService $productSpec 产品规格
 * @property \addons\TinyShop\services\product\SpecValueService $productSpecValue 产品规格值
 * @property \addons\TinyShop\services\product\SkuService $productSku 产品sku
 * @property \addons\TinyShop\services\product\EvaluateService $productEvaluate 产品评价
 * @property \addons\TinyShop\services\product\EvaluateStatService $productEvaluateStat 产品评价统计
 * @property \addons\TinyShop\services\product\LadderPreferentialService $productLadderPreferential 产品阶梯优惠
 * @property \addons\TinyShop\services\member\MemberService $member 用户
 * @property \addons\TinyShop\services\member\FootprintService $memberFootprint 足迹
 * @property \addons\TinyShop\services\member\AddressService $memberAddress 收货地址
 * @property \addons\TinyShop\services\member\InvoiceService $memberInvoice 发票
 * @property \addons\TinyShop\services\member\CartItemService $memberCartItem 购物车
 * @property \addons\TinyShop\services\base\SpecService $baseSpec 系统规格
 * @property \addons\TinyShop\services\base\SpecValueService $baseSpecValue 系统规格值
 * @property \addons\TinyShop\services\base\SupplierService $baseSupplier 供货商
 * @property \addons\TinyShop\services\base\AttributeService $baseAttribute 系统产品类型
 * @property \addons\TinyShop\services\base\AttributeValueService $baseAttributeValue 系统产品类型属性
 * @property \addons\TinyShop\services\order\OrderService $order 订单
 * @property \addons\TinyShop\services\order\ActionService $orderAction 订单操作记录
 * @property \addons\TinyShop\services\order\InvoiceService $orderInvoice 订单发票记录
 * @property \addons\TinyShop\services\order\ProductService $orderProduct 订单产品
 * @property \addons\TinyShop\services\order\CustomerService $orderCustomer 订单产品售后
* @property \addons\TinyShop\services\order\ProductExpressService $orderProductExpress 订单发货记录
 * @property \addons\TinyShop\services\order\PickupService $orderPickup 订单自提
 * @property \addons\TinyShop\services\marketing\PointConfigService $marketingPointConfig 营销积分
 * @property \addons\TinyShop\services\marketing\FullMailService $marketingFullMail 营销包邮
 * @property \addons\TinyShop\services\marketing\CouponService $marketingCoupon 优惠券
 * @property \addons\TinyShop\services\marketing\CouponTypeService $marketingCouponType 优惠券类型
 * @property \addons\TinyShop\services\marketing\CouponProductService $marketingCouponProduct 优惠券关联产品
 * @property \addons\TinyShop\services\pickup\PointService $pickupPoint 自提点
 * @property \addons\TinyShop\services\pickup\AuditorService $pickupAuditor 自提审核用户
 *
 * @author jianyan74 <751393839@qq.com>
 */
class Application extends Service
{
    /**
     * @var array
     */
    public $childService = [
        'product' => 'addons\TinyShop\services\product\ProductService',
        'productCate' => 'addons\TinyShop\services\product\CateService',
        'productBrand' => 'addons\TinyShop\services\product\BrandService',
        'productTag' => 'addons\TinyShop\services\product\TagService',
        'productSku' => 'addons\TinyShop\services\product\SkuService',
        'productSpec' => 'addons\TinyShop\services\product\SpecService',
        'productSpecValue' => 'addons\TinyShop\services\product\SpecValueService',
        'productEvaluate' => 'addons\TinyShop\services\product\EvaluateService',
        'productEvaluateStat' => 'addons\TinyShop\services\product\EvaluateStatService',
        'productLadderPreferential' => 'addons\TinyShop\services\product\LadderPreferentialService',
        'order' => 'addons\TinyShop\services\order\OrderService',
        'orderAction' => 'addons\TinyShop\services\order\ActionService',
        'orderInvoice' => 'addons\TinyShop\services\order\InvoiceService',
        'orderProduct' => 'addons\TinyShop\services\order\ProductService',
        'orderCustomer' => 'addons\TinyShop\services\order\CustomerService',
        'orderProductExpress' => 'addons\TinyShop\services\order\ProductExpressService',
        'orderPickup' => 'addons\TinyShop\services\order\PickupService',
        'baseSpec' => 'addons\TinyShop\services\base\SpecService',
        'baseSpecValue' => 'addons\TinyShop\services\base\SpecValueService',
        'baseAttribute' => 'addons\TinyShop\services\base\AttributeService',
        'baseSupplier' => 'addons\TinyShop\services\base\SupplierService',
        'baseAttributeValue' => 'addons\TinyShop\services\base\AttributeValueService',
        'expressCompany' => 'addons\TinyShop\services\express\CompanyService',
        'expressFee' => 'addons\TinyShop\services\express\FeeService',
        'member' => 'addons\TinyShop\services\member\MemberService',
        'memberAddress' => 'addons\TinyShop\services\member\AddressService',
        'memberInvoice' => 'addons\TinyShop\services\member\InvoiceService',
        'memberFootprint' => 'addons\TinyShop\services\member\FootprintService',
        'memberCartItem' => [
            'class' => 'addons\TinyShop\services\member\CartItemService',
            'drive' => 'mysql',
        ],
        'marketingPointConfig' => 'addons\TinyShop\services\marketing\PointConfigService',
        'marketingFullMail' => 'addons\TinyShop\services\marketing\FullMailService',
        'marketingCoupon' => 'addons\TinyShop\services\marketing\CouponService',
        'marketingCouponType' => 'addons\TinyShop\services\marketing\CouponTypeService',
        'marketingCouponProduct' => 'addons\TinyShop\services\marketing\CouponProductService',
        'pickupPoint' => 'addons\TinyShop\services\pickup\PointService',
        'pickupAuditor' => 'addons\TinyShop\services\pickup\AuditorService',
        // 公用
        'collect' => 'addons\TinyShop\services\common\CollectService',
        'transmit' => 'addons\TinyShop\services\common\TransmitService',
        'nice' => 'addons\TinyShop\services\common\NiceService',
        'helper' => 'addons\TinyShop\services\common\HelperService',
        'adv' => 'addons\TinyShop\services\common\AdvService',
    ];
}