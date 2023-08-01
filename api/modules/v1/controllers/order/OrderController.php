<?php

namespace addons\TinyShop\api\modules\v1\controllers\order;

use Yii;
use yii\helpers\Json;
use yii\web\UnprocessableEntityHttpException;
use common\forms\CreditsLogForm;
use common\helpers\ResultHelper;
use common\models\member\Account;
use common\models\member\Member;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use common\helpers\BcHelper;
use common\helpers\StringHelper;
use common\models\member\Address;
use api\controllers\OnAuthController;
use addons\TinyShop\common\models\order\Order;
use addons\TinyShop\common\forms\PreviewForm;
use addons\TinyShop\common\components\InitOrderData;
use addons\TinyShop\common\components\PreviewHandler;
use addons\TinyShop\common\components\marketing\FreightHandler;
use addons\TinyShop\common\components\marketing\FullMailHandler;
use addons\TinyShop\common\components\marketing\CouponHandler;
use addons\TinyShop\common\components\marketing\AfterHandler;
use addons\TinyShop\common\enums\ShippingTypeEnum;
use addons\TinyShop\common\enums\MarketingEnum;
use addons\TinyShop\common\traits\AutoCalculatePriceTrait;
use addons\TinyShop\common\components\platform\PlatformUsePointHandler;

/**
 * 订单
 *
 * Class OrderController
 * @package addons\TinyShop\api\modules\v1\controllers\order
 * @author jianyan74 <751393839@qq.com>
 */
class OrderController extends OnAuthController
{
    use AutoCalculatePriceTrait;

    /**
     * @var Order
     */
    public $modelClass = Order::class;

    /**
     * @var array
     */
    protected $handlers = [
        FullMailHandler::class, // 满包邮
        FreightHandler::class, // 运费计算
        CouponHandler::class, // 优惠券
        AfterHandler::class, // 统一处理
    ];

    /**
     * 执行外部营销
     *
     * @var PreviewHandler
     */
    protected $previewHandler;

    /**
     * 订单预览
     *
     * @var PreviewForm
     */
    protected $previewForm;

    /**
     * @var Member
     */
    protected $member;

    /**
     * @var Account
     */
    protected $account;

    /**
     * @var Address
     */
    protected $address;

    /**
     * 唯一关联ID
     *
     * @var int
     */
    protected $uniteNo;

    /**
     * 是否查询过地址(避免重复查询)
     *
     * @var bool
     */
    protected $queryAddress = false;

    /**
     * @param $action
     * @return bool
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\BadRequestHttpException
     * @throws \yii\web\ForbiddenHttpException
     * @throws \yii\web\NotFoundHttpException
     */
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        $this->previewHandler = new PreviewHandler($this->handlers);

