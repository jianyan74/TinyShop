<?php

namespace addons\TinyShop\services\order;

use Yii;
use yii\data\Pagination;
use yii\db\ActiveQuery;
use yii\web\UnprocessableEntityHttpException;
use common\helpers\ArrayHelper;
use common\helpers\AddonHelper;
use common\helpers\StringHelper;
use common\helpers\EchantsHelper;
use common\enums\StatusEnum;
use common\enums\PayTypeEnum;
use common\models\member\Member;
use common\helpers\BcHelper;
use common\models\forms\MerchantCreditsLogForm;
use common\models\forms\CreditsLogForm;
use addons\TinyShop\common\models\SettingForm;
use addons\TinyShop\common\models\forms\PreviewForm;
use addons\TinyShop\common\models\order\Order;
use addons\TinyShop\common\models\order\OrderProduct;
use addons\TinyShop\common\enums\OrderStatusEnum;
use addons\TinyShop\common\enums\ShippingTypeEnum;
use addons\TinyShop\common\enums\ExplainStatusEnum;
use addons\TinyShop\common\enums\RefundStatusEnum;
use addons\TinyShop\common\enums\OrderTypeEnum;
use addons\TinyShop\common\models\forms\OrderQueryForm;
use addons\TinyShop\common\enums\AccessTokenGroupEnum;

/**
 * Class OrderService
 * @package addons\TinyShop\services\order
 * @author jianyan74 <751393839@qq.com>
 */
class OrderService extends \common\components\Service
{
    protected $_setting;

    /**
     * 创建订单
     *
     * @param PreviewForm $previewForm
     * @return Order
     * @throws UnprocessableEntityHttpException
     */
    public function create(PreviewForm $previewForm)
    {
        $config = AddonHelper::getConfig();
        // 生成订单
        /** @var Order $order */
        $order = new Order();
        $order = $order->loadDefaultValues();
        $order->attributes = ArrayHelper::toArray($previewForm);
        $order->marketing_id = $previewForm->marketing_id;
        $order->marketing_type = $previewForm->marketing_type;
        $order->order_status = OrderStatusEnum::NOT_PAY;
        $order->out_trade_no = date('YmdHis') . StringHelper::random(10, true);
        $order->order_sn = time() . StringHelper::random(10, true);
        $order->user_name = $previewForm->member->nickname;
        $order->buyer_ip = Yii::$app->request->userIP;
        $order->merchant_name = $config['title'] ?? '';
        $order->give_point_type = $config['shopping_back_points'] ?? 1;
        $order->close_time = $previewForm->close_time;
        $previewForm->final_payment_money > 0 && $order->final_payment_money = $previewForm->final_payment_money;

        // 收货地址
        if (!empty($address = $previewForm->address)) {
            $order->setScenario('address');
            $order->receiver_mobile = $address['mobile'];
            $order->receiver_province = $address['province_id'];
            $order->receiver_city = $address['city_id'];
            $order->receiver_area = $address['area_id'];
            $order->receiver_address = $address['address_details'];
            $order->receiver_region_name = $address['address_name'];
            $order->receiver_zip = (string)$address['zip_code'];
            $order->receiver_name = $address['realname'];
        }

        if (!$order->save()) {
            throw new UnprocessableEntityHttpException($this->getError($order));
        }

        // 门店自提
        if ($order->shipping_type == ShippingTypeEnum::PICKUP) {
            Yii::$app->tinyShopService->orderPickup->create($previewForm->pickup, $order);
        }

        // 使用优惠券
        !empty($previewForm->coupon) && Yii::$app->tinyShopService->marketingCoupon->used($previewForm->coupon, $order->id);
        // 创建订单详情
        $this->createProduct($previewForm->orderProducts, $previewForm->sku, $order);
        // 发票id
        !empty($previewForm->invoice) && $order->invoice_id = $previewForm->invoice->id;
        $order->save();

        // 发票记录
        if (!empty($previewForm->invoice)) {
            Yii::$app->tinyShopService->orderInvoice->create($order, $previewForm->invoice, $previewForm->invoice_content);
        }

        // 记录营销
        !empty($previewForm->marketingDetails) && Yii::$app->tinyShopService->orderProductMarketingDetail->create($order->id, $previewForm->marketingDetails);

        // 记录操作
        Yii::$app->tinyShopService->orderAction->create(
            '创建订单',
            $order->id,
            $order->order_status,
            $previewForm->member->id,
            $previewForm->member->nickname
        );

        return $order;
    }

