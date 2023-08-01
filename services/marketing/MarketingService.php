<?php

namespace addons\TinyShop\services\marketing;

use Yii;
use yii\helpers\Json;
use common\helpers\BcHelper;
use common\helpers\ArrayHelper;
use addons\TinyShop\common\enums\MarketingEnum;
use addons\TinyShop\common\enums\DecimalReservationEnum;
use addons\TinyShop\common\enums\DiscountTypeEnum;

/**
 * Class MarketingService
 * @package addons\TinyShop\services\marketing
 * @author jianyan74 <751393839@qq.com>
 */
class MarketingService
{
    /**
     * 重组营销显示列表
     *
     * @param $list
     * @param $isMarketing
     * @return array
     */
    public function regroupList($list, $isMarketing = true)
    {
        $data = [];
        foreach ($list as $item) {
            $product = $item['product'];
            $product['tags'] = Json::decode($product['tags']);
            $product['marketing_type'] = $item['marketing_type'];
            $product['marketing_id'] = $item['marketing_id'];
            $product['marketing_tags'] = $isMarketing == true ? '' : [];
            unset($item['product']);
            $product['marketing'] = ArrayHelper::merge($item, Json::decode($item['marketing_data']));;
            unset($product['marketing']['marketing_data']);
            // 排除打包一口价和第二件半价价格
            if (!in_array($product['marketing']['marketing_type'], [MarketingEnum::SECOND_HALF_DISCOUNT, MarketingEnum::BALE])) {
                $product['marketing']['marketing_price'] = Yii::$app->tinyShopService->marketing->getPrice(
                    $product['marketing']['discount'],
                    $product['marketing']['discount_type'],
                    1,
                    $product['price'],
                    $product['marketing']['decimal_reservation_number']
                );
            } else {
                $product['marketing']['marketing_price'] = $product['price'];
            }

            $product['price'] = floatval($product['price']);
            $product['market_price'] = floatval($product['market_price']);
            $product['marketing']['marketing_price'] = floatval($product['marketing']['marketing_price']);

            $data[] = $product;
            unset($product);
        }

        return $data;
    }

    /**
     * 获取金额
     *
     * @param float $discount 营销内容
     * @param int $discount_type 营销类型
     * @param int $num
     * @param float $total_price
     * @param int $decimal_reservation_number
     * @return int|string|null
     */
    public function getPrice(
        $discount,
        $discount_type,
        $num,
        $total_price,
        $decimal_reservation_number = DecimalReservationEnum::DEFAULT
    ) {
        $newPrice = 0;
        // 营销方式
        switch ($discount_type) {
            // 减钱 (营销类型 * 数量)
            case DiscountTypeEnum::MONEY :
                $discount_money = BcHelper::mul($discount, $num);
                $newPrice = BcHelper::sub($total_price, $discount_money);
                break;
            // 折扣
            case DiscountTypeEnum::DISCOUNT :
                $rate = BcHelper::div($discount, 10, 10);
                $newPrice = BcHelper::mul($total_price, $rate);
                break;
            // 促销价 (营销类型 * 数量)
            case DiscountTypeEnum::FIXATION :
                $newPrice = BcHelper::mul($discount, $num);
                break;
        }

        switch ($decimal_reservation_number) {
            // 抹去分
            case DecimalReservationEnum::CLEAR_DECIMAL_ONE :
                $newPrice = BcHelper::mul($newPrice, 1, 1);
                break;
            // 抹去角和分
            case DecimalReservationEnum::CLEAR_DECIMAL_TWO :
                $newPrice = BcHelper::mul($newPrice, 1, 0);
                break;
        }

        return $newPrice;
    }

    /**
     * 获取营销说明
     *
     * @param $marketing
     * @return array
     */
    public function getTagByCartItem($marketing)
    {
        $marketing['discount'] = floatval($marketing['discount']);
        switch ($marketing['marketing_type']) {
            case MarketingEnum::BALE :
                return [
                    $marketing['discount'] . '元' . $marketing['number'] . '件',
                    '仅需' . $marketing['discount'] . '元,可任选' . $marketing['number'] . '件'
                ];
            case MarketingEnum::SECOND_HALF_DISCOUNT :
                $tag = '第' . $marketing['number'] . '件' . $marketing['discount'] . '折';
                if ($marketing['discount'] == 5) {
                    $tag = '第' . $marketing['number'] . '件' . '半价';
                }

                if ($marketing['number'] == 2 && $marketing['discount'] == 0) {
                    $tag = '买一送一';
                }

                if ($marketing['number'] > 2 && $marketing['discount'] == 0) {
                    $tag = '第' . $marketing['number'] . '件' . '0元';
                }

                return [$tag, '可叠加使用优惠'];
            case MarketingEnum::DISCOUNT :
                $explain = '限时抢低价优惠';
                if ($marketing['discount_type'] == DiscountTypeEnum::MONEY) {
                    $explain = '限时立减' . $marketing['discount'] . '元/件';
                }

                if ($marketing['discount_type'] == DiscountTypeEnum::DISCOUNT) {
                    $explain = '限时打' . $marketing['discount'] . '折';
                }

                return ['限时抢', $explain];
            case MarketingEnum::PRE_SELL :
                return ['预售', ''];
            case MarketingEnum::SEC_KILL :
                return ['秒杀', ''];
            case MarketingEnum::BARGAIN :
                return ['砍价', ''];
            case MarketingEnum::GROUP_BUY :
                return ['团购', '最少购买' . $marketing['min_buy'] . '件可享优惠'];
            case MarketingEnum::WHOLESALE :
                // TODO 拼团返利
                return ['拼团', ''];
            default :
                return ['', ''];
        }
    }

