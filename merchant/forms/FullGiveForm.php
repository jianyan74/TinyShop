<?php

namespace addons\TinyShop\merchant\forms;

use Yii;
use common\helpers\ArrayHelper;
use addons\TinyShop\common\enums\PointExchangeTypeEnum;
use addons\TinyShop\common\models\marketing\FullGive;
use addons\TinyShop\common\models\marketing\FullGiveProduct;
use addons\TinyShop\common\enums\RangeTypeEnum;
use addons\TinyShop\common\models\marketing\FullGiveRule;
use addons\TinyShop\common\models\product\Product;

/**
 * Class FullGiveForm
 * @package addons\TinyShop\merchant\forms
 * @author jianyan74 <751393839@qq.com>
 */
class FullGiveForm extends FullGive
{
    /**
     * @var array
     */
    public $products = [];
    /**
     * @var array
     */
    public $rules = [];

    /**
     * @return array
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['range_type'], 'isRequired'],
            [['products'], 'isIntegralBuy'],
            [['products', 'rules'], 'safe'],
            [['rules'], 'rulesIsRequired'],
        ]);
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'products' => '选择商品',
        ]);
    }

    /**
     * @param $attribute
     */
    public function isRequired($attribute)
    {
        if ($this->range_type == RangeTypeEnum::ASSIGN && empty($this->products)) {
            $this->addError($attribute, '请选择商品');
        }
    }

    /**
     * @param $attribute
     */
    public function rulesIsRequired($attribute)
    {
        $rules = $this->rules;
        $this->rules = [];

        $count = count($rules['price']);
        for ($i = 0; $i < $count; $i++) {
            $rule = new FullGiveRule();
            $rule->price = $rules['price'][$i] ?? 0;
            $rule->discount = !empty($rules['discount'][$i]) ? $rules['discount'][$i] : 0;
            $rule->free_shipping = !empty($rules['free_shipping'][$i]) ? $rules['free_shipping'][$i] : 0;
            $rule->give_point = !empty($rules['give_point'][$i]) ? $rules['give_point'][$i] : 0;
            $rule->give_coupon_type_id = !empty($rules['give_coupon_type_id'][$i]) ? $rules['give_coupon_type_id'][$i] : 0;
            $rule->gift_id = !empty($rules['gift_id'][$i]) ? $rules['gift_id'][$i] : 0;
            $this->rules[] = $rule;

            if ($rule->discount == 0 && empty($rule->free_shipping) && empty($rule->give_point) && empty($rule->give_coupon_type_id) && empty($rule->gift_id)) {
                $this->addError($attribute, '请至少选择一种优惠方式');
                break;
            }

            if (!$rule->validate()) {
                $this->addErrors($rule->getErrors());
                break;
            }
        }
    }

    /**
     * @param $attribute
     */
    public function isIntegralBuy($attribute)
    {
        $products = Yii::$app->tinyShopService->product->findByIds($this->products);
        /** @var Product $product */
        foreach ($products as $product) {
            if (PointExchangeTypeEnum::isIntegralBuy($product['point_exchange_type'])) {
                $this->addError($attribute, $product['name'] . '是积分兑换商品不可加入营销');
            }
        }
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        if ($this->range_type == RangeTypeEnum::ALL) {
            FullGiveProduct::deleteAll(['full_give_id' => $this->id]);
        }

        if ($this->range_type == RangeTypeEnum::ASSIGN) {
            FullGiveProduct::deleteAll(['full_give_id' => $this->id]);

            $defaultProducts = Yii::$app->tinyShopService->product->findByIds($this->products);
            /** @var Product $product */
            foreach ($defaultProducts as $product) {
                $model = new FullGiveProduct();
                $model->product_id = $product['id'];
                $model->product_name = $product['name'];
                $model->product_picture = $product['picture'];
                $model->start_time = $this->start_time;
                $model->end_time = $this->end_time;
                $model->state = $this->state;
                $model->status = $this->status;
                $model->full_give_id = $this->id;
                $model->merchant_id = $this->merchant_id;
                $model->save();
            }
        }

        /** @var FullGiveRule $rule */
        FullGiveRule::deleteAll(['full_give_id' => $this->id]);
        foreach ($this->rules as $rule) {
            $rule->full_give_id = $this->id;
            $rule->save();
        }

        parent::afterSave($insert, $changedAttributes);
    }
}