    /**
     * 支付
     *
     * @param Order $order
     * @param int $paymentType 支付类型
     * @throws UnprocessableEntityHttpException
     * @throws \yii\web\NotFoundHttpException
     */
    public function pay(Order $order, $paymentType)
    {
        if ($order->order_status != OrderStatusEnum::NOT_PAY) {
            throw new UnprocessableEntityHttpException('订单已经被处理，无法进行支付');
        }

        if ($order->pay_status == StatusEnum::ENABLED) {
            throw new UnprocessableEntityHttpException('订单已支付，请不要重复支付');
        }

        // 平台余额支付
        $paymentType == PayTypeEnum::USER_MONEY && $order->user_platform_money = $order->pay_money;
        // 拼团支付回调修改为待成团
        $order->order_status = OrderStatusEnum::PAY;
        $order->payment_type = $paymentType;
        $order->pay_status = StatusEnum::ENABLED;
        $order->pay_time = time();

        // 扣减库存
        $orderProduct = $order->product;
        $skuNums = ArrayHelper::map($orderProduct, 'sku_id', 'num');
        Yii::$app->tinyShopService->productSku->decrRepertory($skuNums);

        return $this->paySuccess($order);
    }

    /**
     * 真正的支付完成
     *
     * @param Order $order
     * @throws UnprocessableEntityHttpException
     * @throws \yii\web\NotFoundHttpException
     */
    protected function paySuccess(Order $order)
    {
        $this->givePoint($order);

        $order->is_new_member = $this->findIsNewMember($order->buyer_id, $order->merchant_id);

        // 记录操作
        Yii::$app->tinyShopService->orderAction->create(
            '订单支付',
            $order->id,
            $order->order_status,
            $order->buyer_id,
            $order->user_name
        );
    }

    /**
     * 关闭订单
     *
     * @param $id
     * @param string $member_id
     * @param bool $constraint 强制关闭不校验被处理
     * @throws UnprocessableEntityHttpException
     * @throws \yii\web\NotFoundHttpException
     */
    public function close($id, $member_id = '', $constraint = false)
    {
        $order = $this->findByIdAndVerify($id, $member_id);
        // 判断订单是否已经被关闭
        if ($order->order_status == OrderStatusEnum::REPEAL) {
            throw new UnprocessableEntityHttpException('订单已经关闭');
        }
        // 判断是否是未支付的订单
        if ($constraint == false && $order->order_status != OrderStatusEnum::NOT_PAY) {
            throw new UnprocessableEntityHttpException('订单已经被处理');
        }

        // 积分返回
        if ($order->point > 0) {
            Yii::$app->services->memberCreditsLog->incrInt(new CreditsLogForm([
                'member' => $order->member,
                'num' => $order->point,
                'credit_group' => 'orderClose',
                'map_id' => $order->id,
                'remark' => Yii::$app->params['tinyShopName']  . '订单关闭',
            ]));
        }

        // 优惠券返回
        $order->coupon_id > 0 && Yii::$app->tinyShopService->marketingCoupon->back($order->coupon_id, $order->buyer_id);

        // 取消赠送
        if ($order->give_point > 0) {
            if (
                ($order->give_point_type == 1 && $order->order_status >= OrderStatusEnum::ACCOMPLISH) ||
                ($order->give_point_type == 2 && $order->order_status >= OrderStatusEnum::SING) ||
                ($order->give_point_type == 3 && $order->order_status >= OrderStatusEnum::PAY)
            ) {
                // 取消赠送
                Yii::$app->services->memberCreditsLog->closeGiveInt(new CreditsLogForm([
                    'member' => $order->member,
                    'num' => $order->give_point,
                    'credit_group' => 'orderCloseGive',
                    'map_id' => $order->id,
                    'remark' => Yii::$app->params['tinyShopName']  . '订单关闭取消赠送',
                ]));
            }
        }

        $order->order_status = OrderStatusEnum::REPEAL;
        if (!$order->save()) {
            throw new UnprocessableEntityHttpException($this->getError($order));
        }
    }

