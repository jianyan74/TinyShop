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
use addons\TinyShop\common\components\purchase\PresellBuyPurchase;
use addons\TinyShop\common\components\purchase\CartPurchase;

/**
 * Interface InitOrderDataInterface
 * @package addons\TinyShop\common\components\purchase
 */
abstract class InitOrderDataInterface
{
    /**
     * 触发的自带营销规则记录
     *
     * 例子
     *
     * '''
     * [
     *    [
     *        'sku_id' => 1, // 触发的sku id
     *        'type' => '', // 触发类型
     *        'data' => [], // 触发的具体内容
     *    ]
     * ]
     * '''
     *
     * @var array
     */
    public $rule = [];

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
    public function afterExecute(PreviewForm $previewForm, string $type, $create = false): PreviewForm
    {
        // 重组默认商品信息
        $defaultProducts = ArrayHelper::arrayKey($previewForm->defaultProducts, 'id');
        $orderProducts = $previewForm->orderProducts;
        $previewForm->product_money = 0;

        /** @var OrderProduct $orderProduct */
        foreach ($orderProducts as $orderProduct) {
            $defaultProduct = $defaultProducts[$orderProduct->product_id];

            // 创建订单校验
            if ($create == true) {
                // 校验下单类型
                if (PointExchangeTypeEnum::isIntegralBuy($orderProduct->point_exchange_type) && $type != PresellBuyPurchase::getType()) {
                    throw new UnprocessableEntityHttpException($orderProduct->product_name . '只能使用积分下单类型');
                }

                // 限购
                $myMaxBuy = $defaultProduct['myGet']['all_num'] ?? 0;
                if ($defaultProduct['max_buy'] > 0 && (($myMaxBuy + $orderProduct->num) > $defaultProduct['max_buy'])) {
                    throw new UnprocessableEntityHttpException($orderProduct->product_name . ' 最多可购买数量为 ' . $defaultProduct['max_buy']);
                }
            }

            // TODO 满减送

            // 阶梯优惠
            list($total_price, $price, $ladderPreferentialTrigger) = Yii::$app->tinyShopService->productLadderPreferential->getPrice($defaultProduct['ladderPreferential'], $orderProduct->num, $orderProduct->price);
            if ($orderProduct->product_money != $total_price) {
                $orderProduct->product_money = $total_price;
                $orderProduct->price = $price;
                // 记录规则
                $this->setRule($orderProduct->sku_id, ProductMarketingEnum::LADDER_PREFERENTIAL, $ladderPreferentialTrigger);
            }

            // 赠送积分
            $givePoint = $this->giveIntegral($defaultProduct['integral_give_type'], $defaultProduct['give_point'], $orderProduct->product_money);
            if ($givePoint > 0) {
                $orderProduct->give_point = $givePoint * $orderProduct->num;
                $previewForm->give_point += $orderProduct->give_point;
                // 记录规则
                $this->setRule($orderProduct->sku_id, ProductMarketingEnum::GIVE_POINT, $orderProduct->give_point);
            }

            $previewForm->product_money += $orderProduct->product_money;
            unset($productMoney, $ladderPreferentialTrigger, $givePoint);
        }

        unset($defaultProducts);

        return $previewForm;
    }

    /**
     * 写入规则
     *
     * @param $sku_id
     * @param $type
     * @param $data
     */
    protected function setRule($sku_id, $type, $data)
    {
        $this->rule[] = [
            'sku_id' => $sku_id, // 触发的sku id
            'title' => ProductMarketingEnum::getValue($type), // 触发类型说明
            'type' => $type, // 触发类型
            'data' => $data, // 触发的具体内容
        ];
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