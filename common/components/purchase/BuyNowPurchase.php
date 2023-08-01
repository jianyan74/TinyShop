<?php

namespace addons\TinyShop\common\components\purchase;

use Yii;
use yii\web\UnprocessableEntityHttpException;
use common\helpers\BcHelper;
use addons\TinyShop\common\forms\PreviewForm;
use addons\TinyShop\common\models\order\OrderProduct;
use addons\TinyShop\common\components\InitOrderDataInterface;
use addons\TinyShop\common\enums\MarketingEnum;
use addons\TinyShop\common\models\marketing\MarketingProduct;
use addons\TinyShop\common\models\marketing\MarketingProductSku;

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
        $data = $form->data;
        if (empty($data['sku_id']) || empty($sku = Yii::$app->tinyShopService->productSku->findWithProductById($data['sku_id'], $form->member->id))) {
            throw new UnprocessableEntityHttpException('找不到商品信息');
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
        $orderProduct->num = $num;
        $orderProduct->price = $sku['price'];
        $orderProduct->cost_price = BcHelper::mul($orderProduct->num, $sku['cost_price']);
        $orderProduct->product_money = BcHelper::mul($orderProduct->num, $orderProduct->price);
        $orderProduct->profit_price = BcHelper::sub($orderProduct->product_money, $orderProduct->cost_price);
        $orderProduct->product_original_money = $orderProduct->product_money;
        $orderProduct->product_picture = !empty($sku['picture']) ? $sku['picture'] : $sku['product']['picture'];
        $orderProduct->product_type = $sku['product']['type'];
        $orderProduct->stock_deduction_type = $sku['product']['stock_deduction_type'];
        $orderProduct->buyer_id = $form->member->id;
        $orderProduct->merchant_id = $sku['product']['merchant_id'];
        $orderProduct->point_exchange_type = $sku['product']['point_exchange_type'];
        $orderProduct->give_point = $sku['product']['give_point'];
        $orderProduct->give_growth = $sku['product']['give_growth'];
        $orderProduct->supplier_id = $sku['product']['supplier_id'];
        $orderProduct->is_commission = $sku['product']['is_commission'];

        // 默认数据带下单数量方便计算
        $product = $sku['product'];
        $product['number'] = $num;

        // 修改总订单
        $form->merchant_id = $orderProduct->merchant_id;
        $form->product_count = $num;
        $form->product_money = $orderProduct->product_money;
        $form->product_original_money = $orderProduct->product_money;
        $form->product_type = $orderProduct->product_type;
        $form->max_use_point = $sku['product']['max_use_point'] * $num; // 最多抵现积分
        $form->defaultProducts[] = $product;
        $form->orderProducts[] = $orderProduct;
        $form->merchant = $form->merchant_id > 0 ? Yii::$app->services->merchant->findById($form->merchant_id) : [];
        unset($sku['product']);
        $form->sku[] = $sku;

        return $form;
    }

    /**
     * @param array $data
     * @param OrderProduct $orderProduct
     * @param string $title
     * @return MarketingProduct|MarketingProductSku|array|\yii\db\ActiveRecord
     * @throws UnprocessableEntityHttpException
     */
    protected function getMarketing(array $data, $marketingType, OrderProduct $orderProduct, $title = '')
    {
        if (!$data['marketing_id']) {
            throw new UnprocessableEntityHttpException('未填写 marketing_id');
        }

        $result = Yii::$app->tinyShopService->marketingProductSku->findByIdAndMarketing(
            $orderProduct->product_id,
            $orderProduct->sku_id,
            $data['marketing_id'],
            $marketingType,
            $data['marketing_product_id'] ?? '',
        );

        /** @var MarketingProductSku $marketing 营销规格 */
        $marketing = [];
        if (count($result) == 1) {
            $marketing = $result[0];
        } else {
            foreach ($result as $item) {
                if ($item['sku_id'] > 0) {
                    $marketing = $item;
                }
            }
        }

        if (empty($marketing)) {
            throw new UnprocessableEntityHttpException('该' . $title . '活动无效，请刷新重新下单');
        }

        if ($marketing->start_time > time()) {
            throw new UnprocessableEntityHttpException('该' . $title . '活动未开启');
        }

        if ($marketing->end_time < time()) {
            throw new UnprocessableEntityHttpException('该' . $title . '活动已结束');
        }

        // 单次最少购买
        if (
            $marketing->min_buy > 0 &&
            $orderProduct->num < $marketing->min_buy
        ) {
            throw new UnprocessableEntityHttpException('该活动 ' . $orderProduct->product_name . ' 最少购买数量为 ' . $marketing->min_buy . ' 件');
        }

        // 限购判断
        if (
            $marketing->max_buy > 0 &&
            !empty($buyNum = Yii::$app->tinyShopService->orderProduct->findSumByMember($orderProduct->product_id, $orderProduct->buyer_id, $marketing->marketing_id, $marketing->marketing_type)) &&
            (($buyNum + $orderProduct->num) > $marketing->max_buy)) {
            throw new UnprocessableEntityHttpException('该活动 ' . $orderProduct->product_name . ' 最多可购买数量为 ' . $marketing->max_buy . ' 件');
        }

        return $marketing;
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