    /**
     * 发货
     *
     * @param $id
     * @throws UnprocessableEntityHttpException
     * @throws \yii\web\NotFoundHttpException
     */
    public function consign($id)
    {
        $order = $this->findById($id);

        if ($order->order_status == OrderStatusEnum::SHIPMENTS) {
            throw new UnprocessableEntityHttpException('订单已经发货');
        }

        if ($order->order_status != OrderStatusEnum::PAY) {
            throw new UnprocessableEntityHttpException('订单已经被处理');
        }

        $order->order_status = OrderStatusEnum::SHIPMENTS;
        $order->consign_time = time();
        if (!$order->save()) {
            throw new UnprocessableEntityHttpException($this->getError($order));
        }
    }

    /**
     * 确认收货
     *
     * @param $id
     * @param string $member_id
     * @throws UnprocessableEntityHttpException
     * @throws \yii\web\NotFoundHttpException
     */
    public function takeDelivery($id, $member_id = '')
    {
        $order = $this->findByIdAndVerify($id, $member_id);

        if ($order->order_status == OrderStatusEnum::SING) {
            throw new UnprocessableEntityHttpException('订单已经签收');
        }

        if ($order->order_status != OrderStatusEnum::SHIPMENTS) {
            throw new UnprocessableEntityHttpException('订单已经被处理');
        }

        if (Yii::$app->tinyShopService->orderProduct->getAfterSaleCountByOrderId($id) > 0) {
            throw new UnprocessableEntityHttpException('请先处理或关闭订单售后');
        }

        $order->order_status = OrderStatusEnum::SING;
        $order->sign_time = time();

        return $this->givePoint($order);
    }

    /**
     * 赠送积分
     *
     * @param Order $model
     * @return bool
     * @throws UnprocessableEntityHttpException
     * @throws \yii\web\NotFoundHttpException
     */
    protected function givePoint(Order $model)
    {
        if (!$model->save()) {
            throw new UnprocessableEntityHttpException($this->getError($model));
        }

        // 赠送积分
        if ($model->give_point > 0) {
            if (
                ($model->give_point_type == 1 && $model->order_status == OrderStatusEnum::ACCOMPLISH) ||
                ($model->give_point_type == 2 && $model->order_status == OrderStatusEnum::SING) ||
                ($model->give_point_type == 3 && $model->order_status == OrderStatusEnum::PAY)
            ) {
                // 赠送积分
                Yii::$app->services->memberCreditsLog->giveInt(new CreditsLogForm([
                    'member' => $model->member,
                    'num' => $model->give_point,
                    'credit_group' => 'orderGive',
                    'map_id' => $model->id,
                    'remark' => Yii::$app->params['tinyShopName']  . '订单赠送',
                ]));
            }
        }

        return true;
    }

    /**
     * 自动收货
     */
    public function signAll($config, $merchant_id)
    {
        $order_auto_delinery = $config['order_auto_delinery'] ?? (new SettingForm())->order_auto_delinery;
        if ($order_auto_delinery == 0) {
            return;
        }

        $orderIds = Order::find()
            ->select('id')
            ->where(['order_status' => OrderStatusEnum::SHIPMENTS])
            ->andWhere(['<=', 'consign_time', time() - $order_auto_delinery * 60 * 60 * 24])
            ->andFilterWhere(['merchant_id' => $merchant_id])
            ->column();

        try {
            foreach ($orderIds as $id) {
                $this->takeDelivery($id);
            }
        } catch (\Exception $e) {

        }
    }

    /**
     * 关闭订单
     *
     * @param $config
     * @param $merchant_id
     * @throws UnprocessableEntityHttpException
     * @throws \yii\web\NotFoundHttpException
     */
    public function closeAll($merchant_id)
    {
        $orderIds = Order::find()
            ->select('id')
            ->where(['order_status' => OrderStatusEnum::NOT_PAY])
            ->andWhere(['<=', 'close_time', time()])
            ->andFilterWhere(['merchant_id' => $merchant_id])
            ->column();

        try {
            foreach ($orderIds as $id) {
                $this->close($id);
                // 记录操作
                Yii::$app->tinyShopService->orderAction->create('自动关闭', $id, OrderStatusEnum::NOT_PAY, 0, '系统');
            }
        } catch (\Exception $e) {
            // 记录行为日志
            Yii::$app->services->log->setErrorStatus(500, 'TinyShopCloseAll', $e->getMessage());
            Yii::$app->services->log->push();
        }
    }

