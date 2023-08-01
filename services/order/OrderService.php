<?php

namespace addons\TinyShop\services\order;

use Yii;
use common\enums\PayTypeEnum;
use common\forms\CreditsLogForm;
use common\helpers\StringHelper;
use common\helpers\ArrayHelper;
use yii\data\Pagination;
use yii\db\ActiveQuery;
use yii\web\UnprocessableEntityHttpException;
use common\components\Service;
use common\enums\StatusEnum;
use common\enums\PayStatusEnum;
use common\helpers\BcHelper;
use common\enums\ExtendConfigTypeEnum;
use addons\TinyShop\common\enums\ExplainStatusEnum;
use addons\TinyShop\common\enums\ProductTypeEnum;
use addons\TinyShop\common\forms\SettingForm;
use addons\TinyShop\common\enums\OrderStatusEnum;
use addons\TinyShop\common\enums\MarketingEnum;
use addons\TinyShop\common\enums\ShippingTypeEnum;
use addons\TinyShop\common\enums\SubscriptionActionEnum;
use addons\TinyShop\common\enums\RefundStatusEnum;
use addons\TinyShop\common\forms\OrderSearchForm;
use addons\TinyShop\common\forms\PreviewForm;
use addons\TinyShop\common\models\order\Order;
use addons\TinyShop\common\models\order\OrderProduct;
use addons\TinyShop\common\enums\ProductStockDeductionTypeEnum;

/**
 * Class OrderService
 * @package addons\TinyShop\services\order
 */
class OrderService extends Service
{
    /**
     * @param PreviewForm $previewForm
     * @return Order
     */
    public function create(PreviewForm $previewForm)
    {
        $order = new Order();
        $order = $order->loadDefaultValues();
        $order->attributes = ArrayHelper::toArray($previewForm);
        $order->order_status = OrderStatusEnum::NOT_PAY;
        $order->out_trade_no = date('YmdHis') . StringHelper::random(10, true);
        $order->order_sn = time() . StringHelper::random(10, true);
        $order->buyer_ip = Yii::$app->services->base->getUserIp();
        $order->buyer_nickname = $previewForm->member->nickname ?? '';
        empty($order->close_time) && $order->close_time = (int) (time() + $previewForm->config['order_buy_close_time'] * 60);
        $order->give_point_type = $previewForm->config['order_back_points'];
        $order->give_growth_type = $previewForm->config['order_back_growth'];

        // 收货地址
        if (!empty($address = $previewForm->address)) {
            $order->setScenario('address');
            $order->receiver_id = $address['id'];
            $order->receiver_realname = $address['realname'];
            $order->receiver_mobile = $address['mobile'];
            $order->receiver_province_id = $address['province_id'];
            $order->receiver_city_id = $address['city_id'];
            $order->receiver_area_id = $address['area_id'];
            $order->receiver_details = $address['details'] . ' ' . $address['street_number'];
            $order->receiver_name = $address['name'];
            $order->receiver_zip = (string)$address['zip_code'];
            $order->receiver_longitude = $address['longitude'];
            $order->receiver_latitude = $address['latitude'];
        }

        if ($order->merchant_id > 0 && ($merchant = Yii::$app->services->merchant->findById($order->merchant_id))) {
            $order->merchant_title = $merchant->title;
        }

        if (!$order->save()) {
            throw new UnprocessableEntityHttpException($this->getError($order));
        }

        // 门店自提/到店
        if (in_array($order->shipping_type, [ShippingTypeEnum::PICKUP, ShippingTypeEnum::TO_STORE])) {
            Yii::$app->tinyShopService->orderStore->create($previewForm->store, $order);
            $order->store_id = $previewForm->store->id;
        }

        // 使用优惠券
        !empty($previewForm->coupon) && Yii::$app->tinyShopService->marketingCoupon->used($previewForm->coupon, $order->id);
        // 创建订单详情
        $orderProducts = $this->createProduct($previewForm->orderProducts, $previewForm->sku, $order);
        // 发票记录
        if (!empty($previewForm->invoice)) {
            $orderInvoice = Yii::$app->tinyShopService->orderInvoice->create($order, $previewForm->invoice, $previewForm->invoice_content);
            Order::updateAll(['invoice_id' => $orderInvoice->id], ['id' => $order->id]);
        }

        // 记录营销
        !empty($previewForm->marketingDetails) && Yii::$app->tinyShopService->orderMarketingDetail->create($order->id, $previewForm->marketingDetails);

        // 扣除库存判断
        Yii::$app->tinyShopService->productSku->decrRepertory($order, $orderProducts, ProductStockDeductionTypeEnum::CREATE);

        // 记录操作
        Yii::$app->services->actionLog->create('order', '创建订单', $order->id);

        return $order;
    }

