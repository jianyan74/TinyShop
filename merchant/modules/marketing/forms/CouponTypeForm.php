<?php

namespace addons\TinyShop\merchant\modules\marketing\forms;

use addons\TinyShop\common\models\marketing\MarketingCate;
use Yii;
use yii\web\UnprocessableEntityHttpException;
use common\helpers\ArrayHelper;
use addons\TinyShop\common\models\marketing\CouponType;
use addons\TinyShop\common\enums\RangeTypeEnum;
use addons\TinyShop\common\enums\MarketingEnum;
use addons\TinyShop\common\enums\DiscountTypeEnum;
use addons\TinyShop\common\models\marketing\MarketingProduct;

/**
 * Class CouponTypeForm
 * @package addons\TinyShop\merchant\modules\marketing\forms
 * @author jianyan74 <751393839@qq.com>
 */
class CouponTypeForm extends CouponType
{
    public $money = 0;

    public $defaultCount = 0;
    public $replenishmentNum = 0;
    /**
     * @var array|string
     */
    public $products = [];
    public $cateIds = [];

    public function rules()
    {
        $rule = parent::rules();
        $rule[] = [['defaultCount'], 'integer', 'min' => 0];
        $rule[] = [['replenishmentNum'], 'integer', 'min' => 0, 'max' => 99999];
        $rule[] = [['products', 'cateIds'], 'safe'];
        $rule[] = [['money'], 'number', 'min' => 0];
        $rule[] = [['money', 'range_type'], 'required'];
        $rule[] = [['range_type'], 'verifyRangeType'];

        return $rule;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'defaultCount' => '已发放数量',
            'replenishmentNum' => '补发数量',
            'products' => '活动商品',
            'cateIds' => '商品分类',
            'money' => '面额',
        ]);
    }

    public function afterFind()
    {
        $this->products = Yii::$app->tinyShopService->marketingProduct->findByMarketing($this->id, MarketingEnum::COUPON);
        $this->discount = floatval($this->discount);
        if ($this->discount_type == DiscountTypeEnum::MONEY) {
            $this->money = $this->discount;
            $this->discount = 9.9;
        }

        parent::afterFind();
    }

    /**
     * @param $attribute
     */
    public function verifyRangeType($attribute)
    {
        if (in_array($this->range_type, [RangeTypeEnum::ASSIGN_PRODUCT, RangeTypeEnum::NOT_ASSIGN_PRODUCT]) && empty($this->products)) {
            $this->addError($attribute, '请选择活动商品');
        }

        if (in_array($this->range_type, [RangeTypeEnum::ASSIGN_CATE, RangeTypeEnum::NOT_ASSIGN_CATE]) && empty($this->cateIds)) {
            $this->addError($attribute, '请选择活动分类');
        }
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->replenishmentNum = $this->count;
        }

        if (!$this->isNewRecord && $this->replenishmentNum > 0) {
            $this->count += $this->replenishmentNum;
        }

        // 修改写入
        if ($this->discount_type == DiscountTypeEnum::MONEY) {
            $this->discount = $this->money;
        }

        return parent::beforeSave($insert);
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     * @throws \yii\db\Exception
     */
    public function afterSave($insert, $changedAttributes)
    {
        // 创建具体优惠券
        $this->replenishmentNum > 0 && Yii::$app->tinyShopService->marketingCoupon->create($this, $this->replenishmentNum);

        // 关联商品
        Yii::$app->tinyShopService->marketingProduct->delByMarketing($this->id, [MarketingEnum::COUPON_IN, MarketingEnum::COUPON_NOT_IN]);
        if (in_array($this->range_type, [RangeTypeEnum::ASSIGN_PRODUCT, RangeTypeEnum::NOT_ASSIGN_PRODUCT])) {
            foreach ($this->products as $key => $product) {
                $model = new MarketingProduct();
                $model = $model->loadDefaultValues();
                $model->attributes = $product;
                $model->product_id = $product['id'];
                $model->merchant_id = $this->merchant_id;
                $model->marketing_id = $this->id;
                $model->marketing_type = $this->range_type == RangeTypeEnum::ASSIGN_PRODUCT ? MarketingEnum::COUPON_IN : MarketingEnum::COUPON_NOT_IN;
                $model->discount = $this->discount;
                $model->discount_type = $this->discount_type;
                $model->prediction_time = $this->get_start_time;
                $model->start_time = $this->get_start_time;
                $model->end_time = $this->get_end_time;
                $model->status = $this->status;
                if (!$model->save()) {
                    throw new UnprocessableEntityHttpException(Yii::$app->services->base->analysisErr($model->getFirstErrors()));
                }
            }
        }

        // 关联分类
        Yii::$app->tinyShopService->marketingCate->delByMarketing($this->id, [MarketingEnum::COUPON_IN, MarketingEnum::COUPON_NOT_IN]);
        if (in_array($this->range_type, [RangeTypeEnum::ASSIGN_CATE, RangeTypeEnum::NOT_ASSIGN_CATE])) {
            foreach ($this->cateIds as $cateId) {
                $model = new MarketingCate();
                $model = $model->loadDefaultValues();
                $model->cate_id = $cateId;
                $model->merchant_id = $this->merchant_id;
                $model->marketing_id = $this->id;
                $model->marketing_type = $this->range_type == RangeTypeEnum::ASSIGN_CATE ? MarketingEnum::COUPON_IN : MarketingEnum::COUPON_NOT_IN;
                $model->prediction_time = $this->get_start_time;
                $model->start_time = $this->get_start_time;
                $model->end_time = $this->get_end_time;
                $model->status = $this->status;
                if (!$model->save()) {
                    throw new UnprocessableEntityHttpException(Yii::$app->services->base->analysisErr($model->getFirstErrors()));
                }
            }
        }

        parent::afterSave($insert, $changedAttributes);
    }
}
