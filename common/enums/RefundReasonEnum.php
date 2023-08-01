<?php

namespace addons\TinyShop\common\enums;

use common\enums\BaseEnum;

/**
 * 退款原因
 *
 * Class RefundReasonEnum
 * @package addons\TinyShop\common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class RefundReasonEnum extends BaseEnum
{
    const INCORRECT_SELECTION = 1; // 拍错/多拍
    const NO_LONGER_WANT = 2; // 不想要了
    const NO_EXPRESS_INFO = 3; // 无快递信息
    const EMPTY_PACKAGE = 4; // 包裹为空
    const REJECT_RECEIVE_PACKAGE = 5; // 已拒签包裹
    const NOT_DELIVERED_TOO_LONG = 6; // 快递长时间未送达
    const NOT_MATCH_PRODUCT_DESC = 7; // 与商品描述不符
    const QUALITY_ISSUE = 8; // 质量问题
    const SEND_WRONG_GOODS = 9; // 卖家发错货
    const THREE_NO_PRODUCT = 10; // 三无产品
    const FAKE_PRODUCT = 11; // 假冒产品
    const OTHERS = 12; // 其它

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::INCORRECT_SELECTION => '拍错/多拍',
            self::NO_LONGER_WANT => '不想要了',
            self::NO_EXPRESS_INFO => '无快递信息',
            self::EMPTY_PACKAGE => '包裹为空',
            self::REJECT_RECEIVE_PACKAGE => '已拒签包裹',
            self::NOT_DELIVERED_TOO_LONG => '快递长时间未送达',
            self::NOT_MATCH_PRODUCT_DESC => '与商品描述不符',
            self::QUALITY_ISSUE => '质量问题',
            self::SEND_WRONG_GOODS => '卖家发错货',
            self::THREE_NO_PRODUCT => '三无产品',
            self::FAKE_PRODUCT => '假冒产品',
            self::OTHERS => '其他',
        ];
    }
}