    /**
     * @param Order $order
     * @param $payType
     * @param false $mandatoryInventoryReductions
     * @throws UnprocessableEntityHttpException
     */
    public function pay(Order $order, $payType, $mandatoryInventoryReductions = false)
    {
        if (!in_array($order->order_status, [OrderStatusEnum::NOT_PAY])) {
            throw new UnprocessableEntityHttpException('订单已经被处理');
        }

        if ($order->pay_status == PayStatusEnum::YES) {
            throw new UnprocessableEntityHttpException('请不要重复支付');
        }

        $order->order_status = OrderStatusEnum::PAY;
        $order->pay_type = $payType;
        $order->pay_status = PayStatusEnum::YES;
        $order->pay_time = time();

        try {
            Yii::$app->tinyShopService->productSku->decrRepertory($order, $order->product, ProductStockDeductionTypeEnum::PAY);
        } catch (\Exception $e) {
            if ($mandatoryInventoryReductions == false) {
                throw new UnprocessableEntityHttpException($e->getMessage());
            } else {
                $order->is_oversold = StatusEnum::ENABLED;
            }
        }

        // 赠送积分
        $this->give($order);
        // 自动打印
        $this->autoPrintReceiptAll($order);

        // 判断同城配送是否自动确认订单
        if (
            $order->shipping_type == ShippingTypeEnum::LOCAL_DISTRIBUTION &&
            ($localConfig = Yii::$app->tinyShopService->localConfig->findByMerchantId($order->merchant_id)) &&
            $localConfig->auto_order_receiving == StatusEnum::ENABLED
        ) {
            try {
                 $this->affirm($order->id);
            } catch (\Exception $e) {
                Yii::error($e->getMessage());
            }
        }

        return $this->realPay($order);
    }

    /**
     * 最终支付
     *
     * @param Order $order
     */
    protected function realPay(Order $order): Order
    {
        if (!$order->save()) {
            throw new UnprocessableEntityHttpException($this->getError($order));
        }

        // 订单支付提醒
        Yii::$app->tinyShopService->notify->createRemind(
            $order->id,
            SubscriptionActionEnum::ORDER_PAY,
            $order->merchant_id,
            ['order' => $order]
        );

        // 记录操作
        Yii::$app->services->actionLog->create('order', '订单支付', $order->id);

        // 电子卡卷
        if (in_array($order->product_type, [ProductTypeEnum::CARD_VOLUME])) {
            Yii::$app->tinyShopService->orderProductCode->create($order);
        }

        return $order;
    }

    /**
     * @param Order $model
     * @throws \yii\web\NotFoundHttpException
     */
    protected function give(Order $model)
    {
        // 赠送积分
        if ($model->give_point > 0) {
            if (
                ($model->give_point_type == 1 && $model->order_status == OrderStatusEnum::ACCOMPLISH) ||
                ($model->give_point_type == 2 && $model->order_status == OrderStatusEnum::SING) ||
                ($model->give_point_type == 3 && $model->order_status == OrderStatusEnum::PAY)
            ) {
                // 赠送积分
                Yii::$app->services->memberCreditsLog->incrInt(new CreditsLogForm([
                    'member' => $model->member,
                    'num' => $model->give_point,
                    'group' => 'orderGive',
                    'map_id' => $model->id,
                    'remark' => '订单赠送-' . $model->order_sn,
                    'is_give' => true,
                ]));
            }
        }

        // 赠送成长值
        if ($model->give_growth > 0) {
            if (
                ($model->give_growth_type == 1 && $model->order_status == OrderStatusEnum::ACCOMPLISH) ||
                ($model->give_growth_type == 2 && $model->order_status == OrderStatusEnum::SING) ||
                ($model->give_point_type == 3 && $model->order_status == OrderStatusEnum::PAY)
            ) {
                // 赠送成长值
                Yii::$app->services->memberCreditsLog->incrGrowth(new CreditsLogForm([
                    'member' => $model->member,
                    'num' => $model->give_growth,
                    'group' => 'orderGive',
                    'map_id' => $model->id,
                    'remark' => '订单赠送-' . $model->order_sn,
                ]));
            }
        }

        return true;
    }

