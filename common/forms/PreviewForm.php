<?php

namespace addons\TinyShop\common\forms;

use Yii;
use common\helpers\ArrayHelper;
use common\models\member\Member;
use common\models\member\Address;
use addons\TinyShop\common\models\marketing\Coupon;
use addons\TinyShop\common\models\order\OrderProduct;
use addons\TinyShop\common\models\product\Product;
use addons\TinyShop\common\models\merchant\Merchant;
use addons\TinyShop\common\models\order\Order;

/**
 * Class PreviewForm
 * @package addons\TinyShop\common\forms
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
     *
     * @var
     */
    public $type;

    /**
     * 超值换购
     *
     * @var array|string
     */
    public $plus_buy;

    /** ------------- 处理后的数据 ------------- */

    /**
     * 触发的营销
     *
     * @var
     */
    public $marketing;

    /**
     * 优惠
     *
     * @var array
     */
    public $marketingDetails = [];

    /**
     * 默认的系统商品
     *
     * @var Product|array
     */
    public $defaultProducts = [];

    /**
     * 生成的订单商品
     *
     * @var array|OrderProduct
     */
    public $orderProducts = [];

    /**
     * 生成的订单商品组别
     *
     * @var array|OrderProduct
     */
    public $groupOrderProducts = [];

    /**
     * sku数据
     *
     * @var array
     */
    public $sku = [];

    /**
     * 全部商品ID
     *
     * @var array
     */
    public $productIds = [];

    /**
     * 商家分类ID
     *
     * @var array
     */
    public $cateIds = [];
    /**
     * 平台分类ID
     *
     * @var array
     */
    public $platformCateIds = [];

    /**
     * @var Address
     */
    public $address;
    public $address_id;

    /**
     * 门店自提点
     *
     * @var
     */
    public $store;

    /**
     * @var Member
     */
    public $member;

    /**
     * @var Merchant
     */
    public $merchant;

    /**
     * 优惠券
     *
     * @var Coupon
     */
    public $coupon;
    public $coupon_id;

    /**
     * 发票
     *
     * @var
     */
    public $invoice;
    public $invoice_id;
    public $invoice_content;

    /**
     * 物流ID
     *
     * @var int
     */
    public $company_id;

    /**
     * 全部包邮
     *
     * @var int
     */
    public $is_full_mail = 0;
    /**
     * 包邮商品
     *
     * @var array
     */
    public $fullProductIds = [];
    /**
     * 计算运费
     *
     * @var bool
     */
    public $freight = false;

    /**
     * 最多可抵扣积分
     *
     * @var
     */
    public $max_use_point = 0;
    /**
     * 使用积分
     *
     * @var int
     */
    public $use_point = 0;

    /**
     * @var array
     */
    public $config = [];

    /**
     * @return array
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['data', 'type'], 'required'],
            [['plus_buy'], 'safe'],
            [['invoice_content'], 'string'],
            [['coupon_id', 'address_id', 'company_id', 'use_point'], 'integer', 'min' => 0],
            [['shipping_type'], 'required', 'on' => ['create']],
            [['invoice_id'], 'invoiceVerify', 'on' => ['create']],
        ]);
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'member' => '用户',
            'data' => '数据',
            'type' => '数据类型',
            'plus_buy' => '超值换购',
            'coupon' => '优惠券',
            'coupon_id' => '优惠券',
            'address' => '收货地址',
            'address_id' => '收货地址',
            'company_id' => '物流公司',
            'use_point' => '使用积分',
            'invoice_content' => '开票内容',
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
            }
        }
    }
}
