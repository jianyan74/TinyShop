<?php

namespace addons\TinyShop\services\order;

use Yii;
use yii\helpers\Json;
use yii\web\UnprocessableEntityHttpException;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use common\components\Service;
use common\helpers\StringHelper;
use addons\TinyShop\common\models\order\Order;
use addons\TinyShop\common\enums\RefundTypeEnum;
use addons\TinyShop\common\forms\OrderAfterSaleForm;
use addons\TinyShop\common\enums\RefundStatusEnum;
use addons\TinyShop\common\models\order\AfterSale;
use addons\TinyShop\common\enums\OrderStatusEnum;
use addons\TinyShop\common\enums\SubscriptionActionEnum;
use addons\TinyShop\common\enums\OrderAfterSaleTypeEnum;
use addons\TinyShop\common\models\order\OrderProduct;

/**
 * 售后
 *
 * Class AfterSaleService
 * @package addons\TinyShop\services\order
 * @author jianyan74 <751393839@qq.com>
 */
class AfterSaleService extends Service
{
    /**
     * 退货/退款申请
     *
     * @param OrderAfterSaleForm $refundForm
     * @param $member_id
     * @return AfterSale|void
     * @throws UnprocessableEntityHttpException
     */
    public function apply(OrderAfterSaleForm $refundForm, $member_id)
    {
        $orderProduct = Yii::$app->tinyShopService->orderProduct->findById($refundForm->id);
        if (!$orderProduct) {
            throw new UnprocessableEntityHttpException('订单产品不存在');
        }

        if (in_array($orderProduct->refund_status, RefundStatusEnum::refund())) {
            throw new UnprocessableEntityHttpException('售后已经在处理中');
        }

        if ($orderProduct->refund_status == RefundStatusEnum::NO_PASS_ALWAYS) {
            throw new UnprocessableEntityHttpException('退款已拒绝，不能再次申请');
        }

        if ($orderProduct->refund_status == RefundStatusEnum::CANCEL) {
            throw new UnprocessableEntityHttpException('申请已关闭不可再次申请');
        }

        if ($member_id && $member_id != $orderProduct->buyer_id) {
            throw new UnprocessableEntityHttpException('权限不足');
        }

        if (
            in_array($orderProduct->order_status, [OrderStatusEnum::NOT_PAY, OrderStatusEnum::PAY]) &&
            $refundForm->refund_type == RefundTypeEnum::EXCHANGE_PRODUCT
        ) {
            throw new UnprocessableEntityHttpException('未发货不允许申请换货');
        }

        /** @var Order $order */
        $order = $orderProduct->order;
        /** @var AfterSale $model */
        $model = new AfterSale();
        $model->attributes = ArrayHelper::toArray($orderProduct);
        $model->merchant_id = $orderProduct['merchant_id'];
        $model->order_product_id = $orderProduct['id'];
        $model->type = $orderProduct->order_status != OrderStatusEnum::ACCOMPLISH ? OrderAfterSaleTypeEnum::IN_SALE : OrderAfterSaleTypeEnum::AFTER_SALE ;
        $model->number = $orderProduct->num;
        $model->refund_type = $refundForm->refund_type;
        $model->refund_reason = $refundForm->refund_reason;
        $model->refund_evidence = $refundForm->refund_evidence;
        $model->refund_apply_money = $refundForm->refund_apply_money;
        $model->refund_evidence = is_array($refundForm->refund_evidence) ? $refundForm->refund_evidence : Json::decode($refundForm->refund_evidence);
        $model->order_sn = $order->order_sn;
        $model->store_id = $order->store_id;
        $model->buyer_id = $order->buyer_id;
        $model->buyer_nickname = $order->buyer_nickname;
        $model->refund_status = RefundStatusEnum::APPLY;
        if (!$model->save()) {
            throw new UnprocessableEntityHttpException($this->getError($model));
        }

        // 售后时间判断
        if ($model->type == OrderAfterSaleTypeEnum::AFTER_SALE) {
            $config = Yii::$app->tinyShopService->config->setting();
            if (!empty($config->order_after_sale_date) && ($order->finish_time + $config->order_after_sale_date * 60 * 60 * 24) < time()) {
                 throw new UnprocessableEntityHttpException('可售后时间已过，不可申请');
            }
        }

        // 记录操作
        Yii::$app->services->actionLog->create('orderAfterSale', '申请退款', $model->id);

        // 售后状态
        Yii::$app->tinyShopService->order->updateAfterSale($order->id, StatusEnum::ENABLED);

        // 订单退款申请提醒
        Yii::$app->tinyShopService->notify->createRemind(
            $model->order_id,
            SubscriptionActionEnum::ORDER_AFTER_SALE_APPLY,
            $model->merchant_id,
            ['order' => $order]
        );

        return $model;
    }

