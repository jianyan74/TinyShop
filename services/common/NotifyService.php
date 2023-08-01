<?php

namespace addons\TinyShop\services\common;

use Yii;
use common\helpers\ArrayHelper;
use common\components\Service;
use common\enums\NotifyTypeEnum;
use common\enums\MemberTypeEnum;
use common\enums\StatusEnum;
use common\enums\PayTypeEnum;
use common\enums\PayStatusEnum;
use addons\TinyShop\common\models\common\Notify;
use addons\TinyShop\common\models\common\NotifyMember;
use addons\TinyShop\common\enums\SubscriptionActionEnum;
use addons\TinyShop\common\enums\OrderStatusEnum;
use addons\TinyShop\common\enums\OrderTypeEnum;
use addons\TinyShop\common\enums\AccessTokenGroupEnum;
use addons\TinyShop\common\enums\MarketingEnum;
use addons\TinyShop\common\enums\ShippingTypeEnum;
use addons\TinyShop\common\enums\RangeTypeEnum;
use addons\TinyShop\common\enums\DiscountTypeEnum;
use addons\TinyShop\common\enums\CouponGetTypeEnum;
use addons\TinyShop\common\enums\RefundStatusEnum;

/**
 * Class NotifyService
 * @package addons\TinyShop\services\common
 * @author jianyan74 <751393839@qq.com>
 */
class NotifyService extends Service
{
    /**
     * 解析数据
     *
     * @param $data
     * @return array|array[]|object|object[]|string|string[]
     * @throws \yii\base\InvalidConfigException
     */
    public function analysisData($data)
    {
        $data = ArrayHelper::toArray($data);
        // 订单
        if (isset($data['order']) && !empty($data['order'])) {
            $order = $data['order'];
            $order['order_status'] = OrderStatusEnum::getValue($order['order_status']);
            $order['pay_type'] = PayTypeEnum::getValue($order['pay_type']);
            $order['shipping_type'] = ShippingTypeEnum::getValue($order['shipping_type']);
            $order['order_type'] = OrderTypeEnum::getValue($order['order_type']);
            $order['order_from'] = AccessTokenGroupEnum::relevance($order['order_from']);
            $order['marketing_type'] = MarketingEnum::getValue($order['marketing_type']);
            $order['pay_status'] = PayStatusEnum::getValue($order['pay_status']);
            $order['pay_time'] = Yii::$app->formatter->asDatetime($order['pay_time']);
            $order['receiving_time'] = Yii::$app->formatter->asDatetime($order['receiving_time']);
            $order['shipping_time'] = Yii::$app->formatter->asDatetime($order['shipping_time']);
            $order['sign_time'] = Yii::$app->formatter->asDatetime($order['sign_time']);
            $order['consign_time'] = Yii::$app->formatter->asDatetime($order['consign_time']);
            $order['finish_time'] = Yii::$app->formatter->asDatetime($order['finish_time']);
            $order['close_time'] = Yii::$app->formatter->asDatetime($order['close_time']);
            $order['created_at'] = Yii::$app->formatter->asDatetime($order['created_at']);
            $order['updated_at'] = Yii::$app->formatter->asDatetime($order['updated_at']);
            $data['order'] = $order;
        }

        // 订单产品
        if (isset($data['orderProduct']) && !empty($data['orderProduct'])) {
            $orderProduct = $data['orderProduct'];
            $data['orderProduct'] = $orderProduct;
        }

        // 优惠券
        if (isset($data['couponType']) && !empty($data['couponType'])) {
            $couponType = $data['couponType'];
            $couponType['get_type'] = CouponGetTypeEnum::getValue($couponType['get_type']);
            $couponType['discount_type'] = DiscountTypeEnum::getValue($couponType['discount_type']);
            $couponType['range_type'] = RangeTypeEnum::getValue($couponType['range_type']);
            $couponType['created_at'] = Yii::$app->formatter->asDatetime($couponType['created_at']);
            $couponType['updated_at'] = Yii::$app->formatter->asDatetime($couponType['updated_at']);
            $data['couponType'] = $couponType;
        }

        // 砍价
        if (isset($data['launch']) && !empty($data['launch'])) {
            $launch = $data['launch'];
            $data['launch'] = $launch;
        }

        // 好友砍价
        if (isset($data['bargainPartake']) && !empty($data['bargainPartake'])) {
            $bargainPartake = $data['bargainPartake'];
            $data['bargainPartake'] = $bargainPartake;
        }

        // 开团
        if (isset($data['wholesale']) && !empty($data['wholesale'])) {
            $wholesale = $data['wholesale'];
            $data['wholesale'] = $wholesale;
        }

        // 售后
        if (isset($data['afterSale']) && !empty($data['afterSale'])) {
            $afterSale = $data['afterSale'];
            $afterSale['get_type'] = RefundStatusEnum::getValue($afterSale['refund_status']);
            $data['afterSale'] = $afterSale;
        }


        $data['time'] = Yii::$app->formatter->asDatetime(time());
        $data['ip'] = Yii::$app->services->base->getUserIp();

        return $data;
    }

