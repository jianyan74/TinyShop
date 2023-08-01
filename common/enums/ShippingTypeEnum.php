<?php

namespace addons\TinyShop\common\enums;

use common\enums\BaseEnum;

/**
 * 配送类型
 *
 * Class ShippingTypeEnum
 * @package addons\TinyShop\common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class ShippingTypeEnum extends BaseEnum
{
    const LOGISTICS = 1;
    const LOCAL_DISTRIBUTION = 2;
    // 无需物流
    const PICKUP = 100;
    const TO_STORE = 101;
    const VIRTUAL = 201;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::LOGISTICS => '物流配送',
            self::LOCAL_DISTRIBUTION => '同城配送',
            // 无需物流
            self::PICKUP => '买家自提',
            self::TO_STORE => '买家到店',
            self::VIRTUAL => '无需物流', // 虚拟商品
        ];
    }

    /**
     * 绑定商品配送类型
     *
     * @return string[]
     */
    public static function getDeliveryType()
    {
        return [
            self::LOGISTICS => '物流配送',
            self::LOCAL_DISTRIBUTION => '同城配送',
            self::PICKUP => '买家自提',
        ];
    }

    /**
     * @param $key
     * @return string
     */
    public static function getExplain($key)
    {
        $list = [
            self::LOGISTICS => '支持物流配送的商品在购买后将会通过快递的方式进行配送，可在订单中查看物流信息',
            self::PICKUP => '支持门店自提的商品在购买后用户可自行到下单时所选择的自提点进行提货',
            self::LOCAL_DISTRIBUTION => '支持同城配送的商品在购买后平台将安排配送人员配送到用户指定的收货地点',
        ];

        return $list[$key] ?? '';
    }
}
