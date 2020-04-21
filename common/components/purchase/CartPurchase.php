<?php

namespace addons\TinyShop\common\components\purchase;

use Yii;
use yii\web\UnprocessableEntityHttpException;
use addons\TinyShop\common\models\forms\PreviewForm;
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
        $cartIds = explode(',', $form->data);
        $carts = Yii::$app->tinyShopService->memberCartItem->findByIds($cartIds, $form->buyer_id);
        if (empty($carts) || count($cartIds) != count($carts)) {
            throw new UnprocessableEntityHttpException('找不到产品信息');
        }

        foreach ($carts as $model) {
            $orderProduct = new OrderProduct();
            $orderProduct = $orderProduct->loadDefaultValues();
            $orderProduct->merchant_id = $model['product']['merchant_id'];
            $orderProduct->product_id = $model['product_id'];
            $orderProduct->product_name = $model['product']['name'];
            $orderProduct->sku_id = $model['sku_id'];
            $orderProduct->sku_name = $model['sku']['name'];
            $orderProduct->cost_price = $model['sku']['cost_price'];
            $orderProduct->num = $model['number'];
            $orderProduct->price = $model['sku']['price'];
            $orderProduct->product_money = $orderProduct->num * $orderProduct->price;
            $orderProduct->product_picture = !empty($model['sku']['picture']) ? $model['sku']['picture'] : $model['product']['picture'];
            $orderProduct->buyer_id = $model['member_id'];
            $orderProduct->point_exchange_type = $model['product']['point_exchange_type'];
            $orderProduct->is_virtual = $model['product']['is_virtual'];
            $orderProduct->is_open_commission = $model['product']['is_open_commission'];

            // 默认数据带购物车数量方便计算
            $product = $model['product'];
            $product['number'] = $model['number'];

            // 修改总订单
            $form->product_count += $model['number'];
            $form->merchant_id = $orderProduct->merchant_id;
            $form->product_money += $orderProduct->product_money;
            $form->max_use_point += $model['product']['max_use_point'] * $orderProduct->num; // 最多抵现积分
            $form->defaultProducts[] = $product;
            $form->orderProducts[] = $orderProduct;
            $form->sku[] = $model['sku'];

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