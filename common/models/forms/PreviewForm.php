<?php

namespace addons\TinyShop\common\models\forms;

use common\enums\StatusEnum;
use Yii;
use common\helpers\ArrayHelper;
use common\models\member\Address;
use common\models\member\Member;
use addons\TinyShop\common\models\marketing\Coupon;
use addons\TinyShop\common\models\order\Order;
use addons\TinyShop\common\models\pickup\Point;
use addons\TinyShop\common\enums\PreviewTypeEnum;
use addons\TinyShop\common\enums\ShippingTypeEnum;
use addons\TinyShop\common\models\order\OrderProduct;
use addons\TinyShop\common\enums\OrderTypeEnum;

/**
 * Class PreviewForm
 * @package addons\TinyShop\common\models\forms
 * @author jianyan74 <751393839@qq.com>
 */
class PreviewForm extends Order
{
    /** ------------- 提交的必填数据 ------------- */

    /**
     * 数据
     *
     * @var
     */
    public $data;
    /**
     * 提交的类型
     * @var
     */
    public $type;

    /** ------------- 处理后的数据 ------------- */

    /**
     * 默认的系统产品
     *
     * @var array
     */
    public $defaultProducts = [];

    /**
     * 生成的订单产品
     *
     * @var array|OrderProduct
     */
    public $orderProducts = [];

    /**
     * sku数据
     *
     * @var array
     */
    public $sku = [];

    /** ------------- 地址 ------------- */

    /**
     * @var
     */
    public $address_id;
    /**
     * @var Address
     */
    public $address;

    /** ------------- 发票 ------------- */

    /**
     * 发票信息
     *
     * @var
     */
    public $invoice;

    /** ------------- 自提 ------------- */

    /**
     * 自提开启
     *
     * @var
     */
    public $buyer_self_lifting;
    /**
     * 自提id
     *
     * @var int
     */
    public $pickup_id;
    /**
     *
     * 自提对象
     *
     * @var Point
     */
    public $pickup;
    /**
     * 自提费用是否开启
     *
     * @var
     */
    public $pickup_point_is_open;

    /**
     * 自提费用
     *
     * @var
     */
    public $pickup_point_fee;
    /**
     * 自提满多少减免
     *
     * @var
     */
    public $pickup_point_freight;

    /** ------------- 用户 ------------- */

    /**
     * @var Member
     */
    public $member;
    /**
     * 包邮
     *
     * @var bool
     */
    public $is_full_mail = false;
    /**
     * 物流可选
     *
     * @var
     */
    public $is_logistics;

    /** ------------- 发票 ------------- */

    /**
     * 发票id
     *
     * @var
     */
    public $invoice_id;
    /**
     * 发票备注
     *
     * @var
     */
    public $invoice_content;
    /**
     * 发票系统可选备注
     *
     * @var
     */
    public $invoice_content_default;
    /**
     * 税率
     *
     * @var
     */
    public $order_invoice_tax;

    /** ------------- 积分 ------------- */

    /**
     * 使用积分
     *
     * @var
     */
    public $use_point;

    /**
     * 最大可用积分
     *
     * @var
     */
    public $max_use_point;

    /** ------------- 优惠券 ------------- */

    /**
     * 优惠券
     *
     * @var Coupon
     */
    public $coupon;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['data', 'type'], 'required'],
            [
                [
                    'coupon_id',
                    'company_id',
                    'address_id',
                    'pickup_id',
                    'invoice_id',
                    'shipping_type',
                    'buyer_self_lifting',
                    'pickup_point_freight',
                    'pickup_point_fee',
                    'pickup_point_is_open',
                ],
                'integer',
            ],
            [['invoice_content', 'receiver_name'], 'string'],
            [['coupon_money', 'shipping_money'], 'number'],
            ['type', 'in', 'range' => PreviewTypeEnum::getKeys()],
            ['shipping_type', 'in', 'range' => ShippingTypeEnum::getKeys()],
            [['use_point', 'company_id'], 'integer', 'min' => 0],
            [['address_id'], 'required', 'on' => ['create']],
            [['address_id'], 'addressVerify', 'on' => ['create']],
            [['use_point'], 'usePointVerify', 'on' => ['create']],
            [['invoice_id'], 'invoiceVerify', 'on' => ['create']],
            [['company_id'], 'companyVerify', 'on' => ['create']],
            [['receiver_mobile', 'receiver_name'], 'receiverVerify', 'on' => ['create']],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'use_point' => '抵现积分',
            'max_use_point' => '最大可使用积分',
            'address_id' => '收货地址',
            'address' => '收货地址',
            'is_logistics' => '运费模板可选',
            'pickup_id' => '自提点',
            'buyer_self_lifting' => '自提开启',
            'pickup_point_is_open' => '自提运费开启',
            'pickup_point_fee' => '自提运费',
            'pickup_point_freight' => '自提满X包邮',
            'is_full_mail' => '包邮',
            'invoice_id' => '发票',
            'orderProducts' => '生成的订单产品',
            'defaultProducts' => '默认的系统产品',
            'sku' => 'sku数据',
            'invoice_content' => '发票备注',
            'member' => '用户',
            'data' => '数据',
            'type' => '数据类型',
            'coupon' => '优惠券',
        ]);
    }

    /**
     * 场景
     *
     * @return array
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['create'] = ArrayHelper::merge(array_keys($this->attributeLabels()), ['address_id']);

        return $scenarios;
    }

    /**
     * 地址验证
     *
     * @param $attribute
     */
    public function addressVerify($attribute)
    {
        if (empty($this->address)) {
            $this->addError($attribute, '地址不存在');
        } elseif (!empty($this->is_logistics) && !$this->company_id && empty($this->pickup_id)) {
            $this->addError($attribute, '请选择物流公司');
        }
    }

    /**
     * 下单地址
     *
     * @param $attribute
     */
    public function receiverVerify($attribute)
    {
        if (!$this->receiver_mobile) {
            $this->addError($attribute, '请填写手机号码');
        }

        if (!$this->receiver_name) {
            $this->addError($attribute, '请填写姓名');
        }
    }

    /**
     * 物流公司验证
     *
     * @param $attribute
     */
    public function companyVerify($attribute)
    {
        if ($this->company_id) {
            $company = Yii::$app->tinyShopService->expressCompany->findById($this->company_id);
            if (!$company) {
                $this->addError($attribute, '物流公司不存在');
            }

            $this->company_name = $company->title;
        }
    }

    /**
     * 发票校验
     *
     * @param $attribute
     */
    public function invoiceVerify($attribute)
    {
        if (!empty($this->invoice_id)) {
            $this->invoice = Yii::$app->services->memberInvoice->findById($this->invoice_id, $this->member->id);

            if (!$this->invoice) {
                $this->addError($attribute, '发票信息不存在');

                return;
            }
        }
    }

    /**
     * 使用积分验证
     *
     * @param $attribute
     */
    public function usePointVerify($attribute)
    {
        if ($this->use_point > $this->max_use_point) {
            $this->addError($attribute, '积分不可超出最大可用额度');
        }
    }
}