    /**
     * 确认订单
     *
     * @param $id
     */
    public function affirm($id)
    {

    }

    /**
     * 退单
     *
     * @param $id
     * @return void
     */
    public function chargeback($id)
    {
        /** @var Order $order */
        $order = $this->findModel($id);
        if (!empty($order->productExpress)) {
            throw new UnprocessableEntityHttpException('部分商品已发货，不支持退单');
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

        $config = Yii::$app->tinyShopService->config->setting();
        $order->auto_sign_time = (int) (time() + $config->order_auto_delivery * 3600 * 24);
        $order->order_status = OrderStatusEnum::SHIPMENTS;
        $order->consign_time = time();
        if (!$order->save()) {
            throw new UnprocessableEntityHttpException($this->getError($order));
        }

        // 订单发货提醒
        Yii::$app->tinyShopService->notify->createRemindByReceiver(
            $order->id,
            SubscriptionActionEnum::ORDER_CONSIGN,
            $order->buyer_id,
            ['order' => $order]
        );

        return true;
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

        if (in_array($order->product_type, ProductTypeEnum::entity()) && $order->order_status != OrderStatusEnum::SHIPMENTS) {
            throw new UnprocessableEntityHttpException('订单已经被处理');
        }

        if (Yii::$app->tinyShopService->orderProduct->findAfterSaleCountByOrderId($id) > 0) {
            throw new UnprocessableEntityHttpException('请先处理或关闭订单售后');
        }

        // 虚拟商品
        !in_array($order->product_type, ProductTypeEnum::entity()) && $order->consign_time = time();

        $config = Yii::$app->tinyShopService->config->setting();
        $order->auto_finish_time = (int) (time() + $config->order_auto_complete_time * 3600 * 24);
        $order->order_status = OrderStatusEnum::SING;
        $order->sign_time = time();
        if (!$order->save()) {
            throw new UnprocessableEntityHttpException($this->getError($order));
        }

        // 记录操作
        Yii::$app->services->actionLog->create('order', '确认收货', $order->id);

        return $this->give($order);
    }

    /**
     * 完成订单
     *
     * @param Order $order
     * @throws \yii\web\NotFoundHttpException
     */
    public function finalize(Order $order, SettingForm $setting)
    {
        if ($order->order_status == OrderStatusEnum::ACCOMPLISH) {
            return false;
        }

        $order->auto_evaluate_time = 0;
        if (empty($setting->evaluate_day)) {
            $order->auto_evaluate_time = (int) (time() + $setting->order_evaluate_day * 3600 * 24);
        }

        $order->order_status = OrderStatusEnum::ACCOMPLISH;
        $order->finish_time = time();
        $order->save();

        // 赠送积分
        $this->give($order);
    }

    /**
     * 完成评价
     *
     * @param $order_id
     * @return false|void
     */
    public function evaluate($order_id)
    {
        $orderProduct = Yii::$app->tinyShopService->orderProduct->findByOrderId($order_id);
        foreach ($orderProduct as $item) {
            // TODO 判断订单状态，如果有退款进行中的订单
            if ($item['is_evaluate'] == ExplainStatusEnum::DEAULT) {
                return false;
            }
        }

        if ($order = $this->findById($order_id)) {
            $order->is_evaluate = ExplainStatusEnum::EVALUATE;
            $order->save();
        }
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
            return true;
        }

        // 判断是否是未支付的订单
        if ($constraint == false && !in_array($order->order_status, [OrderStatusEnum::NOT_PAY, OrderStatusEnum::PAY])) {
            throw new UnprocessableEntityHttpException('订单已经被处理');
        }

        // 积分返回
        if ($order->point > 0) {
            Yii::$app->services->memberCreditsLog->incrInt(new CreditsLogForm([
                'member' => $order->member,
                'num' => $order->point,
                'group' => 'orderClose',
                'map_id' => $order->id,
                'remark' => '订单关闭-'. $order->order_sn,
            ]));
        }

        $order->order_status = OrderStatusEnum::REPEAL;
        if (!$order->save()) {
            throw new UnprocessableEntityHttpException($this->getError($order));
        }

        return true;
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
        $order = $this->findById($order_id);
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
            // 未发货的正常商品
            if (
                $orderProduct['shipping_status'] == StatusEnum::DISABLED &&
                in_array($orderProduct['refund_status'], RefundStatusEnum::deliver())
            ) {
                $notShippingStatusCount++;
            }
        }

