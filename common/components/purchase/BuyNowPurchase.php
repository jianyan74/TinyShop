<?php

namespace addons\TinyShop\common\components\purchase;

use Yii;
use yii\helpers\Json;
use yii\web\UnprocessableEntityHttpException;
use common\helpers\BcHelper;
use common\enums\StatusEnum;
use addons\TinyShop\common\models\forms\PreviewForm;
use addons\TinyShop\common\models\order\OrderProduct;
use addons\TinyShop\common\components\InitOrderDataInterface;
use addons\TinyShop\common\models\product\VirtualType;

/**
 * 立即下单
 *
 * Class BuyNow
 * @package addons\TinyShop\common\components\purchase
 * @author jianyan74 <751393839@qq.com>
 */
class BuyNowPurchase extends InitOrderDataInterface
{
    /**
     * @param PreviewForm $form
     * @return PreviewForm|mixed
     * @throws \yii\web\NotFoundHttpException
     * @throws \yii\web\UnprocessableEntityHttpException
     */
    public function execute(PreviewForm $form): PreviewForm
    {
        try {
            $data = Json::decode($form->data);
        } catch (\Exception $e) {
            throw new UnprocessableEntityHttpException('提交的数据格式有误');
        }

        if (!isset($data['sku_id']) || !($sku = Yii::$app->tinyShopService->productSku->findWithProductById($data['sku_id'], $form->member->id))) {
            throw new UnprocessableEntityHttpException('找不到产品信息');
        }

        if (!isset($data['num']) || (int)$data['num'] < 1) {
            throw new UnprocessableEntityHttpException('请设置正确的下单数量');
        }

        $num = $data['num'];

        $orderProduct = new OrderProduct();
        $orderProduct = $orderProduct->loadDefaultValues();
        $orderProduct->product_id = $sku['product_id'];
        $orderProduct->product_name = $sku['product']['name'];
        $orderProduct->sku_id = $sku['id'];
        $orderProduct->sku_name = $sku['name'];
        $orderProduct->cost_price = $sku['cost_price'];
        $orderProduct->num = $num;
        $orderProduct->price = $sku['price'];
        $orderProduct->product_money = BcHelper::mul($orderProduct->num, $orderProduct->price);
        $orderProduct->product_picture = !empty($sku['picture']) ? $sku['picture'] : $sku['product']['picture'];
        $orderProduct->buyer_id = $form->member->id;
        $orderProduct->merchant_id = $sku['product']['merchant_id'];
        $orderProduct->point_exchange_type = $sku['product']['point_exchange_type'];
        $orderProduct->give_point = $sku['product']['give_point'];
        $orderProduct->is_virtual = $sku['product']['is_virtual'];
        $orderProduct->is_open_commission = $sku['product']['is_open_commission'];

        // 虚拟商品
        if ($orderProduct->is_virtual == StatusEnum::ENABLED) {
            /** @var VirtualType $productVirtualType */
            $productVirtualType = Yii::$app->tinyShopService->productVirtualType->findByProductId($orderProduct->product_id);
            // 虚拟商品类型
            $form->is_virtual = $orderProduct->is_virtual;
            $form->product_virtual_group = $productVirtualType->group;
            $orderProduct->product_virtual_group = $productVirtualType->group;
        }

        // 默认数据带下单数量方便计算
        $product = $sku['product'];
        $product['number'] = $num;

        // 修改总订单
        $form->merchant_id = $orderProduct->merchant_id;
        $form->product_count = $num;
        $form->product_money = $orderProduct->product_money;
        $form->max_use_point = $sku['product']['max_use_point'] * $num; // 最多抵现积分
        $form->defaultProducts[] = $product;
        $form->orderProducts[] = $orderProduct;
        unset($sku['product']);
        $form->sku[] = $sku;

        return $form;
    }

    /**
     * 下单类型
     *
     * @return string
     */
    public static function getType(): string
    {
        return 'buyNow';
    }
}