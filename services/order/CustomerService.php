<?php

namespace addons\TinyShop\services\order;

use Yii;
use yii\web\UnprocessableEntityHttpException;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use common\helpers\AddonHelper;
use common\components\Service;
use addons\TinyShop\common\models\order\Order;
use addons\TinyShop\common\enums\RefundTypeEnum;
use addons\TinyShop\common\models\forms\CustomerRefundForm;
use addons\TinyShop\common\enums\RefundStatusEnum;
use addons\TinyShop\common\models\forms\RefundForm;
use addons\TinyShop\common\models\order\OrderProduct;
use addons\TinyShop\common\models\order\Customer;
use addons\TinyShop\common\enums\OrderStatusEnum;

/**
 * 售后
 *
 * Class CustomerService
 * @package addons\TinyShop\services\order
 * @author jianyan74 <751393839@qq.com>
 */
class CustomerService extends Service
{
    /**
     * 退货/退款申请
     *
     * @param CustomerRefundForm $refundForm
     * @param $member_id
     * @return Customer|void
     * @throws UnprocessableEntityHttpException
     */
    public function refundApply(CustomerRefundForm $refundForm, $member_id)
    {
        $orderProduct = Yii::$app->tinyShopService->orderProduct->findById($refundForm->id);
        if (!$orderProduct) {
            throw new UnprocessableEntityHttpException('订单产品不存在');
        }

        if ($orderProduct->refund_status > 0 || $orderProduct->order_status != OrderStatusEnum::ACCOMPLISH) {
            throw new UnprocessableEntityHttpException('不可申请售后');
        }

        if ($member_id && $member_id != $orderProduct->member_id) {
            throw new UnprocessableEntityHttpException('权限不足');
        }

        /** @var Order $order */
        $order = $orderProduct->order;
        $config = AddonHelper::getConfig();
        $after_sale = $config['after_sale_date'] ?? '';
        if (!empty($after_sale) && ($order->finish_time + $after_sale * 60 * 60 * 24) < time()) {
            throw new UnprocessableEntityHttpException('可售后时间已过，不可申请');
        }

        /** @var Customer $latest */
        $model = $this->findByOrderProductId($refundForm->id, $orderProduct->member_id);
        if ($model) {
            if ($model->refund_status == RefundStatusEnum::NO_PASS_ALWAYS) {
                throw new UnprocessableEntityHttpException('申请已经被取消，不能再次申请');
            }

            if ($model->refund_status == RefundStatusEnum::CANCEL) {
                throw new UnprocessableEntityHttpException('申请已关闭不可再次申请');
            }

            if ($model->refund_status != RefundStatusEnum::NO_PASS) {
                throw new UnprocessableEntityHttpException('申请处理中');
            }
        } else {
            $model = new Customer();
            $model->attributes = ArrayHelper::toArray($orderProduct);
            $model->merchant_id = $orderProduct['merchant_id'];
            $model->order_product_id = $orderProduct['id'];
        }

        $model->refund_type = $refundForm->refund_type;
        $model->refund_reason = $refundForm->refund_reason;
        $model->shipping_type = $order->shipping_type;
        $model->order_sn = $order->order_sn;
        $model->payment_type = $order->payment_type;
        $model->receiver_name = $order->receiver_name;
        $model->receiver_province = $order->receiver_province;
        $model->receiver_city = $order->receiver_city;
        $model->receiver_area = $order->receiver_area;
        $model->receiver_address = $order->receiver_address;
        $model->receiver_region_name = $order->receiver_region_name;
        $model->receiver_mobile = $order->receiver_mobile;
        $model->user_name = $order->user_name;
        $model->refund_status = RefundStatusEnum::APPLY;
        if (!$model->save()) {
            throw new UnprocessableEntityHttpException($this->getError($model));
        }

        // 售后状态修改
        OrderProduct::updateAll(['is_customer' => StatusEnum::ENABLED], ['id' => $model->order_product_id]);

        return $model;
    }