    /**
     * 营销标签
     *
     * @param $marketing
     * @param string $local list:列表显示;marketing_list:营销列表显示;view:详情显示
     * @return array
     */
    public function getTags($marketing, $local = 'list')
    {
        isset($marketing['discount']) && $marketing['discount'] = floatval($marketing['discount']);
        $tags = [];
        switch ($marketing['marketing_type']) {
            case MarketingEnum::BALE :
                $tags[] = $marketing['discount'] . '元' . $marketing['number'] . '件';
                break;
            case MarketingEnum::SECOND_HALF_DISCOUNT :
                $tag = '第' . $marketing['number'] . '件' . $marketing['discount'] . '折';
                if ($marketing['discount'] == 5) {
                    $tag = '第' . $marketing['number'] . '件' . '半价';
                }

                if ($marketing['number'] == 2 && $marketing['discount'] == 0) {
                    $tag = '买一送一';
                }

                if ($marketing['number'] > 2 && $marketing['discount'] == 0) {
                    $tag = '第' . $marketing['number'] . '件' . '0元';
                }

                $tags[] = $tag;
                break;
            case MarketingEnum::DISCOUNT :
                in_array($local, ['list', 'view']) && $tags[] = '限时抢';
                break;
            case MarketingEnum::PRE_SELL :
                $local == 'list' && $tags[] = '预售';
                break;
            case MarketingEnum::SEC_KILL :
                $local == 'list' && $tags[] = '秒杀';
                break;
            case MarketingEnum::BARGAIN :
                $local == 'list' && $tags[] = '砍价';
                break;
            case MarketingEnum::GROUP_BUY :
                $local == 'list' && $tags[] = '团购';
                $local == 'list' && $tags[] = $marketing['min_buy'] . '件起';
                break;
            case MarketingEnum::WHOLESALE :
                in_array($local, ['list', 'view']) && $tags[] = $marketing['number'] . '人团';
                break;
        }

        return $tags;
    }

    /**
     * 合并相同的营销显示
     *
     * @param array $data
     * @return array
     */
    public function mergeIdenticalMarketing($data = [])
    {
        if (empty($data)) {
            return [];
        }

        $marketing = [];
        foreach ($data as $datum) {
            $marketing_type = $datum['marketing_type'];
            $datum['discount_money'] = floatval($datum['discount_money']);

            if (
                !isset($marketing[$marketing_type]) &&
                isset($datum['discount_money']) &&
                $datum['discount_money'] > 0
            ) {
                $marketing[$marketing_type] = [
                    'discount_money' => 0,
                    'marketing_name' => isset($datum['marketing_name']) ? $datum['marketing_name'] : MarketingEnum::getValue($datum['marketing_type']),
                    'marketing_type' => $datum['marketing_type'],
                ];
            }

            if (
                isset($marketing[$marketing_type]) &&
                isset($datum['discount_money']) &&
                $datum['discount_money'] > 0
            ) {
                $marketing[$marketing_type]['discount_money'] = BcHelper::add($marketing[$marketing_type]['discount_money'],
                    $datum['discount_money']);
            }

            // 单独计算积分
            if (
                !isset($marketing[MarketingEnum::GIVE_POINT]) &&
                isset($datum['give_point']) &&
                $datum['give_point'] > 0
            ) {
                $marketing[MarketingEnum::GIVE_POINT] = [
                    'discount_money' => 0,
                    'marketing_name' => MarketingEnum::getValue(MarketingEnum::GIVE_POINT),
                    'marketing_type' => MarketingEnum::GIVE_POINT,
                ];
            }

            if (
                isset($marketing[MarketingEnum::GIVE_POINT]) &&
                isset($datum['give_point']) &&
                $datum['give_point'] > 0
            ) {
                $marketing[MarketingEnum::GIVE_POINT]['discount_money'] = BcHelper::add($marketing[MarketingEnum::GIVE_POINT]['discount_money'],
                    $datum['give_point'], 0);
            }

            // 单独计算成长值
            if (
                !isset($marketing[MarketingEnum::GIVE_GROWTH]) &&
                isset($datum['give_growth']) &&
                $datum['give_growth'] > 0
            ) {
                $marketing[MarketingEnum::GIVE_GROWTH] = [
                    'discount_money' => 0,
                    'marketing_name' => MarketingEnum::getValue(MarketingEnum::GIVE_GROWTH),
                    'marketing_type' => MarketingEnum::GIVE_GROWTH,
                ];
            }

            if (
                isset($marketing[MarketingEnum::GIVE_GROWTH]) &&
                isset($datum['give_growth']) &&
                $datum['give_growth'] > 0
            ) {
                $marketing[MarketingEnum::GIVE_GROWTH]['discount_money'] = BcHelper::add($marketing[MarketingEnum::GIVE_GROWTH]['discount_money'],
                    $datum['give_growth'], 0);
            }

            // 其他显示
            if (in_array($marketing_type, [MarketingEnum::FULL_MAIL])) {
                $marketing[$marketing_type] = [
                    'discount_money' => 0,
                    'marketing_name' => $datum['marketing_condition'] ?? '',
                    'marketing_type' => $datum['marketing_type'],
                ];
            }
        }

        $return = [];
        foreach ($marketing as $value) {
            $return[] = $value;
        }

        return $return;
    }
}