        return true;
    }

    /**
     * 订单预览
     *
     * @return array|mixed
     * @throws \yii\web\NotFoundHttpException
     * @throws \yii\web\UnprocessableEntityHttpException
     */
    public function actionPreview()
    {
        $member_id = Yii::$app->user->identity->member_id;
        $merchant_id = Yii::$app->services->merchant->getNotNullId();
        $this->member = Yii::$app->services->member->findById($member_id);
        $this->account = $this->member->account;
        $this->previewForm = Yii::$app->tinyShopService->orderPreview->initModel($merchant_id);

        return $this->preview(Yii::$app->request->get());
    }

    /**
     * 创建订单
     *
     * @return Order|mixed|\yii\db\ActiveRecord
     * @throws \yii\web\NotFoundHttpException
     * @throws \yii\web\UnprocessableEntityHttpException
     */
    public function actionCreate()
    {
        $member_id = Yii::$app->user->identity->member_id;
        $merchant_id = Yii::$app->services->merchant->getNotNullId();
        $this->member = Yii::$app->services->member->findById($member_id);
        $this->account = $this->member->account;

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->previewForm = Yii::$app->tinyShopService->orderPreview->initModel($merchant_id);
            $previewForm = $this->create(Yii::$app->request->post());
            $transaction->commit();

            return $previewForm;
        } catch (\Exception $e) {
            $transaction->rollBack();

            return ResultHelper::json(422, $e->getMessage());
        }
    }

    /**
     * @return array
     * @throws \yii\web\NotFoundHttpException
     * @throws \yii\web\UnprocessableEntityHttpException
     */
    public function actionItemsPreview()
    {
        $items = Yii::$app->request->get('items');
        $items = Json::decode($items);

        $member_id = Yii::$app->user->identity->member_id;
        $this->member = Yii::$app->services->member->findById($member_id);
        $this->account = $this->member->account;

        $data = [];
        $groupOrderProducts = [];
        $payMoney = 0;
        foreach ($items as $item) {
            $this->previewForm = Yii::$app->tinyShopService->orderPreview->initModel($item['merchant_id'] ?? 0);
            $row = $this->preview($item['item']);
            $payMoney = BcHelper::add($row['preview']['product_money'], $payMoney);
            $groupOrderProducts = ArrayHelper::merge($groupOrderProducts, $row['groupOrderProducts']);
            unset($row['address'], $row['account'], $row['productIds'], $row['cateIds']);

            $data[] = $row;
        }

        // 判断订单商品类型
        $productType = $data[0]['products'][0]['product_type'];
        $marketingType = $data[0]['products'][0]['marketing_type'];

        // 积分兑换商品不可用
        $pointMaxConfig = [];
        if ($marketingType != MarketingEnum::POINT_EXCHANGE) {
            $pointMaxConfig = Yii::$app->tinyShopService->marketingPointConfig->getMaxConfig($payMoney, $this->account->user_integral, true);
        }

        return [
            'address' => $this->address,
            'account' => $this->account,
            'items' => $data,
            'productType' => $productType,
            'marketingType' => $marketingType,
            'lastMemberInfo' => [],
            'coupons' => Yii::$app->tinyShopService->marketingCoupon->getUsableByMemberId($member_id, 0, $groupOrderProducts),
            'pointConfig' => [
                'status' => (int)!empty($pointMaxConfig),
                'available' => $pointMaxConfig['maxPoint'] ?? 0, // 可用积分
                'money' => $pointMaxConfig['maxMoney'] ?? 0, // 抵扣金额
            ],
        ];
    }

    /**
     * @return Order|array|mixed
     * @throws \yii\web\NotFoundHttpException
     * @throws \yii\web\UnprocessableEntityHttpException
     */
    public function actionItemsCreate()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $items = Yii::$app->request->post('items');
            $usePoint = (int)Yii::$app->request->post('use_point'); // 积分抵扣
            $items = Json::decode($items);

            $member_id = Yii::$app->user->identity->member_id;
            $this->member = Yii::$app->services->member->findById($member_id);
            $this->account = $this->member->account;

            $payMoney = 0;
            $orders = [];
            $allGroupOrderProducts = [];
            $this->uniteNo = time() . StringHelper::random(10, true);
            $previewForms = [];
            foreach ($items as $item) {
                $this->previewForm = Yii::$app->tinyShopService->orderPreview->initModel($item['merchant_id']);
                $previewForms[$item['merchant_id']] = $this->beforeCreate($item['item']);
                $payMoney = BcHelper::add($previewForms[$item['merchant_id']]['product_money'], $payMoney);
                $allGroupOrderProducts = ArrayHelper::merge($allGroupOrderProducts, $previewForms[$item['merchant_id']]->groupOrderProducts);
            }

            // 积分抵扣
            if ($usePoint > 0) {
                // 积分兑换商品不可用
                $pointMaxConfig = Yii::$app->tinyShopService->marketingPointConfig->getMaxConfig($payMoney, $this->account->user_integral, true);
                // 使用积分抵扣
                if (!empty($pointMaxConfig)) {
                    $platformMarketingDetails = (new PlatformUsePointHandler())->execute($pointMaxConfig['maxMoney'], $allGroupOrderProducts);
                    foreach ($platformMarketingDetails as $key => $platformMarketingDetail) {
                        $platformMarketingDetail[0]['discount_money'] > 0 && $previewForms[$key]->point = (int) BcHelper::div($platformMarketingDetail[0]['discount_money'], $pointMaxConfig['convertRate']);
                        $previewForms[$key]->marketingDetails = array_merge($previewForms[$key]->marketingDetails, $platformMarketingDetail);
                    }
                }
            }

            $payMoney = 0;
            foreach ($previewForms as $previewForm) {
                $order = $this->afterCreate($previewForm, $member_id);
                $payMoney = BcHelper::add($payMoney, $order->pay_money);
                $orders[] = $order;
            }

            $transaction->commit();

            return [
                'unite_no' => $this->uniteNo,
                'pay_money' => $payMoney,
                'orders' => $orders
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();

            return ResultHelper::json(422, YII_DEBUG ? Yii::$app->services->base->getErrorInfo($e) : $e->getMessage());
        }
    }

    /**
     * @param array $data
     * @return array|mixed
     * @throws \yii\web\NotFoundHttpException
     * @throws \yii\web\UnprocessableEntityHttpException
     */
    protected function preview(array $data, $formatting = true)
    {
        $member_id = Yii::$app->user->identity->member_id;
        /** @var PreviewForm $model */
        $model = $this->previewForm;
        !$model->shipping_type && $model->shipping_type = ShippingTypeEnum::LOGISTICS;
        $model->member = $this->member;
        if (
            $this->queryAddress == false &&
            $model->shipping_type == ShippingTypeEnum::LOGISTICS &&
            empty($model->address_id) &&
            empty($model->address)
        ) {
            $this->address = Yii::$app->services->memberAddress->findDefaultByMemberId($member_id); // 默认地址
            $model->address = $this->address;
            $this->queryAddress = true;
        }

        $model->attributes = $data;

        try {
            !is_array($model->data) && $model->data = Json::decode($model->data);
        } catch (\Exception $e) {
            return ResultHelper::json(422, 'Data 数据传输错误');
        }

        // 触发 - 初始化数据
        $initOrderData = new InitOrderData();
        $model = $initOrderData->execute($model, $model->type);
        !empty($model->address_id) && $model->address = Yii::$app->services->memberAddress->findById($model->address_id, $member_id);

        // 触发 - 营销
        $model = $this->previewHandler->start($model);
        if ($model->getErrors() || !$model->validate()) {
            throw new UnprocessableEntityHttpException($this->getError($model));
        }

        // 非格式化返回
        if ($formatting == false) {
            return $model;
        }

        $merchant = [];
        if (!empty($model->merchant)) {
            $merchant = [
                'id' => $model->merchant['id'],
                'title' => $model->merchant['title'],
                'cover' => $model->merchant['cover'],
            ];
        }

        return [
            'account' => $this->account,
            'address' => $model->address,
            'merchant' => $merchant,
            'preview' => ArrayHelper::toArray($model),
            'products' => $model->orderProducts,
            'groupOrderProducts' => $model->groupOrderProducts,
            'marketingFullDetails' => $model->marketingDetails,
            'marketingDetails' => Yii::$app->tinyShopService->marketing->mergeIdenticalMarketing($model->marketingDetails), // 被触发的自带营销规则
            'coupons' => Yii::$app->tinyShopService->marketingCoupon->getUsableByMemberId($member_id, $model->merchant_id, $model->groupOrderProducts),
            // 配置
            'config' => $model->config
        ];
    }

    /**
     * @param array $data
     * @return Order|array|mixed
     * @throws \yii\web\NotFoundHttpException
     * @throws \yii\web\UnprocessableEntityHttpException
     */
    protected function beforeCreate(array $data)
    {
        $member_id = Yii::$app->user->identity->member_id;
        /** @var PreviewForm $model */
        $model = $this->previewForm;
        $model->setScenario('create');
        $model->attributes = $data;
        $model->unite_no = $this->uniteNo;

        try {
            !is_array($model->data) && $model->data = Json::decode($model->data);
        } catch (\Exception $e) {
            return ResultHelper::json(422, 'Data 数据传输错误');
        }

        $model->member = $this->member;
        if (empty($model->member)) {
            throw new UnprocessableEntityHttpException('找不到用户信息');
        }

        // 触发 - 初始化数据
        $initOrderData = new InitOrderData();
        $initOrderData->isNewRecord = true;
        $model = $initOrderData->execute($model, $model->type);
        $model->address_id && $model->address = Yii::$app->services->memberAddress->findById($model->address_id, $member_id);

        // 触发 - 营销
        $model = $this->previewHandler->start($model, true);
        if ($model->getErrors() || !$model->validate()) {
            throw new UnprocessableEntityHttpException($this->getError($model));
        }

        // 订单来源
        $model->order_from = Yii::$app->user->identity->group;

        return $model;
    }

    /**
     * @param PreviewForm $model
     * @return Order
     * @throws UnprocessableEntityHttpException
     * @throws \yii\web\NotFoundHttpException
     */
    public function afterCreate(PreviewForm $model, $member_id)
    {
        $model = $this->calculatePrice($model);
        $order = Yii::$app->tinyShopService->order->create($model);

        // 消耗积分
        if ($order->point > 0) {
            Yii::$app->services->memberCreditsLog->decrInt(new CreditsLogForm([
                'account' => Yii::$app->services->memberAccount->findByMemberId($member_id),
                'num' => $order->point,
                'group' => 'orderCreate',
                'map_id' => $order->id,
                'remark' => '订单支付-' . $order->order_sn,
            ]));
        }

        // 支付金额为0 直接支付
        $order->pay_money == 0 && Yii::$app->tinyShopService->order->pay($order, $order->pay_type);

        // 删除购物车
        if ($model->type == MarketingEnum::CART && !empty($model->data)) {
            Yii::$app->tinyShopService->memberCartItem->deleteIds($model->data, $order->buyer_id);
        }

        return $order;
    }

    /**
     * @return array
     * @throws UnprocessableEntityHttpException
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionMarketing()
    {
        $usePoint = Yii::$app->request->get('use_point');
        $preview = $this->actionItemsPreview();
        $data = [
            'coupon' => 0, // 平台优惠券金额
            'pay' => 0, // 支付金额
            'point' => 0, // 所需积分
            'merchant' => [], // 商家优惠金额
        ];

        $groupOrderProducts = [];
        foreach ($preview['items'] as $item) {
            $couponMoney = $invoiceMoney = 0;
            $shippingMoney = $item['preview']['shipping_money'];
            $groupOrderProducts = ArrayHelper::merge($groupOrderProducts, $item['groupOrderProducts']);
            $marketingDetails = Yii::$app->tinyShopService->marketing->mergeIdenticalMarketing($item['marketingDetails']);
            foreach ($marketingDetails as $detail) {
                if ($detail['marketing_type'] == MarketingEnum::COUPON) {
                    $couponMoney = $detail['discount_money'];
                }
            }

            $payMoney = (float)BcHelper::add($item['preview']['product_money'], $shippingMoney);
            $data['merchant'][] = [
                'merchant_id' => $item['preview']['merchant_id'],
                'shipping' => (float)$shippingMoney,
                'coupon' => (float)$couponMoney,
                'invoice' => (float)$invoiceMoney,
                'point' => $item['preview']['point'],
                'pay' => $payMoney,
            ];

            $data['pay'] = (float)BcHelper::add($payMoney, $data['pay']);
            $data['point'] = $item['preview']['point'];
        }

        // 积分兑换商品不可用
        $pointMaxConfig = [];
        $marketingType = $preview['items'][0]['products'][0]['marketing_type'];
        if ($marketingType != MarketingEnum::POINT_EXCHANGE) {
            $pointMaxConfig = Yii::$app->tinyShopService->marketingPointConfig->getMaxConfig($payMoney, $this->account->user_integral, true);
        }

        $data['pointConfig'] = [
            'status' => (int)!empty($pointMaxConfig),
            'available' => $pointMaxConfig['maxPoint'] ?? 0, // 可用积分
            'money' => $pointMaxConfig['maxMoney'] ?? 0, // 抵扣金额
        ];

        // 使用积分抵扣
        if ($usePoint > 0 && $data['pointConfig']['status'] == StatusEnum::ENABLED) {
            $data['pay'] = BcHelper::sub($data['pay'], $pointMaxConfig['maxMoney']);
        }

        if ($data['coupon'] > 0) {
            $data['pay'] = BcHelper::sub($data['pay'], $data['coupon']);
        }

        $data['pay'] < 0 && $data['pay'] = 0;

        return $data;
    }

    /**
     * 支付详情
     *
     * @param $unite_no
     * @return array
     */
    public function actionPayInfo($unite_no)
    {
        $data = [
            'pay_money' => 0,
            'pay_time' => time(),
        ];

        $orders = Yii::$app->tinyShopService->order->findByUniteNo($unite_no);
        foreach ($orders as $order) {
            $data['pay_money'] = BcHelper::add($data['pay_money'], $order['pay_money']);
            $data['pay_time'] = $order['pay_time'];
        }

        return $data;
    }

    /**
     * 权限验证
     *
     * @param string $action 当前的方法
     * @param null $model 当前的模型类
     * @param array $params $_GET变量
     * @throws \yii\web\BadRequestHttpException
     */
    public function checkAccess($action, $model = null, $params = [])
    {
        // 方法名称
        if (in_array($action, ['view', 'delete', 'update'])) {
            throw new \yii\web\BadRequestHttpException('权限不足');
        }
    }
}