    /**
     * 完成全部订单
     *
     * @param $config
     * @param $merchant_id
     */
    public function finalizeAll($config, $merchant_id)
    {
        $order_delivery_complete_time = $config['order_delivery_complete_time'] ?? (new SettingForm())->order_delivery_complete_time;

        $orders = Order::find()
            ->where(['order_status' => OrderStatusEnum::SING, 'merchant_id' => $merchant_id])
            ->andWhere(['in', 'order_type', OrderTypeEnum::normal()])
            ->andWhere(['<=', 'sign_time', time() - $order_delivery_complete_time * 60 * 60 * 24])
            ->all();

        try {
            /** @var Order $order */
            foreach ($orders as $order) {
                $this->finalize($order);

                // 记录操作
                Yii::$app->tinyShopService->orderAction->create('自动完成', $order['id'], OrderStatusEnum::ACCOMPLISH, 0, '系统');
            }
        } catch (\Exception $e) {
            // 记录行为日志
            Yii::$app->services->log->setErrorStatus(500, 'TinyShopFinalizeAll', $e->getMessage());
            Yii::$app->services->log->push();
        }
    }

    /**
     * 完成订单
     *
     * @param Order $order
     * @throws \yii\web\NotFoundHttpException
     */
    public function finalize(Order $order)
    {
        $order->order_status = OrderStatusEnum::ACCOMPLISH;
        $order->finish_time = time();
        $order->save();
    }

    /**
     * 自动更新订单的整体状态
     *
     * @param $order_id
     * @throws UnprocessableEntityHttpException
     * @throws \yii\web\NotFoundHttpException
     */
    public function autoUpdateStatus($order_id)
    {
        $orderProducts = Yii::$app->tinyShopService->orderProduct->findByOrderId($order_id);

        $count = count($orderProducts);
        $giftCount = 0;
        // 排除赠品
        foreach ($orderProducts as $orderProduct) {
            if ($orderProduct['gift_flag'] > 0) {
                $giftCount++;
            }
        }

        // 正常的数量
        $normalCount = $count - $giftCount;

        // 退款状态数量
        $refundStatusCount = 0;
        // 已发货状态
        $shippingStatusCount = 0;
        // 未发货状态
        $notShippingStatusCount = 0;
        foreach ($orderProducts as $orderProduct) {
            $orderProduct['shipping_status'] == StatusEnum::ENABLED && $shippingStatusCount++;
            $orderProduct['refund_status'] == RefundStatusEnum::CONSENT && $refundStatusCount++;
            // 未发货的正常产品
            if (
                $orderProduct['shipping_status'] == StatusEnum::DISABLED &&
                in_array($orderProduct['refund_status'], RefundStatusEnum::deliver())
            ) {
                $notShippingStatusCount++;
            }
        }

        // 全部已退款直接关闭
        if ($count === $refundStatusCount || $normalCount === $refundStatusCount) {
            return $this->close($order_id, '', true);
        }

        // 全部已发货
        if ($count === $shippingStatusCount) {
            return $this->consign($order_id);
        }

        // 校验发货状态如果其他货物已发就改变订单状态
        if (
            ($count == ($refundStatusCount + $shippingStatusCount)) &&
            ($order = $this->findById($order_id)) &&
            $order['order_status'] == OrderStatusEnum::PAY &&
            $notShippingStatusCount == 0
        ) {
            return $this->consign($order_id);
        }
    }

    /**
     * 完成评价
     *
     * @param $order_id
     */
    public function evaluate($order_id)
    {
        $orderProduct = Yii::$app->tinyShopService->orderProduct->findByOrderId($order_id);

        $status = true;
        foreach ($orderProduct as $item) {
            // TODO 判断订单状态，如果有退款进行中的订单
            if ($item['is_evaluate'] == ExplainStatusEnum::DEAULT) {
                $status = false;
            }
        }

        if ($status == true && ($order = $this->findById($order_id))) {
            $order->is_evaluate = ExplainStatusEnum::EVALUATE;
            $order->review_status = StatusEnum::ENABLED;
            $order->save();
        }
    }