    /**
     * 同意退款申请
     *
     * @param $id
     * @return Order|array|\yii\db\ActiveRecord|null
     * @throws UnprocessableEntityHttpException
     */
    public function pass($id)
    {
        $model = $this->findById($id);
        // 仅退款
        if ($model->refund_type == RefundTypeEnum::MONEY) {
            $model->refund_status = RefundStatusEnum::AFFIRM_RETURN_MONEY;
        } else {
            // 退货、退款 / 换货
            $model->refund_status = RefundStatusEnum::SALES_RETURN;
            // 退款 / 换货通知
            /** @var OrderProduct $orderProduct */
            $orderProduct = $model->orderProduct;
            $orderProduct->product_name = StringHelper::textNewLine($orderProduct->product_name, 15, 1)[0]; // 内容过长无法通知
            Yii::$app->tinyShopService->notify->createRemindByReceiver(
                $model->order_id,
                SubscriptionActionEnum::ORDER_RETURN_MEMBER_DELIVER,
                $model->buyer_id,
                [
                    'afterSale' => $model,
                    'orderProduct' => $orderProduct,
                ]
            );
        }

        if (!$model->save()) {
            throw new UnprocessableEntityHttpException($this->getError($model));
        }

        // 记录行为
        Yii::$app->services->actionLog->create('orderAfterSale', '同意退款申请', $model->id);

        return $model;
    }

    /**
     * 拒绝退款申请
     *
     * @param $id
     * @param $always
     * @return Order|array|\yii\db\ActiveRecord|null
     * @throws UnprocessableEntityHttpException
     */
    public function refuse($id, $always)
    {
        $model = $this->findById($id);

        if ($model->refund_status != RefundStatusEnum::APPLY) {
            throw new UnprocessableEntityHttpException('操作失败,未申请退款或已被处理');
        }

        $model->refund_status = $always == true ? RefundStatusEnum::NO_PASS_ALWAYS : RefundStatusEnum::NO_PASS;
        if (!$model->save()) {
            throw new UnprocessableEntityHttpException($this->getError($model));
        }

        // 记录行为
        Yii::$app->services->actionLog->create('orderAfterSale', '拒绝退款申请', $model->id);

        // 自动处理订单状态
        Yii::$app->tinyShopService->order->autoUpdateAfterSale($model->order_id);

        return $model;
    }

    /**
     * 退货提交
     *
     * @param OrderAfterSaleForm $refundForm
     * @param $member_id
     * @throws UnprocessableEntityHttpException
     */
    public function salesReturn($refundForm, $member_id)
    {
        $model = $this->findByIdAndVerify($refundForm->id, $member_id);
        $model->member_express_company = $refundForm->member_express_company;
        $model->member_express_no = $refundForm->member_express_no;
        $model->member_express_mobile = $refundForm->member_express_mobile;
        $model->member_express_time = time();
        // 未填写发货手机号码
        if (empty($model->member_express_mobile)) {
            $model->member_express_mobile = $model->order->receiver_mobile ?? '';
        }

        if (!in_array($model->refund_type, [RefundTypeEnum::MONEY_AND_PRODUCT, RefundTypeEnum::EXCHANGE_PRODUCT])) {
            throw new UnprocessableEntityHttpException('未申请退货退款/换货');
        }

        if ($model->refund_status == RefundStatusEnum::AFFIRM_SALES_RETURN) {
            throw new UnprocessableEntityHttpException('已经提交退货申请');
        }

        if ($model->refund_status != RefundStatusEnum::SALES_RETURN) {
            throw new UnprocessableEntityHttpException('操作失败,已经已被处理');
        }

        $model->refund_status = RefundStatusEnum::AFFIRM_SALES_RETURN;
        if (!$model->save()) {
            throw new UnprocessableEntityHttpException($this->getError($model));
        }

        // 记录行为
        Yii::$app->services->actionLog->create('orderAfterSale', '退货提交', $model->id);

        return $model;
    }

    /**
     * 关闭退款/退货申请
     *
     * @param $id
     * @param $member_id
     * @throws UnprocessableEntityHttpException
     */
    public function close($id, $member_id)
    {
        $model = $this->findByIdAndVerify($id, $member_id);

        if ($model->refund_status == 0) {
            throw new UnprocessableEntityHttpException('未申请退款');
        }

        if ($model->refund_status == RefundStatusEnum::CANCEL) {
            throw new UnprocessableEntityHttpException('申请已关闭');
        }

        if ($model->refund_status == RefundStatusEnum::SHIPMENTS) {
            throw new UnprocessableEntityHttpException('卖家已发货');
        }

        if ($model->refund_status == RefundStatusEnum::CONSENT) {
            throw new UnprocessableEntityHttpException('卖家已同意退款');
        }

        $model->refund_status = RefundStatusEnum::CANCEL;
        if (!$model->save()) {
            throw new UnprocessableEntityHttpException($this->getError($model));
        }

        // 记录行为
        Yii::$app->services->actionLog->create('orderAfterSale', '关闭退款/退货申请', $model->id);

        // 自动处理订单状态
        Yii::$app->tinyShopService->order->autoUpdateAfterSale($model->order_id);

        return $model;
    }

