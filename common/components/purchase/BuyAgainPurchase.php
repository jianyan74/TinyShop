<?php

namespace addons\TinyShop\common\components\purchase;

use Yii;
use yii\web\UnprocessableEntityHttpException;
use common\helpers\ArrayHelper;
use common\helpers\BcHelper;
use addons\TinyShop\common\components\InitOrderDataInterface;
use addons\TinyShop\common\forms\PreviewForm;
use addons\TinyShop\common\models\order\OrderProduct;

/**
 * 再次购买
 *
 * Class BuyAgainPurchase
 * @package addons\TinyShop\common\components\purchase
 * @author jianyan74 <751393839@qq.com>
 */
class BuyAgainPurchase extends InitOrderDataInterface
{
    /**
     * @param PreviewForm $form
     * @return PreviewForm|mixed
     * @throws \yii\web\NotFoundHttpException
     * @throws \yii\web\UnprocessableEntityHttpException
     */
    public function execute(PreviewForm $form): PreviewForm
    {
        if (
            !$form->data['buy_again_id'] ||
            !($oldOrderProducts = Yii::$app->tinyShopService->orderProduct->findByOrderId($form->data['buy_again_id']))
        ) {
            throw new UnprocessableEntityHttpException('找不到订单');
        }

        $numMap = ArrayHelper::map($oldOrderProducts, 'sku_id', 'num');
        $nameMap = [];
        foreach ($oldOrderProducts as $oldOrderProduct) {
            $nameMap[$oldOrderProduct['sku_id']] = $oldOrderProduct['product_name'] . ' ' . $oldOrderProduct['sku_name'];
        }

        // 查找已有的sku
        $skus = Yii::$app->tinyShopService->productSku->findByIds(ArrayHelper::getColumn($oldOrderProducts, 'sku_id'));
        $skuIds = ArrayHelper::getColumn($skus, 'id');
        foreach ($nameMap as $key => $value) {
            if (!in_array($key, $skuIds)) {
                throw new UnprocessableEntityHttpException($value . ' 已失效');
            }
        }

        foreach ($skus as $model) {
            $orderProduct = new OrderProduct();
            $orderProduct = $orderProduct->loadDefaultValues();
            $orderProduct->merchant_id = $model['product']['merchant_id'];
            $orderProduct->product_id = $model['product_id'];
            $orderProduct->product_name = $model['product']['name'];
            $orderProduct->sku_id = $model['id'];
            $orderProduct->sku_name = $model['name'];
            $orderProduct->num = $numMap[$model['id']];
            $orderProduct->cost_price = BcHelper::mul($orderProduct->num, $model['cost_price']);
            $orderProduct->price = $model['price'];
            $orderProduct->product_money = BcHelper::mul($orderProduct->num, $orderProduct->price);
            $orderProduct->product_original_money = $orderProduct->product_money;
            $orderProduct->product_picture = !empty($model['picture']) ? $model['picture'] : $model['product']['picture'];
            $orderProduct->product_type = $model['product']['type'];
            $orderProduct->stock_deduction_type = $model['product']['stock_deduction_type'];
            $orderProduct->point_exchange_type = $model['product']['point_exchange_type'];
            $orderProduct->supplier_id = $model['product']['supplier_id'];
            $orderProduct->is_commission = $model['product']['is_commission'];

            // 默认数据带购物车数量方便计算
            $product = $model['product'];
            $product['number'] = $orderProduct->num;

            // 修改总订单
            $form->product_count += $orderProduct->num;
            $form->merchant_id = $orderProduct->merchant_id;
            $form->product_money = BcHelper::add($form->product_money, $orderProduct->product_money);
            $form->product_original_money = BcHelper::add($form->product_original_money, $orderProduct->product_money);
            $form->product_type = $orderProduct->product_type;
            $form->max_use_point += $model['product']['max_use_point'] * $orderProduct->num; // 最多抵现积分
            $form->defaultProducts[] = $product;
            $form->orderProducts[] = $orderProduct;
            $form->merchant = $form->merchant_id > 0 ? Yii::$app->services->merchant->findById($form->merchant_id) : [];
            unset($model['product'], $product);
            $form->sku[] = $model;
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
        return 'buyAgain';
    }
}
