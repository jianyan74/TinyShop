<?php

namespace addons\TinyShop\merchant\forms;

use Yii;
use yii\db\ActiveQuery;
use yii\web\NotFoundHttpException;
use yii\helpers\Json;
use common\helpers\ArrayHelper;
use common\enums\StatusEnum;
use addons\TinyShop\common\models\product\Sku;
use addons\TinyShop\common\models\product\Spec;
use addons\TinyShop\common\models\product\SpecValue;
use addons\TinyShop\common\models\product\AttributeValue;
use addons\TinyShop\common\enums\PointExchangeTypeEnum;

/**
 * Class ProductForm
 * @package addons\TinyShop\merchant\forms
 * @author jianyan74 <751393839@qq.com>
 */
class ProductForm extends \addons\TinyShop\common\models\product\Product
{
    /**
     * sku
     *
     * @var array
     */
    public $skuData = [];

    /**
     * 规格
     *
     * @var array
     */
    public $specValueData = [];

    /**
     * 属性
     *
     * @var array
     */
    public $attributeValueData = [];

    /**
     * 阶梯优惠
     *
     * @var array
     */
    public $ladderPreferentialData = [];

    /**
     * 会员折扣
     *
     * @var array
     */
    public $memberDiscount = [];

    /**
     * @var array
     */
    public $defaultMemberDiscount = [];

    /**
     * @var int
     */
    public $member_level_decimal_reservation;

    /**
     * 规格值单独内容(颜色/图片)
     *
     * @var array
     */
    public $specValueFieldData = [];

