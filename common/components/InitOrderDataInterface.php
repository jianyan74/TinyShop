<?php

namespace addons\TinyShop\common\components;

use Yii;
use yii\web\UnprocessableEntityHttpException;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use common\helpers\BcHelper;
use addons\TinyShop\common\models\order\OrderProduct;
use addons\TinyShop\common\models\forms\PreviewForm;
use addons\TinyShop\common\enums\ProductMarketingEnum;
use addons\TinyShop\common\enums\PointExchangeTypeEnum;
use addons\TinyShop\common\traits\CalculatePriceTrait;
use addons\TinyShop\common\components\purchase\PointExchangePurchase;

/**
 * Interface InitOrderDataInterface
 * @package addons\TinyShop\common\components\purchase
 */
abstract class InitOrderDataInterface
{
    use CalculatePriceTrait;

    /**
     * 创建记录
     *
     * @var bool
     */
    public $isNewRecord = false;

    /**
     * 执行
     *
     * @param PreviewForm $form
     * @return mixed
     */
    abstract public function execute(PreviewForm $form): PreviewForm;

    /**
     * 下单类型
     *
     * @return string
     */
    abstract public static function getType(): string;

    /**
     * 触发商品自带营销
     *
     * 例如：会员折扣、限时折扣、阶梯优惠等
     *
     * @param PreviewForm $previewForm
     * @param string $type 下单类型
     * @param bool $create 是否创建订单
     * @return PreviewForm
     * @throws UnprocessableEntityHttpException
     */
    public function afterExecute(PreviewForm $previewForm, string $type): PreviewForm
    {
        // 重组默认商品信息
        $defaultProducts = ArrayHelper::arrayKey($previewForm->defaultProducts, 'id');
        $orderProducts = $previewForm->orderProducts;

        $groupOrderProducts = [];
        /** @var OrderProduct $item 产品重新归类组别 */
        foreach ($orderProducts as $item) {
            if (!isset($groupOrderProducts[$item->product_id])) {
                $groupOrderProducts[$item->product_id] = [];
                $groupOrderProducts[$item->product_id]['product_money'] = 0;
                $groupOrderProducts[$item->product_id]['count'] = 0;
                $groupOrderProducts[$item->product_id]['name'] = $item->product_name;
                $groupOrderProducts[$item->product_id]['products'] = [];
            }

            $groupOrderProducts[$item->product_id]['product_money'] += $item->product_money;
            $groupOrderProducts[$item->product_id]['count'] += $item->num;

            $groupOrderProducts[$item->product_id]['products'][] = $item;
        }

        // 写入组别
        $previewForm->groupOrderProducts = $groupOrderProducts;
        // 重新计算价格
        $previewForm = $this->calculatePrice($previewForm);

        foreach ($groupOrderProducts as $product_id => &$groupOrderProduct) {
            $defaultProduct = $defaultProducts[$product_id];
            // 创建订单校验
            if ($this->isNewRecord == true) {
                // 限购
                $myMaxBuy = $defaultProduct['myGet']['all_num'] ?? 0;
                if ($defaultProduct['max_buy'] > 0 && (($myMaxBuy + $groupOrderProduct['count']) > $defaultProduct['max_buy'])) {
                    throw new UnprocessableEntityHttpException($groupOrderProduct['product_name'] . ' 最多可购买数量为 ' . $defaultProduct['max_buy']);
                }
            }

            // 非积分和预售下单触发
            if (!in_array($type, [PointExchangePurchase::getType()])) {
                //-------------------------- 会员折扣 -------------------------- //
                if ($previewForm->member->current_level > 0) {
                    $memberDiscount = Yii::$app->tinyShopService->productMemberDiscount->findByProductIdAndLevel($product_id, $previewForm->member->current_level);
                    if ($memberDiscount && !empty($memberDiscount['memberLevel'])) {
                        $price = $groupOrderProduct['product_money'] * $memberDiscount['memberLevel']['discount'];
                        $price = BcHelper::div($price, 100);

                        $previewForm->marketingDetails[] = [
                            'marketing_id' => $memberDiscount['id'],
                            'marketing_type' => ProductMarketingEnum::MEMBER_DISCOUNT,
                            'marketing_condition' => '会员等级' . $memberDiscount['memberLevel']['name'] . '，折扣减' . $price,
                            'discount_money' => $price,
                            'product_id' => $product_id,
                        ];
                    }
                }

                //-------------------------- 阶梯优惠 -------------------------- //

                $ladderPreferential = Yii::$app->tinyShopService->productLadderPreferential->getPrice($defaultProduct['ladderPreferential'], $groupOrderProduct['count']);
                if ($ladderPreferential) {
                    $previewForm->marketingDetails[] = [
                        'marketing_id' => $ladderPreferential['id'],
                        'marketing_type' => ProductMarketingEnum::LADDER_PREFERENTIAL,
                        'marketing_condition' => '满' . $ladderPreferential['quantity'] . '件，每件减' . $ladderPreferential['price'],
                        'discount_money' => $ladderPreferential['price'] * $groupOrderProduct['count'],
                        'product_id' => $product_id,
                    ];
                }
            }

            /** @var OrderProduct $orderProduct */
            foreach ($groupOrderProduct['products'] as $orderProduct) {
                // 创建订单校验
                if ($this->isNewRecord == true) {
                    // 校验下单类型
                    if (PointExchangeTypeEnum::isIntegralBuy($orderProduct->point_exchange_type) && $type != PointExchangePurchase::getType()) {
                        throw new UnprocessableEntityHttpException($orderProduct->product_name . '只能使用积分下单类型');
                    }
                }

                // 赠送积分
                $givePoint = $this->giveIntegral($defaultProduct['integral_give_type'], $defaultProduct['give_point'], $orderProduct->product_money);
                if ($givePoint > 0) {
                    $allGivePoint = $givePoint * $orderProduct->num;
                    // 记录规则
                    $previewForm->marketingDetails[] = [
                        'marketing_id' => $orderProduct->product_id,
                        'marketing_type' => ProductMarketingEnum::GIVE_POINT,
                        'marketing_condition' => $groupOrderProduct['name'] . '赠送' . $allGivePoint . '积分',
                        'product_id' => $orderProduct->product_id,
                        'sku_id' => $orderProduct->sku_id,
                        'give_point' => $allGivePoint,
                    ];
                }
            }
        }

        return $previewForm;
    }

    /**
     * 赠送积分
     *
     * @param $type
     * @param $point
     * @param $money
     * @return float|int
     */
    protected function giveIntegral($type, $point, $money)
    {
        if ($point > 0) {
            // 百分比换算
            if ($type == StatusEnum::ENABLED) {
                return round(($point / 100) * $money);
            }

            return $point;
        }

        return 0;
    }
}