        // 全部已退款直接关闭
        if ($count === $refundStatusCount || $normalCount === $refundStatusCount) {
            // 记录操作
            Yii::$app->services->actionLog->create('order', '关闭订单', $order_id);

            return $this->close($order_id, '', true);
        }

        // 全部已发货
        if ($count === $shippingStatusCount && in_array($order['order_status'], [OrderStatusEnum::PAY])) {
            return $this->consign($order_id);
        }

        // 校验发货状态如果其他货物已发就改变订单状态
        if (
            ($count == ($refundStatusCount + $shippingStatusCount)) &&
            in_array($order['order_status'], [OrderStatusEnum::PAY]) &&
            $notShippingStatusCount == 0
        ) {
            return $this->consign($order_id);
        }
    }

    /**
     * 查询订单
     *
     * @param OrderSearchForm $queryForm
     * @param false $pageData
     * @return array|\yii\db\ActiveRecord[]
     */
    public function query(OrderSearchForm $queryForm, $pageData = false)
    {
        $synthesizeStatus = $queryForm->synthesize_status;
        // 订单类型
        $orderType = !empty($queryForm->order_type) ? explode(',', $queryForm->order_type) : [];

        // 待评价
        ($synthesizeStatus > OrderStatusEnum::SING || $synthesizeStatus < OrderStatusEnum::REFUND_APPLY) && $synthesizeStatus = OrderStatusEnum::SING;

        $orderStatus = $condition = [];
        if (
            $synthesizeStatus !== '' &&
            in_array($synthesizeStatus, [OrderStatusEnum::NOT_PAY, OrderStatusEnum::PAY, OrderStatusEnum::SHIPMENTS])
        ) { // 0:待付款; 10:待发货; 20:待收货;
            $orderStatus = [$synthesizeStatus];
        } elseif ($synthesizeStatus == OrderStatusEnum::SING) { // 30:评价
            $orderStatus = [OrderStatusEnum::SING, OrderStatusEnum::ACCOMPLISH];
            $condition = ['is_evaluate' => StatusEnum::DISABLED];
        } elseif ($synthesizeStatus == OrderStatusEnum::REFUND_APPLY) { // -10:退款/售后
            $condition = ['is_after_sale' => StatusEnum::ENABLED];
        }

        /** @var ActiveQuery $data */
        $data = Order::find()
            ->where(['>=', 'status', StatusEnum::DISABLED])
            ->andFilterWhere(['in', 'order_type', $orderType])
            ->andFilterWhere(['in', 'order_status', $orderStatus])
            ->andFilterWhere(['buyer_id' => $queryForm->member_id])
            ->andFilterWhere(['shipping_type' => $queryForm->shipping_type])
            ->andFilterWhere(['like', 'order_sn', $queryForm->order_sn])
            ->andFilterWhere(['like', 'order_sn', $queryForm->keyword])
            ->andFilterWhere(['between', 'created_at', $queryForm->start_time, $queryForm->end_time])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->andFilterWhere($condition)
            ->with(['product', 'merchant']);

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
            foreach ($model['product'] as &$value) {
                // 调整金额
                $value['product_money'] = BcHelper::add($value['product_money'], $value['adjust_money']);
            }
        }

        return $pageData == true ? [$models, $pages] : $models;
    }

    /**
     * 自动打印
     *
     * @param Order $order
     */
    public function autoPrintReceiptAll(Order $order)
    {
        try {
            $print = Yii::$app->services->extendConfig->findByType(ExtendConfigTypeEnum::RECEIPT_PRINTER, $order->merchant_id, StatusEnum::ENABLED);
            foreach ($print as $item) {
                $this->printReceipt($order, $item['id']);
            }
            // 打印记录
            !empty($print) && Order::updateAll(['is_print' => StatusEnum::ENABLED], ['id' => $order->id]);
        } catch (\Exception $e) {

        }
    }

    /**
     * 打印单个
     *
     * @param Order $order
     * @param $config_id
     * @return array|mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function printReceipt(Order $order, $config_id)
    {
        $products = [];
        $orderProducts = $order->product;
        /** @var OrderProduct $orderProduct */
        foreach ($orderProducts as $orderProduct) {
            $skuName = '';
            if ($orderProduct->sku_name) {
                $skuName = ' - ' . $orderProduct->sku_name;
            }

            $products[] = [
                'title' => $orderProduct->product_name . $skuName, // 商品名称
                'num' => $orderProduct->num, // 商品数量
                'price' => $orderProduct->product_original_money, // 商品原价
            ];
        }

        // 合并营销显示
        $marketingDetails = Yii::$app->tinyShopService->marketing->mergeIdenticalMarketing($order->marketingDetail ?? []);
        $marketingDetails = ArrayHelper::arrayKey($marketingDetails, 'marketing_type');
        unset($marketingDetails[MarketingEnum::GIVE_POINT], $marketingDetails[MarketingEnum::FULL_MAIL]);

        $data = [
            'title' => Yii::$app->params['tinyShopName'] ?? '', // 商城名称
            'payType' => PayTypeEnum::getValue($order->pay_type),
            'merchantTitle' => $order->merchant_title, // 门店名称
            'orderTime' => Yii::$app->formatter->asDatetime($order->created_at), // 下单时间
            'orderSn' => $order->order_sn, // 下单编号
            'productOriginalMoney' => $order->product_original_money, // 订单总价
            'marketingDetails' => $marketingDetails, // 优惠
            'shippingMoney' => $order->shipping_money, // 配送费
            'pointMoney' => $order->point_money ?? 0, // 积分抵扣
            'taxMoney' => $order->tax_money, // 发票税额
            'payMoney' => $order->pay_money, // 实付金额
            'nickname' => $order->buyer_nickname, // 会员
            'receiverRegionName' => $order->receiver_name, // 配送地址
            'receiverAddress' => $order->receiver_details, // 配送地址
            'receiverName' => $order->receiver_realname, // 联系人
            'receiverMobile' => $order->receiver_mobile, // 联系方式
            'buyerMessage' => $order->buyer_message, // 留言
            'productCount' => $order->product_count,
            'products' => $products,
            'qr' => '', // 二维码内容
        ];

        // 打印记录
        Order::updateAll(['is_print' => StatusEnum::ENABLED], ['id' => $order->id]);

        return Yii::$app->services->extendPrinter->printerById($config_id, $data);
    }

    /**
     * 创建商品
     *
     * @param $orderProducts
     * @param $sku
     * @param Order $order
     * @return mixed
     * @throws UnprocessableEntityHttpException
     */
    protected function createProduct($orderProducts, $sku, Order $order)
    {
        $sku = ArrayHelper::arrayKey($sku, 'id');
        /** @var OrderProduct $model */
        foreach ($orderProducts as $model) {
            // 库存判断
            if (
                $sku[$model['sku_id']]['stock'] < $model['num']
            ) {
                // 如果是赠品且库存不足跳出本次循环
                if ($model['gift_flag'] > 0) {
                    continue;
                }

                throw new UnprocessableEntityHttpException($model['product_name'] . ' 商品库存不足');
            }

            $model->order_id = $order->id;
            $model->order_sn = $order->order_sn;
            $model->store_id = $order->store_id;
            $model->buyer_id = $order->buyer_id;
            $model->order_type = $order->order_type;
            $model->order_status = $order->order_status;
            if (!$model->save()) {
                throw new UnprocessableEntityHttpException($this->getError($model));
            }
        }

        return $orderProducts;
    }

    /**
     * 获取订单数量
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getOrderCountGroupByStatus($condition = [])
    {
        $order = Order::find()
            ->select(['order_status', 'count(id) as count'])
            ->where(['status' => StatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->andFilterWhere($condition)
            ->andFilterWhere(['store_id' => Yii::$app->params['store_id']])
            ->groupBy('order_status')
            ->asArray()
            ->all();

        $data = [];
        foreach ($order as $item) {
            $item['count'] = (int)$item['count'];
            $data[$item['order_status']] = $item;
        }

        return $data;
    }

    /**
     * 获取我的订单数量
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getOrderStatusCountByMemberId($member_id)
    {
        $orderStatus = [
            OrderStatusEnum::NOT_PAY, // 待付款
            OrderStatusEnum::PAY, // 待发货
            OrderStatusEnum::SHIPMENTS, // 待收货
        ];

        $order = $this->getOrderCountGroupByStatus([
            'and',
            ['in', 'order_status', $orderStatus],
            ['buyer_id' => $member_id],
        ]);

        return [
            'remainToBeEvaluated' => (int)$this->remainToBeEvaluated($member_id), // 待评价
            'afterSale' => (int)Yii::$app->tinyShopService->orderProduct->getAfterSaleCount($member_id), // 售后
            'notPay' => $order[OrderStatusEnum::NOT_PAY]['count'] ?? 0, // 待付款
            'pay' => $order[OrderStatusEnum::PAY]['count'] ?? 0, // 待发货
            'shipments' => $order[OrderStatusEnum::SHIPMENTS]['count'] ?? 0, // 待收货
        ];
    }

    /**
     * 自动判断售后状态
     *
     * @param $id
     * @return void
     */
    public function autoUpdateAfterSale($id)
    {
        $count = Yii::$app->tinyShopService->orderProduct->findAfterSaleCountByOrderId($id);
        $count == 0 && $this->updateAfterSale($id, StatusEnum::DISABLED);
    }

    /**
     * @param $id
     * @param $status
     * @return void
     */
    public function updateAfterSale($id, $status)
    {
        Order::updateAll(['is_after_sale' => $status], ['id' => $id]);
    }

    /**
     * 待评价
     *
     * @param $member_id
     * @return false|int|string|null
     */
    public function remainToBeEvaluated($member_id)
    {
        return Order::find()
            ->select(['count(id) as count'])
            ->where(['status' => StatusEnum::ENABLED])
            ->andWhere(['buyer_id' => $member_id])
            ->andWhere(['is_evaluate' => StatusEnum::DISABLED])
            ->andWhere(['in', 'order_status', [OrderStatusEnum::SING, OrderStatusEnum::ACCOMPLISH]])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->scalar();
    }

    /**
     * 待评价数量
     *
     * @param int $limit
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findEvaluateData($limit = 20)
    {
        return Order::find()
            ->where([
                'order_status' => OrderStatusEnum::ACCOMPLISH,
                'is_evaluate' => StatusEnum::DISABLED
            ])
            ->andWhere(['>', 'auto_evaluate_time', 0])
            ->andWhere(['<=', 'auto_evaluate_time', time()])
            ->with(['product'])
            ->limit($limit)
            ->all();
    }

    /**
     * 查找拼团记录
     *
     * @param $wholesale_id
     * @param $member_id
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findByWholesaleRecordId($wholesale_record_id, $member_id)
    {
        return Order::find()
            ->where([
                'wholesale_record_id' => $wholesale_record_id,
                'buyer_id' => $member_id,
                'status' => StatusEnum::ENABLED
            ])
            ->orderBy('id desc')
            ->one();
    }

    /**
     * @return int|string
     */
    public function findCount()
    {
        return Order::find()
            ->select('id')
            ->where(['status' => StatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->andFilterWhere(['store_id' => Yii::$app->params['store_id']])
            ->count();
    }


    /**
     * 获取后台售后数量
     *
     * @param string $member_id
     * @return false|string|null
     */
    public function findAfterSaleCount()
    {
        return Order::find()
            ->select(['count(id) as count'])
            ->andWhere(['is_after_sale' => StatusEnum::ENABLED])
            ->andWhere(['status' => StatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->andFilterWhere(['store_id' => Yii::$app->params['store_id']])
            ->scalar();
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
        $model = $this->findById($id);
        if (!$model) {
            throw new UnprocessableEntityHttpException('订单不存在');
        }

        if ($member_id && $member_id != $model['buyer_id']) {
            throw new UnprocessableEntityHttpException('权限不足');
        }

        return $model;
    }

    /**
     * 多订单唯一ID
     *
     * @param $unite_no
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findByUniteNo($unite_no)
    {
        return Order::find()
            ->where(['unite_no' => $unite_no, 'status' => StatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
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
            ->one();
    }

    /**
     * @param $order_id
     * @return array|null|\yii\db\ActiveRecord|Order
     */
    public function findById($order_id)
    {
        return Order::find()
            ->where(['id' => $order_id, 'status' => StatusEnum::ENABLED])
            ->one();
    }
}
