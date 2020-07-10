<?php

namespace addons\TinyShop\common\components\marketing;

use Yii;
use common\helpers\ArrayHelper;
use common\enums\StatusEnum;
use addons\TinyShop\common\traits\CalculatePriceTrait;
use addons\TinyShop\common\models\order\OrderProduct;
use addons\TinyShop\common\models\forms\PreviewForm;
use addons\TinyShop\common\components\PreviewInterface;
use addons\TinyShop\common\enums\ProductMarketingEnum;
use yii\web\UnprocessableEntityHttpException;

/**
 * 统一处理数据
 *
 * Class AfterHandler
 * @package addons\TinyShop\common\components\marketing
 * @author jianyan74 <751393839@qq.com>
 */
class AfterHandler extends PreviewInterface
{
    use CalculatePriceTrait;

    /**
     * @param PreviewForm $form
     * @return PreviewForm|mixed
     */
    public function execute(PreviewForm $form): PreviewForm
    {
        // 自动计算价格
        $this->calculatePrice($form);

        // 赠送积分
        /** @var OrderProduct $orderProduct */
        $groupOrderProducts = $form->groupOrderProducts;
        $defaultProducts = ArrayHelper::arrayKey($form->defaultProducts, 'id');
        foreach ($groupOrderProducts as $product_id => &$groupOrderProduct) {
            $defaultProduct = $defaultProducts[$product_id];

            /** @var OrderProduct $orderProduct */
            foreach ($groupOrderProduct['products'] as $orderProduct) {
                // 赠送积分
                $givePoint = $this->giveIntegral($defaultProduct['integral_give_type'], $defaultProduct['give_point'], $orderProduct->product_money);
                if ($givePoint > 0) {
                    // 记录规则
                    $form->marketingDetails[] = [
                        'marketing_id' => $orderProduct->product_id,
                        'marketing_type' => ProductMarketingEnum::GIVE_POINT,
                        'marketing_condition' => $groupOrderProduct['name'] . '赠送' . $givePoint . '积分',
                        'product_id' => $orderProduct->product_id,
                        'sku_id' => $orderProduct->sku_id,
                        'give_point' => $givePoint,
                    ];
                }
            }
        }

        $givePoint = ArrayHelper::getColumn($form->marketingDetails, 'give_point');
        $form->give_point = array_sum($givePoint);

        foreach ($form->marketingDetails as &$detail) {
            $detail['marketing_name'] = ProductMarketingEnum::getValue($detail['marketing_type']);
        }

        return $form;
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

    /**
     * 排斥营销
     *
     * @return array
     */
    public function rejectNames()
    {
        return [];
    }

    /**
     * 营销名称
     *
     * @return string
     */
    public static function getName(): string
    {
        return 'give';
    }
}