    /**
     * 同意退款申请
     *
     * @param $id
     * @return Order|array|\yii\db\ActiveRecord|null
     * @throws UnprocessableEntityHttpException
     */
    public function refundPass($id)
    {
        $model = $this->findById($id);
        // 退货、退款
        $model->refund_status = RefundStatusEnum::SALES_RETURN;
        // 仅退款
        if ($model->refund_type == RefundTypeEnum::MONEY) {
            $model->refund_status = RefundStatusEnum::AFFIRM_RETURN_MONEY;
        }

        if (!$model->save()) {
            throw new UnprocessableEntityHttpException($this->getError($model));
        }

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
    public function refundNoPass($id, $always)
    {
        $model = $this->findById($id);

        if ($model->refund_status != RefundStatusEnum::APPLY) {
            throw new UnprocessableEntityHttpException('操作失败,未申请退款或已被处理');
        }

        $model->refund_status = $always == true ? RefundStatusEnum::NO_PASS_ALWAYS : RefundStatusEnum::NO_PASS;
        if (!$model->save()) {
            throw new UnprocessableEntityHttpException($this->getError($model));
        }

        return $model;
    }

    /**
     * 退货提交
     *
     * @param RefundForm|CustomerRefundForm $refundForm
     * @param $member_id
     * @throws UnprocessableEntityHttpException
     */
    public function refundSalesReturn($refundForm, $member_id)
    {
        $model = $this->findByIdAndVerify($refundForm->id, $member_id);
        $model->refund_shipping_code = $refundForm->refund_shipping_code;
        $model->refund_shipping_company = $refundForm->refund_shipping_company;

        if ($model->refund_type != RefundTypeEnum::MONEY_AND_PRODUCT) {
            throw new UnprocessableEntityHttpException('未申请退货退款');
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
    }

    /**
     * 关闭退款/退货申请
     *
     * @param $id
     * @param $member_id
     * @throws UnprocessableEntityHttpException
     */
    public function refundClose($id, $member_id)
    {
        $model = $this->findByIdAndVerify($id, $member_id);

        if ($model->refund_status == 0) {
            throw new UnprocessableEntityHttpException('未申请退款');
        }

        if ($model->refund_status == RefundStatusEnum::CONSENT) {
            throw new UnprocessableEntityHttpException('已经关闭成功');
        }

        $model->refund_status = RefundStatusEnum::CANCEL;
        if (!$model->save()) {
            throw new UnprocessableEntityHttpException($this->getError($model));
        }
    }

    /**
     * 确认收货
     *
     * @param $id
     * @throws UnprocessableEntityHttpException
     */
    public function refundDelivery($id)
    {
        $model = Customer::find()
            ->where(['id' => $id, 'status' => StatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->one();

        if (!$model) {
            throw new UnprocessableEntityHttpException('申请售后记录不存在');
        }

        if ($model->refund_status != RefundStatusEnum::AFFIRM_SALES_RETURN) {
            throw new UnprocessableEntityHttpException('确认收货失败，已经被处理');
        }

        $model->refund_status = RefundStatusEnum::AFFIRM_RETURN_MONEY;
        if (!$model->save()) {
            throw new UnprocessableEntityHttpException($this->getError($model));
        }

        return $model;
    }

    /**
     * 确认退款
     *
     * @param $id
     * @return Customer
     * @throws UnprocessableEntityHttpException
     * @throws \yii\web\NotFoundHttpException
     */
    public function refundReturnMoney($id)
    {
        /** @var Customer $model */
        $model = $this->findById($id);
        /** @var Order $order */
        $order = $model->order;

        // 实际退款金额
        $model->refund_balance_money = Yii::$app->tinyShopService->orderProduct->getRefundBalanceMoney($order, $model);

        // 退款确认
        if ($model->refund_status != RefundStatusEnum::AFFIRM_RETURN_MONEY) {
            throw new UnprocessableEntityHttpException('操作失败,用户已关闭或已处理');
        }

        $model->refund_status = RefundStatusEnum::CONSENT;
        if (!$model->save()) {
            throw new UnprocessableEntityHttpException($this->getError($model));
        }

        // 增加本身订单退款金额
        Order::updateAllCounters(['refund_balance_money' => $model->refund_balance_money], ['id' => $order->id]);

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
        return Customer::find()
            ->where(['order_product_id' => $order_product_id, 'status' => StatusEnum::ENABLED])
            ->andFilterWhere(['member_id' => $member_id])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->one();
    }

    /**
     * @param $order_product_id
     * @param $member_id
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findByOrderProductIds($order_product_ids, $member_id)
    {
        return Customer::find()
            ->where(['status' => StatusEnum::ENABLED])
            ->andWhere(['in', 'order_product_id', $order_product_ids])
            ->andFilterWhere(['member_id' => $member_id])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->asArray()
            ->all();
    }

    /**
     * 获取记录
     *
     * @param $order_product_id
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findById($id)
    {
        return Customer::find()
            ->where(['id' => $id, 'status' => StatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->one();
    }
}