    /**
     * 创建提醒
     *
     * @param int $target_id 触发id
     * @param string $targetType 触发类型
     * @param string $action 提醒关联动作
     * @param int $sender_id 发送者(用户)id
     * @param array $data 内容
     */
    public function createRemind($targetId, $targetType, $merchant_id, $data = [])
    {
        $data = $this->analysisData($data);
        $defaultMerchantId = Yii::$app->services->merchant->getId();
        if (Yii::$app->services->devPattern->isB2C()) {
            $auth = Yii::$app->services->memberAuth->findByMemberType(0, MemberTypeEnum::MANAGER);
        } elseif (Yii::$app->services->devPattern->isB2B2C()) {
            $auth = Yii::$app->services->memberAuth->findByMemberType($merchant_id, MemberTypeEnum::MERCHANT);
        } else {
            $auth = Yii::$app->services->memberAuth->findByMemberType($merchant_id, MemberTypeEnum::MERCHANT);
        }

        // 配置
        $notifyConfigs = Yii::$app->services->notifyConfig->findByName($targetType, 0, 'TinyShop');
        Yii::$app->services->notifyConfig->send($notifyConfigs, $auth, $targetId, $targetType, $data);
        Yii::$app->services->merchant->setId($defaultMerchantId);
    }

    /**
     * 创建提醒
     *
     * @param $target_id
     * @param $targetType
     * @param $receiver_id
     * @param array $data
     * @return bool
     */
    public function createRemindByReceiver($target_id, $targetType, $receiver_id, $data = [])
    {
        $data = $this->analysisData($data);
        $defaultMerchantId = Yii::$app->services->merchant->getAutoId();
        $sysConfig = Yii::$app->services->notifyConfig->findSysByName($targetType, 0, 'TinyShop');
        if (empty($sysConfig->content)) {
            $sysConfig->attributes = SubscriptionActionEnum::default($targetType);
        }

        // 创建系统提醒
        $model = new Notify();
        $model->target_id = $target_id;
        $model->target_type = $targetType;
        $model->action = $targetType;
        $model->sender_id = 0;
        $model->type = NotifyTypeEnum::REMIND;
        $model->title = SubscriptionActionEnum::getValue($targetType);
        $model->content = ArrayHelper::recursionGetVal($sysConfig->content, $data);
        if ($model->save()) {
            $notifyMember = new NotifyMember();
            $notifyMember->notify_id = $model->id;
            $notifyMember->member_id = $receiver_id;
            $notifyMember->type = NotifyTypeEnum::REMIND;
            $notifyMember->save();
        }

        // 未开启通知
        $memberConfig = Yii::$app->tinyShopService->notifySubscriptionConfig->findByMemberId($receiver_id, Yii::$app->services->merchant->getNotNullId());
        if ($memberConfig['action']['all'] == StatusEnum::DISABLED) {
            return false;
        }

        // 用户授权
        $auth = Yii::$app->services->memberAuth->findByMemberId($receiver_id);
        // 配置
        $notifyConfigs = Yii::$app->services->notifyConfig->findByName($targetType, 0, 'TinyShop');

        Yii::$app->services->notifyConfig->send($notifyConfigs, $auth, $target_id, $targetType, $data);
        Yii::$app->services->merchant->setId($defaultMerchantId);
    }

    /**
     * 创建一条信息(私信)
     *
     * @param int $sender_id 触发id
     * @param string $content 内容
     * @param int $receiver 接收id
     */
    public function createMessage($content, $sender_id, $receiver)
    {
        $model = new Notify();
        $model->content = $content;
        $model->sender_id = $sender_id;
        $model->type = NotifyTypeEnum::MESSAGE;
        if ($model->save()) {
            $NotifyMember = new NotifyMember();
            $NotifyMember->notify_id = $model->id;
            $NotifyMember->member_id = $receiver;
            $NotifyMember->type = NotifyTypeEnum::MESSAGE;

            return $NotifyMember->save();
        }

        return false;
    }

    /**
     * 创建公告
     *
     * @param $title
     * @param $status
     * @param $sender_id
     * @return bool
     */
    public function createAnnounce($title, $status, $sender_id)
    {
        $model = Notify::find()
            ->where([
                'sender_id' => $sender_id,
                'type' => NotifyTypeEnum::ANNOUNCE,
            ])
            ->one();

        if (empty($model)) {
            $model = new Notify();
            $model = $model->loadDefaultValues();
            $model->type = NotifyTypeEnum::ANNOUNCE;
            $model->sender_id = $sender_id;
        }

        $model->title = $title;
        $model->status = $status;

        return $model->save();
    }
}