    /**
     * 确认收货
     *
     * @param $id
     * @throws UnprocessableEntityHttpException
     */
    public function merchantTakeDelivery($id)
    {
        $model = $this->findById($id);
        if (!$model) {
            throw new UnprocessableEntityHttpException('申请售后记录不存在');
        }

        if ($model->refund_status != RefundStatusEnum::AFFIRM_SALES_RETURN) {
            throw new UnprocessableEntityHttpException('确认收货失败，已经被处理');
        }

        $model->refund_status = RefundStatusEnum::AFFIRM_RETURN_MONEY;
        // 换货
        if ($model->refund_type == RefundTypeEnum::EXCHANGE_PRODUCT) {
            $model->refund_status = RefundStatusEnum::AFFIRM_SHIPMENTS;
        }

        if (!$model->save()) {
            throw new UnprocessableEntityHttpException($this->getError($model));
        }

        Yii::$app->services->actionLog->create('orderAfterSale', '确认收货', $model->id);

        return $model;
    }

    /**
     * 换货, 商家发货
     *
     * @param AfterSale $afterSale
     * @return AfterSale
     * @throws UnprocessableEntityHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function merchantDelivery(AfterSale $afterSale)
    {
        if ($afterSale->refund_status != RefundStatusEnum::AFFIRM_SHIPMENTS) {
            throw new UnprocessableEntityHttpException('操作失败, 已被处理');
        }

        $afterSale->merchant_express_time = time();
        $afterSale->refund_status = RefundStatusEnum::SHIPMENTS;
        if (!$afterSale->save()) {
            throw new UnprocessableEntityHttpException($this->getError($afterSale));
        }

        // 记录行为
        Yii::$app->services->actionLog->create('orderAfterSale', '换货, 商家发货', $afterSale->id);

        return $afterSale;
    }

    /**
     * 用户确认收货
     *
     * @param $id
     * @throws UnprocessableEntityHttpException
     */
    public function memberTakeDelivery($id, $member_id)
    {
        $model = $this->findByIdAndVerify($id, $member_id);
        if ($model->refund_status != RefundStatusEnum::SHIPMENTS) {
            throw new UnprocessableEntityHttpException('操作失败, 已被处理');
        }

        $model->refund_status = RefundStatusEnum::MEMBER_AFFIRM;
        if (!$model->save()) {
            throw new UnprocessableEntityHttpException($this->getError($model));
        }

        Yii::$app->services->actionLog->create('orderAfterSale', '确认收货', $model->id);

        // 售后状态
        Yii::$app->tinyShopService->order->updateAfterSale($model->order_id, StatusEnum::ENABLED);

        return $model;
    }

    /**
     * 确认退款
     *
     * @param $id
     * @return AfterSale
     * @throws UnprocessableEntityHttpException
     * @throws \yii\web\NotFoundHttpException
     */
    public function returnMoney($id, $refund_money)
    {
        /** @var AfterSale $model */
        $model = $this->findById($id);
        /** @var Order $order */
        $order = $model->order;
        // 实际退款金额
        $model->refund_money = $refund_money;
        $model->refund_time = time();
        // 退款确认
        if ($model->refund_status != RefundStatusEnum::AFFIRM_RETURN_MONEY) {
            throw new UnprocessableEntityHttpException('操作失败,用户已关闭或已处理');
        }

        $model->refund_status = RefundStatusEnum::CONSENT;
        if (!$model->save()) {
            throw new UnprocessableEntityHttpException($this->getError($model));
        }

        // 退款为 0
        if ($model->refund_money > 0) {
            // 增加本身订单退款金额
            Order::updateAllCounters(['refund_money' => $model->refund_money], ['id' => $order->id]);

            // 订单退款提醒
            Yii::$app->tinyShopService->notify->createRemindByReceiver(
                $order->id,
                SubscriptionActionEnum::ORDER_RETURN_MONEY,
                $order->buyer_id,
                ['order' => $order]
            );

            // 订单退款提醒
            Yii::$app->tinyShopService->notify->createRemind(
                $order->id,
                SubscriptionActionEnum::ORDER_RETURN_MONEY,
                $order->merchant_id,
                ['order' => $order]
            );
        }

        // 记录行为
        Yii::$app->services->actionLog->create('orderAfterSale', '确认退款', $model->id);

        return $model;
    }

    /**
     * @param $order_product_id
     * @param string $member_id
     * @return array|\yii\db\ActiveRecord|null
     * @throws UnprocessableEntityHttpException
     */
    public function findByIdAndVerify($order_product_id, $member_id = '')
    {
        $model = $this->findByOrderProductId($order_product_id, $member_id);
        if (!$model) {
            throw new UnprocessableEntityHttpException('申请售后记录不存在');
        }

        return $model;
    }

    /**
     * @param $order_product_id
     * @param $member_id
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findByOrderProductId($order_product_id, $member_id)
    {
        return AfterSale::find()
            ->where(['order_product_id' => $order_product_id, 'status' => StatusEnum::ENABLED])
            ->andFilterWhere(['buyer_id' => $member_id])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->orderBy('id desc')
            ->one();
    }

    /**
     * 获取记录
     *
     * @param $order_product_id
     * @return array|\yii\db\ActiveRecord|null|AfterSale
     */
    public function findById($id)
    {
        return AfterSale::find()
            ->where(['id' => $id, 'status' => StatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->one();
    }
}