    /**
     * 查询订单
     */
    public function query(OrderQueryForm $queryForm)
    {
        $synthesize_status = $queryForm->synthesize_status;
        // 订单类型
        $order_type = OrderTypeEnum::normal();
        if ($queryForm->order_type) {
            $order_type = explode(',', $queryForm->order_type);
        }

        /** @var ActiveQuery $data */
        $data = Order::find()
            ->alias('o')
            ->where(['>=', 'o.status', StatusEnum::DISABLED])
            ->andWhere(['in', 'o.order_type', $order_type])
            ->andFilterWhere(['o.buyer_id' => $queryForm->member_id])
            ->andFilterWhere(['like', 'o.order_sn', $queryForm->order_sn])
            ->andFilterWhere(['between', 'o.created_at', $queryForm->start_time, $queryForm->end_time])
            ->andFilterWhere(['o.merchant_id' => $this->getMerchantId()])
            ->with(['merchant'])
            ->select([
                'o.id',
                'o.merchant_id',
                'order_sn',
                'close_time',
                'out_trade_no',
                'o.order_type',
                'o.order_status',
                'o.wholesale_id',
                'payment_type',
                'o.shipping_type',
                'o.buyer_id',
                'o.product_money',
                'o.order_money',
                'o.is_evaluate',
                'o.is_virtual',
                'final_payment_money',
                'point',
                'point_money',
                'coupon_money',
                'user_money',
                'o.pay_status',
                'pay_time',
                'shipping_time',
                'sign_time',
                'product_count',
                'pay_money',
                'o.created_at',
            ]);

        ($synthesize_status > 3 || $synthesize_status < -1) && $synthesize_status = 3;
        if (in_array($synthesize_status, [0, 1, 2])) { // 0:待付款; 1:待发货; 2:待收货;
            $data = $data->with(['product'])->andFilterWhere(['o.order_status' => $synthesize_status]);
        } elseif ($synthesize_status == 3) { // 3:评价
            $data = $data->with(['product'])
                ->andWhere(['in', 'o.order_status', [OrderStatusEnum::SING, OrderStatusEnum::ACCOMPLISH]])
                ->andWhere(['review_status' => StatusEnum::DISABLED]);
        } else { // -1:退款/售后
            $data = $data->with(['member'])->joinWith([
                'product p' => function (ActiveQuery $query) {
                    return $query->andWhere([
                        'or',
                        ['in', 'refund_status', RefundStatusEnum::refund()],
                        ['is_customer' => StatusEnum::ENABLED]
                    ]);
                },
            ]);
        }

        // 关键字查询
        $keyword = $queryForm->keyword;
        if (!empty($keyword)) {
            $data = $data->with(['member'])->joinWith([
                'product p' => function (ActiveQuery $query) use ($keyword) {
                    return $query->andWhere(['like', 'p.product_name', $keyword]);
                },
            ]);
        }

        $pages = new Pagination([
            'totalCount' => $data->count(),
            'pageSize' => $this->pageSize,
            'validatePage' => false,
        ]);
        $models = $data->offset($pages->offset)
            ->orderBy('id desc')
            ->asArray()
            ->limit($pages->limit)
            ->all();

        foreach ($models as &$model) {
            // 是否在售后流程
            $model['is_customer'] = StatusEnum::DISABLED;

            foreach ($model['product'] as $product) {
                if ($model['is_customer'] == StatusEnum::ENABLED) {
                    break;
                }
                if (in_array($product['refund_status'], RefundStatusEnum::refund())) {
                    $model['is_customer'] = StatusEnum::ENABLED;
                }
            }
        }

        return $models;
    }