    /**
     * @return array
     */
    public function rules()
    {
        $rule = parent::rules();

        return ArrayHelper::merge($rule, [
            ['member_level_decimal_reservation', 'integer'],
            [['is_attribute'], 'verifySku'],
            [['covers'], 'isEmpty'],
        ]);
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

    /**
     * 验证sku
     *
     * @param $attribute
     * @throws NotFoundHttpException
     */
    public function verifySku($attribute)
    {
        if ($this->is_attribute == true) {
            $model = new Sku();

            foreach ($this->skuData as $key => &$datum) {
                $datum['data'] = (string)$key;
                $datum['name'] = '';
                $datum['product_id'] = $this->id;
                $datum['merchant_id'] = $this->merchant_id;
                $model->attributes = $datum;

                if (!$model->validate()) {
                    throw new NotFoundHttpException(Yii::$app->debris->analyErr($model->getFirstErrors()));
                }
            }
        }
    }

    /**
     * 关联规格和其规格值
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSpecWithSpecValue($product_id)
    {
        return $this->hasMany(Spec::class, ['product_id' => 'id'])
            ->with([
                'value' => function (ActiveQuery $query) use ($product_id) {
                    $query->andWhere(['product_id' => $product_id]);
                },
            ])
            ->orderBy('sort asc')
            ->asArray();
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (!$this->isNewRecord) {
            $oldAttributes = $this->oldAttributes;

            // 商品正常情况下删除到购物车
            if ($oldAttributes['status'] == StatusEnum::ENABLED && $this->status == StatusEnum::DISABLED) {
                Yii::$app->tinyShopService->memberCartItem->loseByProductIds([$this->id]);
            }

            // 商品上架情况下到下架
            if ($oldAttributes['product_status'] == self::PRODUCT_STATUS_PUTAWAY && self::PRODUCT_STATUS_SOLD_OUT == $this->product_status) {
                Yii::$app->tinyShopService->memberCartItem->loseByProductIds([$this->id]);
            }

            // 商品规格启用情况下到规格不启用
            if ($oldAttributes['is_attribute'] == StatusEnum::ENABLED && $this->is_attribute == StatusEnum::DISABLED) {
                Yii::$app->tinyShopService->memberCartItem->loseByProductIds([$this->id]);
                // 删除sku
                Sku::deleteAll(['product_id' => $this->id]);
                // 删除规格、规格值
                Spec::deleteAll(['product_id' => $this->id]);
                SpecValue::deleteAll(['product_id' => $this->id]);
                // 删除属性
                AttributeValue::deleteAll(['product_id' => $this->id]);
            }

            // 商品规格不启用情况下到规格启用
            if ($oldAttributes['is_attribute'] == StatusEnum::DISABLED && $this->is_attribute == StatusEnum::ENABLED) {
                Yii::$app->tinyShopService->memberCartItem->loseByProductIds([$this->id]);
                // 删除sku
                Sku::deleteAll(['product_id' => $this->id]);
            }
        }

        // 更新系统主图
        $covers = unserialize($this->covers);
        $this->picture = $covers[0] ?? '';

        // 非积分兑换
        if ($this->point_exchange_type == PointExchangeTypeEnum::NOT_EXCHANGE) {
            $this->point_exchange = 0;
        } else {
            $this->max_use_point = 0;
        }

        $this->address_name = Yii::$app->services->provinces->getCityListName([$this->province_id, $this->city_id, $this->area_id]);

        return parent::beforeSave($insert);
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     * @throws \yii\web\UnprocessableEntityHttpException
     */
    public function afterSave($insert, $changedAttributes)
    {
        if ($this->is_attribute == StatusEnum::ENABLED) {
            // 更新规格、规格值
            $this->updateSpecAndSpecValue();
            // 更新sku
            $this->updateSku();
            // 更新属性
            $this->updateAttributeValue();
        } else {
            // 更新sku
            Yii::$app->tinyShopService->productSku->saveByProductId($this->id, $this->attributes);
        }

        // 更新总库存
        $stock = Yii::$app->tinyShopService->productSku->getStockByProductId($this->id);
        // 更新规格、规格值
        $specModel = Yii::$app->tinyShopService->productSpec->getListWithValue($this->id);
        // 更新记录最小sku
        $minPriceSku = $this->minPriceSku;
        // 更新阶梯优惠
        Yii::$app->tinyShopService->productLadderPreferential->create(
            $this->ladderPreferentialData,
            $this->id,
            $this->max_buy,
            $this->is_open_presell,
            $this->point_exchange_type,
            $minPriceSku['price']
        );

        self::updateAll(
            [
                'stock' => $stock,
                'base_attribute_format' => Json::encode($specModel),
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

    /**
     * 更新属性
     *
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    protected function updateAttributeValue()
    {
        $ids = array_keys($this->attributeValueData);
        $deleteIds = [];
        $updatedIds = [];
        // 已有的参数信息
        $models = $this->attributeValue;
        // 系统模型的参数
        $baseAttributeValue = Yii::$app->tinyShopService->baseAttributeValue->findByIds($ids);
        $baseAttributeValue = ArrayHelper::map($baseAttributeValue, 'id', 'title');

        /** @var AttributeValue $model */
        foreach ($models as $model) {
            if (in_array($model['base_attribute_value_id'], $ids)) {
                $value = $this->attributeValueData[$model['base_attribute_value_id']] ?? '';
                is_array($value) && $value = implode(',', $value);

                $model->title = $baseAttributeValue[$model['base_attribute_value_id']] ?? '';
                $model->value = $value;
                if (!$model->save()) {
                    throw new NotFoundHttpException(Yii::$app->debris->analyErr($model->getFirstErrors()));
                }

                $updatedIds[] = $model['base_attribute_value_id'];
            } else {
                $deleteIds[] = $model['id'];
            }
        }

        // 创建属性
        $rows = [];
        foreach ($this->attributeValueData as $key => $value) {
            if (!in_array($key, $updatedIds)) {
                is_array($value) && $value = implode(',', $value);

                $rows[] = [
                    'base_attribute_value_id' => $key,
                    'product_id' => $this->id,
                    'merchant_id' => $this->merchant_id,
                    'title' => $baseAttributeValue[$key] ?? '',
                    'value' => $value,
                    'created_at' => time(),
                    'updated_at' => time(),
                ];
            }
        }

        // 插入数据
        $field = ['base_attribute_value_id', 'product_id', 'merchant_id', 'title', 'value', 'created_at', 'updated_at'];
        !empty($rows) && Yii::$app->db->createCommand()->batchInsert(AttributeValue::tableName(), $field,
            $rows)->execute();

        // 批量删除冗余的数据
        !empty($deleteIds) && AttributeValue::deleteAll(['and', ['product_id' => $this->id], ['in', 'id', $deleteIds]]);

        unset($baseAttributeValue, $models);
    }

    /**
     * 更新规格
     *
     * @return bool
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    protected function updateSpecAndSpecValue()
    {
        if (empty($this->specValueData)) {
            return true;
        }

        // 当前产品的规格属性
        $specValue = $this->specValue;
        $attributeData = $optionIds = $optionData = [];
        $i = $j = 0;
        foreach ($this->specValueData as $key => $item) {
            $attributeData[$key] = $i;
            $options = explode('-', $item);
            $optionIds = ArrayHelper::merge($optionIds, $options);

            foreach ($options as $option) {
                $optionData[$option] = [
                    'sort' => $j,
                    'attribute_id' => $key,
                ];
                $j++;
            }

            $i++;
        }

        // 更新规格
        $this->updateSpec($attributeData);
        // 更新规格值
        $this->updateSpecValue($optionIds, $optionData);

        unset($specValue);
    }

    /**
     * 更新规格
     *
     * @param $attributeData
     * @param $specValue
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    protected function updateSpec($specData)
    {
        $specIds = array_keys($specData);
        $deleteIds = [];
        $updatedIds = [];
        // 系统规格
        $baseSpec = Yii::$app->tinyShopService->baseSpec->findByIds($specIds);
        $tmpBaseSpec = [];
        foreach ($baseSpec as $item) {
            $tmpBaseSpec[$item['id']] = $item;
        }

        $attribute = $this->spec;
        /** @var Spec $model */
        foreach ($attribute as $model) {
            $baseSpecId = $model['base_spec_id'];
            if (in_array($baseSpecId, $specIds)) {
                $model->title = $tmpBaseSpec[$baseSpecId]['title'] ?? '';
                $model->show_type = $tmpBaseSpec[$baseSpecId]['show_type'] ?? '';
                $model->sort = $specData[$baseSpecId];
                if (!$model->save()) {
                    throw new NotFoundHttpException(Yii::$app->debris->analyErr($model->getFirstErrors()));
                }

                $updatedIds[] = $baseSpecId;
            } else {
                $deleteIds[] = $model['id'];
            }
        }

        // 创建参数
        $rows = [];
        foreach ($specData as $key => $value) {
            if (!in_array($key, $updatedIds)) {
                $rows[] = [
                    'product_id' => $this->id,
                    'merchant_id' => $this->merchant_id,
                    'base_spec_id' => $key,
                    'title' => $tmpBaseSpec[$key]['title'] ?? '',
                    'sort' => $value,
                    'show_type' => $tmpBaseSpec[$key]['show_type'] ?? '',
                ];
            }
        }

        // 插入数据
        $field = ['product_id', 'merchant_id', 'base_spec_id', 'title', 'sort', 'show_type'];
        !empty($rows) && Yii::$app->db->createCommand()->batchInsert(Spec::tableName(), $field, $rows)->execute();

        // 批量删除冗余的数据
        !empty($deleteIds) && Spec::deleteAll([
            'and',
            ['product_id' => $this->id, 'merchant_id' => $this->merchant_id],
            ['in', 'id', $deleteIds],
        ]);
        unset($attribute, $specData, $field, $rows, $baseSpec);
    }

    /**
     * 更新规格值
     *
     * @param $optionIds
     * @param $optionData
     * @param $specValue
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    protected function updateSpecValue($valueIds, $valueData)
    {
        $sysOptions = Yii::$app->tinyShopService->baseSpecValue->findByIds($valueIds);
        $sysOptions = ArrayHelper::map($sysOptions, 'id', 'title');

        $deleteIds = [];
        $updatedIds = [];

        $option = $this->specValue;
        /** @var SpecValue $model */
        foreach ($option as $model) {
            $baseSpecValueId = $model['base_spec_value_id'];
            if (in_array($baseSpecValueId, $valueIds)) {
                $model->title = $sysOptions[$baseSpecValueId] ?? '';
                $model->data = $this->specValueFieldData[$baseSpecValueId] ?? '';
                $model->sort = $valueData[$baseSpecValueId]['sort'];

                if (!$model->save()) {
                    throw new NotFoundHttpException(Yii::$app->debris->analyErr($model->getFirstErrors()));
                }

                $updatedIds[] = $baseSpecValueId;
            } else {
                $deleteIds[] = $model['id'];
            }
        }

        // 创建参数
        $rows = [];
        foreach ($valueData as $key => $value) {
            if (!in_array($key, $updatedIds)) {
                $rows[] = [
                    'product_id' => $this->id,
                    'base_spec_id' => $valueData[$key]['attribute_id'],
                    'base_spec_value_id' => $key,
                    'title' => $sysOptions[$key] ?? '',
                    'data' => $this->specValueFieldData[$key] ?? '',
                    'sort' => $valueData[$key]['sort'],
                    'merchant_id' => $this->merchant_id,
                ];
            }
        }

        // 插入数据
        $field = ['product_id', 'base_spec_id', 'base_spec_value_id', 'title', 'data', 'sort', 'merchant_id'];
        !empty($rows) && Yii::$app->db->createCommand()->batchInsert(SpecValue::tableName(), $field, $rows)->execute();

        // 批量删除冗余的数据
        !empty($deleteIds) && SpecValue::deleteAll(['and', ['product_id' => $this->id], ['in', 'id', $deleteIds]]);
        unset($option, $sysOptions, $field, $rows);
    }

    /**
     * 更新sku
     *
     * @return bool
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    protected function updateSku()
    {
        if (empty($this->skuData)) {
            return true;
        }

        // 查询所有的规格
        $specValue = Yii::$app->tinyShopService->productSpecValue->getListByProductId($this->id);
        $specValue = ArrayHelper::arrayKey($specValue, 'base_spec_value_id');

        /** @var Sku $skuModels */
        $skuModels = $this->sku;
        $deleteIds = [];
        $updatedIds = [];

        // 判断sku是否存在 存在则更新
        /** @var Sku $model */
        foreach ($skuModels as $model) {
            if (!empty($this->skuData[$model->data])) {
                $model->attributes = $this->skuData[$model->data];
                $model->name = $this->getSkuName($specValue, $model->data);
                if (!$model->save()) {
                    throw new NotFoundHttpException(Yii::$app->debris->analyErr($model->getFirstErrors()));
                }

                $updatedIds[] = $model->data;
            } else {
                $deleteIds[] = $model->id;
            }
        }

        // 创建sku
        $rows = [];
        foreach ($this->skuData as $key => $sku) {
            $sku['product_id'] = $this->id;
            $sku['name'] = $this->getSkuName($specValue, $sku['data']);
            $sku['merchant_id'] = $this->merchant_id;
            $sku['created_at'] = time();
            $sku['updated_at'] = time();
            !in_array($key, $updatedIds) && $rows[] = $sku;
        }

        // 插入数据
        $field = [
            'picture',
            'price',
            'market_price',
            'cost_price',
            'stock',
            'code',
            'data',
            'name',
            'product_id',
            'merchant_id',
            'created_at',
            'updated_at',
        ];

        if (!empty($rows)) {
            Yii::$app->db->createCommand()->batchInsert(Sku::tableName(), $field, $rows)->execute();
        }

        // 让购物车里面的sku失效
        if (!empty($deleteIds)) {
            // 删除失效的sku
            Sku::deleteAll([
                'and',
                ['product_id' => $this->id, 'merchant_id' => $this->merchant_id],
                ['in', 'id', $deleteIds],
            ]);

            Yii::$app->tinyShopService->memberCartItem->loseBySkus($deleteIds);
        }

        unset($skuModels, $field, $rows);
    }

    /**
     * @param $specValue
     * @param $sku
     * @return string
     */
    protected function getSkuName($specValue, $sku)
    {
        $name = [];
        $skuArr = explode('-', $sku);
        foreach ($skuArr as $item) {
            isset($specValue[$item]) && $name[] = $specValue[$item]['title'];
        }

        return implode(' ', $name);
    }
}