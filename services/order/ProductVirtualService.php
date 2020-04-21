<?php

namespace addons\TinyShop\services\order;

use addons\TinyShop\common\enums\OrderTypeEnum;
use Yii;
use common\components\Service;
use common\helpers\Html;
use common\helpers\StringHelper;
use addons\TinyShop\common\models\order\ProductVirtual;
use addons\TinyShop\common\enums\VirtualProductGroupEnum;
use addons\TinyShop\common\models\order\Order;
use addons\TinyShop\common\models\order\OrderProduct;
use addons\TinyShop\common\models\virtual\Card;
use addons\TinyShop\common\models\virtual\Download;
use addons\TinyShop\common\models\virtual\NetworkDisk;
use addons\TinyShop\common\enums\ProductVirtualStateEnum;
use yii\web\UnprocessableEntityHttpException;

/**
 * Class ProductVirtualService
 * @package addons\TinyShop\services\order
 * @author jianyan74 <751393839@qq.com>
 */
class ProductVirtualService extends Service
{
    /**
     * 创建卡卷
     *
     * @param Order $order
     * @param bool $verify_order_type 验证订单类型判断是否立即发放
     * @throws \yii\web\NotFoundHttpException
     * @throws UnprocessableEntityHttpException
     */
    public function create(Order $order, $verify_order_type = true)
    {
        // 过滤拼团订单、砍价订单
        if ($verify_order_type && in_array($order->order_type, [OrderTypeEnum::WHOLESALE, OrderTypeEnum::BARGAIN])) {
            return;
        }

        $orderProducts = Yii::$app->tinyShopService->orderProduct->findByOrderIdWithVirtualType($order->id);

        /** @var OrderProduct $orderProduct */
        foreach ($orderProducts as $orderProduct) {
            $num = $orderProduct->num;
            /** @var Card|Download|NetworkDisk $virtualProductGroup */
            $virtualProductGroup = VirtualProductGroupEnum::getModel($orderProduct['virtualType']['group'], $orderProduct['virtualType']['value']);
            for ($i = 0; $i < $num; $i++) {
                $model = new ProductVirtual();
                $model = $model->loadDefaultValues();

                switch ($model->product_group) {
                    case VirtualProductGroupEnum::CARD :
                        $model = $this->findByProductId($orderProduct->product_id);
                        if (!$model) {
                            throw new UnprocessableEntityHttpException('点卡库存不足');
                        }

                        $model->state = ProductVirtualStateEnum::USE;
                        break;
                    case VirtualProductGroupEnum::NETWORK_DISK :
                        $model->remark = "网盘地址：" . $virtualProductGroup->cloud_address . ";网盘密码：" . $virtualProductGroup->cloud_password;
                        $model->state = ProductVirtualStateEnum::USE;
                        break;
                    case VirtualProductGroupEnum::DOWNLOAD :
                        $model->remark = Html::a('点此下载', $virtualProductGroup->text_download_resources) . ";解压密码：" . $virtualProductGroup->unzip_password;
                        $model->state = ProductVirtualStateEnum::USE;
                        break;
                    case VirtualProductGroupEnum::VIRTUAL :
                        $model->code = StringHelper::code($model->merchant_id);
                        $model->state = ProductVirtualStateEnum::NORMAL;
                        break;
                }

                $model->sku_id = $orderProduct->sku_id;
                $model->product_id = $orderProduct->product_id;
                $model->product_name = $orderProduct->product_name;
                $model->product_group = $orderProduct['virtualType']['group'];
                $model->money = $orderProduct->product_money;
                $model->merchant_id = $orderProduct->merchant_id;
                $model->member_id = $orderProduct->member_id;
                $model->member_nickname = $order->user_name;
                $model->order_product_id = $orderProduct->id;
                $model->order_sn = $order->order_sn;
                $model->period = $orderProduct['virtualType']['period'];
                $model->confine_use_number = $orderProduct['virtualType']['confine_use_number'];
                $model->start_time = time();
                $model->end_time = $model->period == 0 ? 0 : time() + $model->period * 3600 * 24;

                $model->save();
            }
        }

        // 如果不是虚拟商品(可核销的)直接已完成订单
        if ($order->product_virtual_group != VirtualProductGroupEnum::VIRTUAL) {
            // 订单收货
            Yii::$app->tinyShopService->order->virtualTakeDelivery($order);
            // 订单完成
            Yii::$app->tinyShopService->order->finalize($order);
        } else {
            // 订单收货
            Yii::$app->tinyShopService->order->virtualTakeDelivery($order);
        }
    }

    /**
     * 失效过期的卡卷
     */
    public function closeAll()
    {
        ProductVirtual::updateAll(
            ['state' => ProductVirtualStateEnum::LOSE],
            [
                'and',
                ['state' => ProductVirtualStateEnum::NORMAL],
                ['>', 'end_time', 0],
                ['<', 'end_time', time()],
            ]
        );
    }

    /**
     * 根据订单失效卡卷
     *
     * @param $order_id
     */
    public function closeByOrderId($order_id)
    {
        ProductVirtual::updateAll(
            ['state' => ProductVirtualStateEnum::LOSE],
            [
                'order_id' => $order_id,
                'state' => ProductVirtualStateEnum::NORMAL
            ]
        );
    }

    /**
     * @param $product_id
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findByProductId($product_id)
    {
        return ProductVirtual::find()
            ->where(['product_id' => $product_id])
            ->andWhere(['state' => ProductVirtualStateEnum::PASSED]) // 待发放
            ->one();
    }

    /**
     * @param $code
     * @param $merchant_id
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findByCodeAndMerchantId($code, $merchant_id)
    {
        return ProductVirtual::find()
            ->where([
                'code' => $code,
                'merchant_id' => $merchant_id,
            ])
            ->one();
    }

    /**
     * @param $order_sn
     * @param $member_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findByOrderSnAndMemberId($order_sn, $member_id)
    {
        return ProductVirtual::find()
            ->where([
                'order_sn' => $order_sn,
                'member_id' => $member_id,
            ])
            ->asArray()
            ->all();
    }

    /**
     * @param $order_product_id
     * @param $member_id
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findByOrderProductIdAndMemberId($order_product_id, $member_id)
    {
        return ProductVirtual::find()
            ->where([
                'order_product_id' => $order_product_id,
                'member_id' => $member_id,
            ])
            ->asArray()
            ->all();
    }
}