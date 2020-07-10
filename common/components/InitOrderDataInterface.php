<?php

namespace addons\TinyShop\common\components;

use yii\web\UnprocessableEntityHttpException;
use common\helpers\ArrayHelper;
use addons\TinyShop\common\models\order\OrderProduct;
use addons\TinyShop\common\models\forms\PreviewForm;
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
                $groupOrderProducts[$item->product_id]['max_use_point'] = 0;
                $groupOrderProducts[$item->product_id]['name'] = $item->product_name;
                $groupOrderProducts[$item->product_id]['products'] = [];
            }

            $groupOrderProducts[$item->product_id]['product_money'] += $item->product_money;
            $groupOrderProducts[$item->product_id]['max_use_point'] += $defaultProducts[$item->product_id]['max_use_point'] * $item->num;
            $groupOrderProducts[$item->product_id]['count'] += $item->num;
            $groupOrderProducts[$item->product_id]['products'][] = $item;
        }

        // 写入组别
        $previewForm->groupOrderProducts = $groupOrderProducts;
        // 重新计算价格
        $previewForm = $this->calculatePrice($previewForm);
        // 重新获取组别
        $groupOrderProducts = $previewForm->groupOrderProducts;

        foreach ($groupOrderProducts as $product_id => &$groupOrderProduct) {
            $defaultProduct = $defaultProducts[$product_id];
            // 创建订单校验
            if ($this->isNewRecord == true) {
                // 最少购买
                if ($defaultProduct['min_buy'] > 0 && $groupOrderProduct['count'] < $defaultProduct['min_buy']) {
                    throw new UnprocessableEntityHttpException($groupOrderProduct['name'] . ' 最少购买数量为 ' . $defaultProduct['min_buy']);
                }

                // 限购
                $myMaxBuy = $defaultProduct['myGet']['all_num'] ?? 0;
                if ($defaultProduct['max_buy'] > 0 && (($myMaxBuy + $groupOrderProduct['count']) > $defaultProduct['max_buy'])) {
                    throw new UnprocessableEntityHttpException($groupOrderProduct['name'] . ' 最多可购买数量为 ' . $defaultProduct['max_buy']);
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
            }
        }

        return $previewForm;
    }
}