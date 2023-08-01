<?php

namespace addons\TinyShop\merchant\modules\product\forms;

use Yii;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use addons\TinyShop\common\models\product\Sku;
use addons\TinyShop\common\models\product\Spec;
use addons\TinyShop\common\models\product\SpecValue;
use addons\TinyShop\common\models\product\Product;
use yii\web\NotFoundHttpException;

/**
 * Class ProductForm
 * @package addons\TinyShop\merchant\modules\product\forms
 */
class ProductForm extends Product
{
    /**
     * 参数
     *
     * @var array
     */
    public $attributeData = [];

    /**
     * 规格
     *
     * @var array
     */
    public $specData = [];

    /**
     * sku
     *
     * @var array
     */
    public $skuData = [];

    /**
     * 分类ID
     *
     * @var array
     */
    public $cateIds = [];

    /**
     * 平台分类ID
     *
     * @var int
     */
    public $platformCateId;

    public function rules()
    {
        $rule = parent::rules();
        if (Yii::$app->services->devPattern->isB2B2C()) {
            $rule = ArrayHelper::merge([
                [['platformCateId'], 'required'],
            ], $rule);
        }

        $rule = ArrayHelper::merge($rule, [
            [['cateIds'], 'required'],
            [['platformCateId', 'cateIds', 'attributeData', 'specData', 'skuData'], 'safe'],
            [['covers'], 'isEmpty'],
        ]);

        return $rule;
    }

    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'cateIds' => '商品分类',
            'platformCateId' => '平台分类',
        ]);
    }

    public function afterFind()
    {
        // 平台分类
        $this->platformCateId = $this->cate_id ?? 0;
        // 商户分类
        $this->cateIds = Yii::$app->tinyShopService->productCateMap->findByProductId($this->id);

        parent::afterFind();
    }

    /**
     * @param $attribute
     */
    public function isEmpty($attribute)
    {
        if (empty($this->covers)) {
            $this->addError($attribute, '请上传幻灯片');
        }
    }

    public function beforeSave($insert)
    {
        $this->picture = $this->covers[0];
        $this->cate_id = $this->cateIds[0];
        // 总销量
        $this->total_sales = $this->sales + $this->real_sales;
        if (Yii::$app->services->devPattern->isB2B2C()) {
            if (empty($this->platformCateId)) {
                throw new NotFoundHttpException('请设置平台分类');
            }

            $this->cate_id = $this->platformCateId;
        }

        if (
            empty($this->cateIds) ||
            $this->cateIds[0] == 0
        ) {
            throw new NotFoundHttpException('请设置商品分类');
        }

        if ($this->is_spec != StatusEnum::ENABLED) {
            $this->spec_template_id = 0;
        }

        // 设置发货地址
        $this->address_name = Yii::$app->services->provinces->getCityListName([$this->province_id, $this->city_id, $this->area_id]);

        if (!$this->isNewRecord) {
            $oldAttributes = $this->oldAttributes;
            // 商品正常情况下删除到购物车
            if ($oldAttributes['status'] == StatusEnum::ENABLED && $this->status == StatusEnum::DELETE) {
                 Yii::$app->tinyShopService->memberCartItem->loseByProductIds([$this->id], true);
            }

            // 商品上架情况下到下架
            if ($oldAttributes['status'] == StatusEnum::ENABLED && StatusEnum::DISABLED == $this->status) {
                 Yii::$app->tinyShopService->memberCartItem->loseByProductIds([$this->id], true);
                // 营销失效
                Yii::$app->tinyShopService->marketingProduct->loseByProductId($this->id, true);
            }

            // 商品规格启用情况下到规格不启用
            if ($oldAttributes['is_spec'] == StatusEnum::ENABLED && $this->is_spec == StatusEnum::DISABLED) {
                Yii::$app->tinyShopService->memberCartItem->loseByProductIds([$this->id]);
                // 营销失效
                Yii::$app->tinyShopService->marketingProduct->loseByProductId($this->id);
                // 删除sku
                Sku::deleteAll(['product_id' => $this->id]);
                // 删除规格、规格值
                Spec::deleteAll(['product_id' => $this->id]);
                SpecValue::deleteAll(['product_id' => $this->id]);
            }

            // 商品规格不启用情况下到规格启用
            if ($oldAttributes['is_spec'] == StatusEnum::DISABLED && $this->is_spec == StatusEnum::ENABLED) {
                Yii::$app->tinyShopService->memberCartItem->loseByProductIds([$this->id]);
                // 营销失效
                Yii::$app->tinyShopService->marketingProduct->loseByProductId($this->id);
                // 删除sku
                Sku::deleteAll(['product_id' => $this->id]);
            }
        }

        return parent::beforeSave($insert);
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     * @throws \yii\db\Exception
     * @throws \yii\web\UnprocessableEntityHttpException
     */
    public function afterSave($insert, $changedAttributes)
    {
        // 单规格
        if ($this->is_spec != StatusEnum::ENABLED) {
            Yii::$app->tinyShopService->productSku->saveByProductId($this->id, $this->attributes);
        } else {
            // SKU
            Yii::$app->tinyShopService->productSku->create($this, $this->skuData);
        }

        // 分类
        Yii::$app->tinyShopService->productCateMap->create($this->id, $this->cateIds, $this->merchant_id);
        Yii::$app->tinyShopService->productCateMap->create($this->id, [$this->cate_id]);

        // 参数
        !empty($this->attributeData) && Yii::$app->tinyShopService->productAttributeValue->create($this->id, $this->merchant_id, $this->attributeData);

        // 规格
        !empty($this->specData) && Yii::$app->tinyShopService->productSpec->create($this, $this->specData);

        // 更新记录最小sku
        $minPriceSku = $this->minPriceSku;

        self::updateAll(
            [
                'stock' => Yii::$app->tinyShopService->productSku->getStockByProductId($this->id),
                'spec_format' => Yii::$app->tinyShopService->productSpec->getPitchOnByProductId($this->id),
                'price' => $minPriceSku['price'],
                'market_price' => $minPriceSku['market_price'],
                'cost_price' => $minPriceSku['cost_price'],
            ],
            [
                'id' => $this->id,
            ]
        );

        parent::afterSave($insert, $changedAttributes);
    }
}
