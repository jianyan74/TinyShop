<?php

namespace addons\TinyShop\common\components\purchase;

use addons\TinyShop\common\enums\MarketingEnum;
use Yii;
use yii\web\UnprocessableEntityHttpException;
use common\helpers\BcHelper;
use addons\TinyShop\common\forms\PreviewForm;
use addons\TinyShop\common\models\order\OrderProduct;
use addons\TinyShop\common\components\InitOrderDataInterface;

/**
 * 购物车下单
 *
 * Class CartPurchase
 * @package addons\TinyShop\common\components\purchase
 * @author jianyan74 <751393839@qq.com>
 */
class CartPurchase extends InitOrderDataInterface
{
    /**
     * @param PreviewForm $form
     * @return PreviewForm|mixed
     * @throws \yii\web\NotFoundHttpException
     * @throws \yii\web\UnprocessableEntityHttpException
     */
    public function execute(PreviewForm $form): PreviewForm
    {
        $carts = Yii::$app->tinyShopService->memberCartItem->findByIds($form->data, $form->buyer_id, true);
        if (count($form->data) != count($carts)) {
            throw new UnprocessableEntityHttpException('找不到商品信息');
        }

        foreach ($carts as $model) {
            // 超值换购
            $plusBuyId = $model['marketing_id'];
            if ($plusBuyId > 0 && $model['marketing_type'] == MarketingEnum::PLUS_BUY) {
                if (!isset($form->plus_buy[$plusBuyId])) {
                    $form->plus_buy[$plusBuyId] = [
                        'marketing_id' => $plusBuyId,
                        'number' => 0,
                        'price' => 0,
                        'product_ids' => [],
                        'carts' => [],
                    ];
                }

                $form->plus_buy[$plusBuyId]['number'] += $model['number'];
                $form->plus_buy[$plusBuyId]['price'] += $model['number'] * $model['sku']['price'];
                $form->plus_buy[$plusBuyId]['product_ids'][] = $model['product_id'];
                $form->plus_buy[$plusBuyId]['carts'][] = $model;
                continue;
            }

            $orderProduct = new OrderProduct();
            $orderProduct = $orderProduct->loadDefaultValues();
            $orderProduct->merchant_id = $model['product']['merchant_id'];
            $orderProduct->product_id = $model['product_id'];
            $orderProduct->product_name = $model['product']['name'];
            $orderProduct->sku_id = $model['sku_id'];
            $orderProduct->sku_name = $model['sku']['name'];
            $orderProduct->num = $model['number'];
            $orderProduct->cost_price = BcHelper::mul($orderProduct->num, $model['sku']['cost_price']);
            $orderProduct->price = $model['sku']['price'];
            $orderProduct->product_money = BcHelper::mul($orderProduct->num, $orderProduct->price);
            $orderProduct->product_original_money = $orderProduct->product_money;
            $orderProduct->product_picture = !empty($model['sku']['picture']) ? $model['sku']['picture'] : $model['product']['picture'];
            $orderProduct->product_type = $model['product']['type'];
            $orderProduct->stock_deduction_type = $model['product']['stock_deduction_type'];
            $orderProduct->buyer_id = $model['member_id'];
            $orderProduct->point_exchange_type = $model['product']['point_exchange_type'];
            $orderProduct->supplier_id = $model['product']['supplier_id'];
            $orderProduct->is_commission = $model['product']['is_commission'];

            // 默认数据带购物车数量方便计算
            $product = $model['product'];
            $product['number'] = $model['number'];

            // 修改总订单
            $form->product_count += $model['number'];
            $form->merchant_id = $orderProduct->merchant_id;
            $form->product_money = BcHelper::add($form->product_money, $orderProduct->product_money);
            $form->product_original_money = BcHelper::add($form->product_original_money, $orderProduct->product_money);
            $form->product_type = $orderProduct->product_type;
            $form->max_use_point += $model['product']['max_use_point'] * $orderProduct->num; // 最多抵现积分
            $form->defaultProducts[] = $product;
            $form->orderProducts[] = $orderProduct;
            $form->sku[] = $model['sku'];
            empty($form->merchant) && $form->merchant = $model['merchant'];

            unset($product);
        }

        return $form;
    }

    /**
     * 下单类型
     *
     * @return string
     */
    public static function getType(): string
    {
        return 'cart';
    }
}