    /**
     * @param $order_id
     * @return array|null|\yii\db\ActiveRecord|Order
     */
    public function findById($order_id)
    {
        return Order::find()
            ->where(['id' => $order_id, 'status' => StatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->one();
    }

    /**
     * 查询并校验订单
     *
     * @param $id
     * @param string $member_id
     * @return array|\yii\db\ActiveRecord|null|Order
     * @throws UnprocessableEntityHttpException
     */
    public function findByIdAndVerify($id, $member_id = '')
    {
        $model = Order::find()
            ->where(['id' => $id, 'status' => StatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->one();

        if (!$model) {
            throw new UnprocessableEntityHttpException('订单不存在');
        }

        if ($member_id && $member_id != $model['buyer_id']) {
            throw new UnprocessableEntityHttpException('权限不足');
        }

        return $model;
    }

    /**
     * 待评价数量
     *
     * @param int $limit
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findEvaluateData($evaluate_day, $limit = 20)
    {
        return Order::find()
            ->where([
                'order_status' => OrderStatusEnum::ACCOMPLISH,
                'is_evaluate' => StatusEnum::DISABLED,
                'review_status' => StatusEnum::DISABLED
            ])
            ->andWhere(['<=', 'finish_time', time() - 3600 * 24 * $evaluate_day])
            ->with(['product'])
            ->limit($limit)
            ->all();
    }

    /**
     * @param $order_id
     * @return array|null|\yii\db\ActiveRecord|Order
     */
    public function findByOrderSn($order_sn)
    {
        return Order::find()
            ->where(['order_sn' => $order_sn, 'status' => StatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->one();
    }

    /**
     * 获取订单数量
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getOrderCountGroupByStatus()
    {
        $order = Order::find()
            ->select(['order_status', 'count(id) as count'])
            ->where(['status' => StatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->groupBy('order_status')
            ->asArray()
            ->all();

        return ArrayHelper::arrayKey($order, 'order_status');
    }

    /**
     * 获取我的订单数量
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getOrderCountGroupByMemberId($member_id)
    {
        $order_status = [
            OrderStatusEnum::NOT_PAY, // 待付款
            OrderStatusEnum::PAY, // 待发货
            OrderStatusEnum::SHIPMENTS, // 待收货
        ];

        $order = Order::find()
            ->select(['order_status', 'count(id) as count'])
            ->where(['status' => StatusEnum::ENABLED])
            ->andWhere(['buyer_id' => $member_id])
            ->andWhere(['in', 'order_status', $order_status])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->groupBy('order_status')
            ->asArray()
            ->all();

        $memberStatus = [];
        // 待评价
        $memberStatus[3] = Order::find()
            ->select(['count(id) as count'])
            ->where(['status' => StatusEnum::ENABLED])
            ->andWhere(['buyer_id' => $member_id])
            ->andWhere(['review_status' => StatusEnum::DISABLED])
            ->andWhere(['in', 'order_status', [OrderStatusEnum::SING, OrderStatusEnum::ACCOMPLISH]])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->scalar();
        // 售后
        $memberStatus[-1] = Yii::$app->tinyShopService->orderProduct->getAfterSaleCount($member_id);
        $order = ArrayHelper::arrayKey($order, 'order_status');
        foreach ($order_status as $status) {
            // 正常统计订单数量
            if (isset($order[$status])) {
                $memberStatus[$status] = $order[$status]['count'];
            } elseif ($status >= 0) {
                $memberStatus[$status] = 0;
            }
        }



        return $memberStatus;
    }

    /**
     * @return int|string
     */
    public function getCount()
    {
        return Order::find()
            ->select('id')
            ->where(['status' => StatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->count();
    }

    /**
     * 获取最新的订单
     *
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findLastPay()
    {
        return Order::find()
            ->select(['created_at', 'order_type', 'pay_money', 'buyer_id', 'user_name'])
            ->where(['pay_status' => StatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->asArray()
            ->one();
    }

    /**
     * 获取指定时间内下单用户数量
     *
     * @param $start_time
     * @param $end_time
     * @return false|string|null
     */
    public function findMemberCountByTime($start_time = '', $end_time = '', $is_new_member = '')
    {
        return Order::find()
            ->select(['buyer_id'])
            ->where(['pay_status' => StatusEnum::ENABLED])
            ->andFilterWhere(['between', 'created_at', $start_time, $end_time])
            ->andFilterWhere(['is_new_member' => $is_new_member])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->groupBy('buyer_id')
            ->count();
    }

    /**
     * 查询是否是新顾客
     *
     * @param $member_id
     * @param $merchant_id
     * @return int
     */
    public function findIsNewMember($member_id, $merchant_id)
    {
        $order = Order::find()
            ->select('id')
            ->where([
                'buyer_id' => $member_id,
                'merchant_id' => $merchant_id,
                'pay_status' => StatusEnum::ENABLED,
            ])
            ->one();

        return !empty($order) ? StatusEnum::DISABLED : StatusEnum::ENABLED;
    }

    /**
     * 获取订单数量、总金额、产品数量
     *
     * @return array|\yii\db\ActiveRecord|null
     */
    public function getStatByTime($time, $select = [])
    {
        $select = ArrayHelper::merge([
            'sum(product_count) as product_count',
            'count(id) as count',
            'sum(pay_money) as pay_money'
        ], $select);
        return Order::find()
            ->select($select)
            ->where(['pay_status' => StatusEnum::ENABLED])
            ->andWhere(['>', 'pay_time', $time])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->asArray()
            ->one();
    }

    /**
     * 获取每天订单数量、总金额、产品数量
     *
     * @return array|\yii\db\ActiveRecord|null
     */
    public function getDayStatByTime($time)
    {
        return Order::find()
            ->select([
                'sum(product_count) as product_count',
                'count(id) as count',
                'sum(pay_money) as pay_money',
                "from_unixtime(created_at, '%Y-%c-%d') as day"
            ])
            ->where(['pay_status' => StatusEnum::ENABLED])
            ->andWhere(['>', 'pay_time', $time])
            ->groupBy(['day'])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->asArray()
            ->all();
    }

    /**
     * @param string $type
     * @param string $count_sql
     * @return array
     */
    public function getBetweenCountStatToEchant($type)
    {
        $fields = [
            'count' => '订单笔数',
            'product_count' => '订单量',
        ];

        // 获取时间和格式化
        list($time, $format) = EchantsHelper::getFormatTime($type);
        // 获取数据
        return EchantsHelper::lineOrBarInTime(function ($start_time, $end_time, $formatting) {
            return Order::find()
                ->select([
                    'count(id) as count',
                    'sum(product_count) as product_count',
                    "from_unixtime(created_at, '$formatting') as time"
                ])
                ->where(['pay_status' => StatusEnum::ENABLED])
                ->andWhere(['between', 'pay_time', $start_time, $end_time])
                ->groupBy(['time'])
                ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
                ->asArray()
                ->all();
        }, $fields, $time, $format);
    }

    /**
     * @param string $type
     * @param string $count_sql
     * @return array
     */
    public function getBetweenProductMoneyAndCountStatToEchant($type)
    {
        $fields = [
            'pay_money' => '下单金额',
            // 'product_count' => '下单量',
        ];

        // 获取时间和格式化
        list($time, $format) = EchantsHelper::getFormatTime($type);
        // 获取数据
        return EchantsHelper::lineOrBarInTime(function ($start_time, $end_time, $formatting) {
            return Order::find()
                ->select([
                    'sum(pay_money) as pay_money',
                   // 'sum(product_count) as product_count',
                    "from_unixtime(created_at, '$formatting') as time"
                ])
                ->where(['pay_status' => StatusEnum::ENABLED])
                ->andWhere(['between', 'pay_time', $start_time, $end_time])
                ->groupBy(['time'])
                ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
                ->asArray()
                ->all();
        }, $fields, $time, $format);
    }

    /**
     * @param string $type
     * @param string $count_sql
     * @return array
     */
    public function getOrderCreateCountStat($type)
    {
        $fields = [
            [
                'name' => '下单数量',
                'type' => 'bar',
                'field' => 'count',
            ],
            [
                'name' => '支付数量',
                'type' => 'bar',
                'field' => 'pay_count',
            ],
            [
                'name' => '下单支付转化率',
                'type' => 'line',
                'field' => 'pay_rate',
            ],
        ];

        // 获取时间和格式化
        list($time, $format) = EchantsHelper::getFormatTime($type);
        // 获取数据
        return EchantsHelper::lineOrBarInTime(function ($start_time, $end_time, $formatting) {
            $data = Order::find()
                ->select([
                    'count(id) as count',
                    'sum(pay_status) as pay_count',
                    "from_unixtime(created_at, '$formatting') as time"
                ])
                ->andWhere(['between', 'created_at', $start_time, $end_time])
                ->groupBy(['time'])
                ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
                ->asArray()
                ->all();

            foreach ($data as &$datum) {
                $datum['pay_rate'] = BcHelper::mul(BcHelper::div($datum['pay_count'], $datum['count']), 100);
            }

            return $data;
        }, $fields, $time, $format);
    }

    /**
     * @param string $type
     * @param string $count_sql
     * @return array
     */
    public function getBetweenProductCountAndCountStatToEchant($type)
    {
        $fields = [
             'product_count' => '商品售出数',
        ];

        // 获取时间和格式化
        list($time, $format) = EchantsHelper::getFormatTime($type);
        // 获取数据
        return EchantsHelper::lineOrBarInTime(function ($start_time, $end_time, $formatting) {
            return Order::find()
                ->select([
                    'sum(product_count) as product_count',
                    "from_unixtime(created_at, '$formatting') as time"
                ])
                ->where(['pay_status' => StatusEnum::ENABLED])
                ->andWhere(['between', 'pay_time', $start_time, $end_time])
                ->groupBy(['time'])
                ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
                ->asArray()
                ->all();
        }, $fields, $time, $format);
    }

    /**
     * 订单来源统计
     *
     * @return array
     */
    public function getFormStat($type)
    {
        $fields = array_values(AccessTokenGroupEnum::getMap());

        // 获取时间和格式化
        list($time, $format) = EchantsHelper::getFormatTime($type);
        // 获取数据
        return EchantsHelper::pie(function ($start_time, $end_time) use ($fields) {
            $data = Order::find()
                ->select(['count(id) as value', 'order_from'])
                ->where(['status' => StatusEnum::ENABLED])
                ->andFilterWhere(['between', 'created_at', $start_time, $end_time])
                ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
                ->groupBy(['order_from'])
                ->asArray()
                ->all();

            foreach ($data as &$datum) {
                $datum['name'] = AccessTokenGroupEnum::getValue($datum['order_from']);
            }

            return [$data, $fields];
        }, $time);
    }

    /**
     * 订单类型统计
     *
     * @return array
     */
    public function getOrderTypeStat($type)
    {
        $fields = array_values(OrderTypeEnum::getMap());

        // 获取时间和格式化
        list($time, $format) = EchantsHelper::getFormatTime($type);
        // 获取数据
        return EchantsHelper::pie(function ($start_time, $end_time) use ($fields) {
            $data = Order::find()
                ->select(['count(id) as value', 'order_type'])
                ->where(['status' => StatusEnum::ENABLED])
                ->andFilterWhere(['between', 'created_at', $start_time, $end_time])
                ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
                ->groupBy(['order_type'])
                ->asArray()
                ->all();

            foreach ($data as &$datum) {
                $datum['name'] = OrderTypeEnum::getValue($datum['order_type']);
            }

            return [$data, $fields];
        }, $time);
    }

    /**
     * 创建产品
     *
     * @param $orderProducts
     * @param $sku
     * @param Order $order
     * @throws UnprocessableEntityHttpException
     */
    protected function createProduct($orderProducts, $sku, Order $order)
    {
        $sku = ArrayHelper::arrayKey($sku, 'id');

        /** @var OrderProduct $model */
        foreach ($orderProducts as $model) {
            // 库存判断
            if ($sku[$model['sku_id']]['stock'] < $model['num']) {
                // 如果是赠品且库存不足跳出本次循环
                if ($model['gift_flag'] > 0) {
                    continue;
                }

                throw new UnprocessableEntityHttpException($model['product_name'] . ' 商品库存不足');
            }

            $model->order_id = $order->id;
            $model->member_id = $order->buyer_id;
            $model->order_type = $order->order_type;
            $model->order_status = $order->order_status;
            if (!$model->save()) {
                throw new UnprocessableEntityHttpException($this->getError($model));
            }
        }
    }

    /**
     * @return SettingForm|mixed
     */
    protected function getSetting()
    {
        if (!$this->_setting) {
            $setting = new SettingForm();
            $setting->attributes = AddonHelper::getConfig();
            $this->_setting = $setting;
        }

        return $this->_setting;
    }
}