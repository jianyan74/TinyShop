<?php

namespace addons\TinyShop\api\modules\v1\forms;

use addons\TinyShop\merchant\forms\BargainConfigForm;
use common\helpers\AddonHelper;
use Yii;
use common\helpers\ArrayHelper;
use common\helpers\BcHelper;
use common\helpers\ArithmeticHelper;
use addons\TinyShop\common\models\marketing\Bargain;
use addons\TinyShop\common\models\marketing\BargainLaunch;
use addons\TinyShop\common\enums\ShippingTypeEnum;
use yii\web\UnprocessableEntityHttpException;

/**
 * Class BargainLaunchForm
 * @package addons\TinyShop\api\modules\v1\forms
 * @author jianyan74 <751393839@qq.com>
 */
class BargainLaunchForm extends BargainLaunch
{
    /**
     * @var Bargain
     */
    protected $bargain;
    /**
     * @var
     */
    protected $onePrice;
    /**
     * @var
     */
    protected $pickup;

    /**
     * @var int
     */
    public $address_id;

    /**
     * @return array
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            ['bargain_id', 'verifyBargain'],
            [['pickup_id', 'address_id'], 'integer'],
            [['shipping_type'], 'verifyShippingType'],
            ['shipping_type', 'in', 'range' => ShippingTypeEnum::getKeys()],
        ]);
    }

    /**
     * @param $attribute
     */
    public function verifyBargain($attribute)
    {
        $this->bargain = Yii::$app->tinyShopService->marketingBargain->findById($this->bargain_id);
        if (!$this->bargain) {
            $this->addError($attribute, '砍价活动不存在');
            return;
        }

        if ($this->bargain->start_time > time()) {
            $this->addError($attribute, '砍价活动未开始');
            return;
        }

        if ($this->bargain->end_time < time()) {
            $this->addError($attribute, '砍价活动已结束');
            return;
        }
    }

    /**
     * @throws UnprocessableEntityHttpException
     */
    public function verifyShippingType()
    {
        switch ($this->shipping_type) {
            case ShippingTypeEnum::LOGISTICS :
                if (!$this->address_id) {
                    throw new UnprocessableEntityHttpException('请选择收货地址');
                }

                if (!($address = Yii::$app->services->memberAddress->findById($this->address_id, $this->member_id))) {
                    throw new UnprocessableEntityHttpException('找不到收货地址');
                }

                $this->receiver_mobile = $address['mobile'];
                $this->receiver_province = $address['province_id'];
                $this->receiver_city = $address['city_id'];
                $this->receiver_area = $address['area_id'];
                $this->receiver_address = $address['address_details'];
                $this->receiver_region_name = $address['address_name'];
                $this->receiver_zip = (string)$address['zip_code'];
                $this->receiver_name = $address['realname'];

                break;
            case ShippingTypeEnum::PICKUP :
                if (!$this->pickup_id) {
                    throw new UnprocessableEntityHttpException('请选择自提地点');
                }

                if (!($this->pickup = Yii::$app->tinyShopService->pickupPoint->findById($this->pickup_id))) {
                    throw new UnprocessableEntityHttpException('自提地点不存在');
                }
                break;
        }
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'address_id' => '地址',
        ]);
    }

    /**
     * @param bool $insert
     * @return bool
     * @throws \yii\web\UnprocessableEntityHttpException
     */
    public function beforeSave($insert)
    {
        $model = new BargainConfigForm();
        $model->attributes = AddonHelper::getConfig(false, '', $this->bargain->merchant_id);

        $skus = Yii::$app->tinyShopService->productSku->decrRepertory([$this->sku_id => 1]);
        $sku = $skus[0];
        $this->merchant_id = $this->bargain->merchant_id;
        $this->bargain_id = $this->bargain->id;
        $this->start_time = time();
        $this->end_time = time() + $model->bargain_end_date * 3600 * 24;
        $this->pay_close_hours = $model->bargain_pay_close_hours;
        $this->min_number = $this->bargain->min_number;
        $this->product_money = $sku['price'];
        $this->product_id = $sku['product_id'];
        $this->product_name = $sku['product']['name'] ?? '';
        $this->sku_name = $sku['name'];

        // 最低价格
        $this->min_money = BcHelper::div($this->bargain->min_rate * $this->product_money, 100);
        $this->bargain_money = $this->product_money - $this->min_money;

        // 首刀比例
        $rate = rand($this->bargain->one_min_rate, $this->bargain->one_max_rate);
        $onePrice = BcHelper::div($rate * $this->bargain_money, 100);
        if ($onePrice > $this->bargain_money) {
            $onePrice = $this->bargain_money;
        }
        $onePrice = (float)$onePrice;

        $this->partake_number = 1;
        $this->surplus_money = BcHelper::sub($this->bargain_money, $onePrice);

        $package = [];
        if ($this->min_number > 1) {
            $package = ArithmeticHelper::getRedPackage($this->surplus_money, $this->min_number - 1, 0.01, $this->surplus_money);
        } else {
            $this->surplus_money = 0;
        }

        // 库存
        $this->repertory_money = ArrayHelper::merge($package, [$onePrice]);
        $this->residue_repertory_money = $package;

        $this->onePrice = $onePrice;

        return parent::beforeSave($insert);
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     * @throws UnprocessableEntityHttpException
     */
    public function afterSave($insert, $changedAttributes)
    {
        // 进行首刀
        Yii::$app->tinyShopService->marketingBargainPartake->create($this->id, $this->member_id, $this->onePrice);

        parent::afterSave($insert, $changedAttributes);
    }
}