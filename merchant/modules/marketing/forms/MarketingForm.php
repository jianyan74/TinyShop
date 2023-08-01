<?php

namespace addons\TinyShop\merchant\modules\marketing\forms;

use Yii;
use yii\helpers\Json;
use yii\web\UnprocessableEntityHttpException;
use common\helpers\ArrayHelper;
use common\enums\StatusEnum;
use addons\TinyShop\common\enums\DiscountTypeEnum;
use addons\TinyShop\common\enums\MarketingEnum;
use addons\TinyShop\common\models\marketing\MarketingProduct;
use addons\TinyShop\common\models\marketing\MarketingProductSku;
use addons\TinyShop\common\enums\MarketingAdvanceTypeEnum;

/**
 * Class MarketingForm
 * @package addons\TinyShop\merchant\modules\marketing\forms
 * @author jianyan74 <751393839@qq.com>
 */
class MarketingForm extends MarketingProduct
{
    /**
     * @var array|string
     */
    public $products = [];

    /**
     * 无需创建 sku 的 商品 ID
     *
     * @var array
     */
    protected $productIds = [];

    /**
     * 验证器
     *
     * @var
     */
    public $verifyForm;

    /**
     * 自定义内容
     *
     * @var
     */
    public $columns = [];

    /**
     * 预告类型
     *
     * @var int
     */
    public $advanceType;

    /**
     * 预告小时
     *
     * @var int
     */
    public $advanceHour;

    /**
     * 默认营销类型
     *
     * @var int
     */
    protected $defaultDiscountType;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['products'], 'safe'],
            [['products'], 'required'],
            [['products'], 'timeUnique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'products' => '活动商品',
        ]);
    }

    /**
     * @throws UnprocessableEntityHttpException
     */
    public function timeUnique()
    {
        if (empty($this->start_time) && empty($this->end_time)) {
            return false;
        }

        $ids = ArrayHelper::getColumn($this->products, 'id');
        // 验证营销是否有效
        Yii::$app->tinyShopService->marketingProduct->verifyMarketing(
            $ids,
            $this->marketing_id,
            $this->marketing_type,
            $this->getPredictionTime($this->start_time),
            $this->end_time,
            $this->status
        );
    }

    /**
     * @throws UnprocessableEntityHttpException
     */
    public function validator()
    {
        foreach ($this->products as $product) {
            $productVerify = new $this->verifyForm();
            $productVerify->attributes = $product;
            // TODO 如果非必填字段，批量填写了Sku就无法写入
            if ($productVerify->validate()) {
                $this->productIds[$product['id']] = true;
                continue;
            }

            // 处理 sku
            foreach ($product['sku'] as $value) {
                $skuVerify = new $this->verifyForm();
                $skuVerify->attributes = $value;
                if (!$skuVerify->validate()) {
                    $error = $product['name'] . array_values($skuVerify->getFirstErrors())[0];
                    throw new UnprocessableEntityHttpException($error);
                }
            }
        }
    }

    /**
     * 营销创建
     *
     * 1、基本营销
     *   - 单一规格
     *   - 多规格
     * 2、秒杀限时营销
     *   - 单一规格
     *   - 多规格
     *   - 多时间点单一规格
     *   - 多时间点多规格
     *
     * @param SecKill|Discount $marketingModel
     * @return void
     */
    public function create($marketingModel = null)
    {
        !empty($this->verifyForm) && $this->validator();
        // 移除原先的数据
        MarketingProduct::deleteAll(['marketing_id' => $this->marketing_id, 'marketing_type' => $this->marketing_type]);
        MarketingProductSku::deleteAll(['marketing_id' => $this->marketing_id, 'marketing_type' => $this->marketing_type]);

        // 初始化数据
        $field = [];
        $rows = [];
        $marketingTimes = [];
        // 插入默认商品
        foreach ($this->products as $product) {
            $model = new MarketingProduct();
            $model = $model->loadDefaultValues();
            $model->attributes = $product;
            $model->merchant_id = $this->merchant_id;
            $model->marketing_id = $this->marketing_id;
            $model->marketing_type = $this->marketing_type;

            empty($model->discount) && $model->discount = 0;
            empty($model->discount_type) && $model->discount_type = DiscountTypeEnum::FIXATION;
            empty($model->marketing_sales) && $model->marketing_sales = 0;
            empty($model->marketing_stock) && $model->marketing_stock = 0;
            empty($model->number) && $model->number = 0;
            !empty($this->defaultDiscountType) && $model->discount_type = $this->defaultDiscountType;
            $model->start_time = $this->start_time ?? 0;
            $model->end_time = $this->end_time ?? 0;
            $model->prediction_time = $this->getPredictionTime($model->start_time);
            $model->product_id = $product['id'];
            $model->status = $this->status;

            $marketingData = [];
            isset($product['tmp_discount_type_explain']) && $marketingData['tmp_discount_type_explain'] = $product['tmp_discount_type_explain'];
            isset($product['tmp_money']) && $marketingData['tmp_money'] = $product['tmp_money'];
            foreach ($this->columns as $column) {
                $marketingData[$column] = $product[$column] ?? '';
            }
            $model->marketing_data = $marketingData;
            if (!$model->save()) {
                throw new UnprocessableEntityHttpException(Yii::$app->services->base->analysisErr($model->getFirstErrors()));
            }

            // 参与价格计算
            $notCalculate = StatusEnum::DISABLED;
            if (
                !empty($this->columns) &&
                !empty($this->verifyForm) &&
                empty($this->productIds[$model->product_id])
            ) {
                $this->createSku($model, $product['sku']);
                // 多规格默认的不参加计算
                $notCalculate = StatusEnum::ENABLED;
            }

            // 默认Sku
            $skuModel = new MarketingProductSku();
            $skuModel = $skuModel->loadDefaultValues();
            $skuModel->attributes = ArrayHelper::toArray($model);
            $skuModel->marketing_product_id = $model->id;
            $skuModel->sku_id = 0;
            $skuModel->not_calculate = $notCalculate;
            $skuModel->marketing_price = Yii::$app->tinyShopService->marketing->getPrice(
                $skuModel->discount,
                $skuModel->discount_type,
                1,
                $product['price'] ?? 0
            );
            $skuModel->save();
        }

        // 批量插入
        !empty($rows) && Yii::$app->db->createCommand()->batchInsert(self::tableName(), $field, $rows)->execute();
        $this->updateMinPrice(ArrayHelper::getColumn($this->products, 'id'), $this->marketing_id, $this->marketing_type);
        !empty($marketingTimes) && $this->batchInsertOtherProductSku($this->marketing_id, $this->marketing_type, $marketingTimes);
    }

    /**
     * @param MarketingProduct $marketingProduct
     * @param array $skus
     * @return void
     * @throws UnprocessableEntityHttpException
     */
    protected function createSku(MarketingProduct $marketingProduct, array $skus)
    {
        $field = [];
        $rows = [];
        foreach ($skus as $value) {
            $skuModel = new MarketingProductSku();
            $skuModel = $skuModel->loadDefaultValues();
            $skuModel->attributes = $value;
            $skuModel->marketing_product_id = $marketingProduct->id;
            $skuModel->merchant_id = $marketingProduct->merchant_id;
            $skuModel->marketing_id = $marketingProduct->marketing_id;
            $skuModel->marketing_type = $marketingProduct->marketing_type;
            $skuModel->product_id = $marketingProduct->product_id;
            $skuModel->sku_id = $value['id'];
            $skuModel->start_time = $marketingProduct->start_time;
            $skuModel->end_time = $marketingProduct->end_time;
            $skuModel->prediction_time = $marketingProduct->prediction_time;
            $skuModel->status = $marketingProduct->status;
            // 限时折扣
            if ($this->marketing_type == MarketingEnum::DISCOUNT) {
                list($discount, $discount_type) = $this->getDiscountAndType($value);
                $skuModel->discount = $discount;
                $skuModel->discount_type = $discount_type;
            }
            empty($skuModel->discount) && $skuModel->discount = 0;
            empty($skuModel->discount_type) && $skuModel->discount_type = DiscountTypeEnum::FIXATION;
            empty($skuModel->marketing_sales) && $skuModel->marketing_sales = 0;
            empty($skuModel->marketing_stock) && $skuModel->marketing_stock = 0;
            empty($skuModel->number) && $skuModel->number = 0;
            !empty($this->defaultDiscountType) && $skuModel->discount_type = $this->defaultDiscountType;
            // 营销额外字段
            $marketingData = [];
            isset($value['tmp_discount_type_explain']) && $marketingData['tmp_discount_type_explain'] = $value['tmp_discount_type_explain'];
            isset($value['tmp_money']) && $marketingData['tmp_money'] = $value['tmp_money'];
            foreach ($this->columns as $column) {
                $marketingData[$column] = $value[$column] ?? '';
            }
            // 独立设置的 Sku
            $skuModel->marketing_data = Json::encode($marketingData);
            $skuModel->marketing_price = Yii::$app->tinyShopService->marketing->getPrice(
                $skuModel->discount,
                $skuModel->discount_type,
                1,
                $value['price'] ?? 0,
            );

            if (!$skuModel->validate()) {
                throw new UnprocessableEntityHttpException(Yii::$app->services->base->analysisErr($skuModel->getFirstErrors()));
            }

            $rows[] = ArrayHelper::toArray($skuModel);
            empty($field) && $field = array_keys($rows[0]);
        }

        !empty($rows) && Yii::$app->db->createCommand()->batchInsert(MarketingProductSku::tableName(), $field, $rows)->execute();
    }

    /**
     * 记录最小规格
     *
     * @param $productIds
     * @param $marketingId
     * @param $marketingType
     * @return void
     */
    protected function updateMinPrice($productIds, $marketingId, $marketingType)
    {
        $ids = [];
        foreach ($productIds as $productId) {
            $ids[] = MarketingProductSku::find()
                ->select(['id'])
                ->where([
                    'product_id' => $productId,
                    'marketing_id' => $marketingId,
                    'marketing_type' => $marketingType,
                    'not_calculate' => 0,
                ])
                ->orderBy('marketing_price asc')
                ->scalar();

            // 修改库存
            $stockData = MarketingProductSku::find()
                ->select(['marketing_product_id', 'sum(marketing_stock) as marketing_stock'])
                ->where([
                    'product_id' => $productId,
                    'marketing_id' => $marketingId,
                    'marketing_type' => $marketingType,
                    'not_calculate' => 0,
                ])
                ->one();

            MarketingProduct::updateAll(['marketing_total_stock' => $stockData['marketing_stock']], ['id' => $stockData['marketing_product_id']]);
        }

        !empty($ids) && MarketingProductSku::updateAll(['is_min_price' => 1], ['in', 'id', $ids]);
    }

    /**
     * @param $product
     * @return array
     */
    protected function getDiscountAndType($product)
    {
        $discount = 0;
        $discount_type = DiscountTypeEnum::DISCOUNT;
        if (!empty($product['tmp_discount'])) {
            $discount = $product['tmp_discount'] ?? 0;
            $discount_type = DiscountTypeEnum::DISCOUNT;
        }

        if (!empty($product['tmp_deduction'])) {
            $discount = $product['tmp_deduction'] ?? 0;
            $discount_type = DiscountTypeEnum::MONEY;
        }

        if (!empty($product['tmp_price'])) {
            $discount = $product['tmp_price'] ?? 0;
            $discount_type = DiscountTypeEnum::FIXATION;
        }

        return [$discount, $discount_type];
    }

    /**
     * @param $marketingId
     * @param $marketingType
     * @param $marketingTimes
     * @return void
     * @throws \yii\db\Exception
     */
    protected function batchInsertOtherProductSku($marketingId, $marketingType, $marketingTimes)
    {
        $marketingProducts = Yii::$app->tinyShopService->marketingProduct->findByMarketingIdAndTypeWithSku($marketingId, $marketingType);
        // 插入商品
        $rows = [];
        $marketingProductIds = [];
        $skus = [];
        foreach ($marketingProducts as $marketingProduct) {
            $marketingProductIds[] = $marketingProduct['id'];
            $skus[$marketingProduct['product_id']] = $marketingProduct['sku'];

            unset($marketingProduct['id'], $marketingProduct['sku']);
            foreach ($marketingTimes as $marketingTime) {
                $marketingProduct['start_time'] = $marketingTime['start_time'];
                $marketingProduct['end_time'] = $marketingTime['end_time'];
                $marketingProduct['prediction_time'] = $marketingTime['prediction_time'];
                $marketingProduct['is_template'] = 0;

                $rows[] = $marketingProduct;
            }
        }

        !empty($rows) && Yii::$app->db->createCommand()->batchInsert(self::tableName(), array_keys($rows[0]), $rows)->execute();
        // 批量更新为模板字段
        !empty($marketingProductIds) && MarketingProduct::updateAll(['is_template' => StatusEnum::ENABLED], ['in', 'id', $marketingProductIds]);
        !empty($marketingProductIds) && MarketingProductSku::updateAll(['is_template' => StatusEnum::ENABLED], ['in', 'marketing_product_id', $marketingProductIds]);

        /******************************* 插入Sku *****************************/
        $newMarketingProducts = MarketingProduct::find()
            ->select(['id', 'product_id', 'start_time', 'end_time', 'prediction_time'])
            ->where(['marketing_id' => $marketingId, 'marketing_type' => $marketingType, 'is_template' => 0])
            ->asArray()
            ->all();

        $skuRows = [];
        foreach ($newMarketingProducts as $newMarketingProduct) {
            if (isset($skus[$newMarketingProduct['product_id']])) {
                foreach ($skus[$newMarketingProduct['product_id']] as $sku) {
                    $sku['start_time'] = $newMarketingProduct['start_time'];
                    $sku['end_time'] = $newMarketingProduct['end_time'];
                    $sku['prediction_time'] = $newMarketingProduct['prediction_time'];
                    $sku['marketing_product_id'] = $newMarketingProduct['id'];
                    $sku['is_template'] = 0;
                    unset($sku['id']);
                    $skuRows[] = $sku;
                }
            }
        }

        !empty($skuRows) && Yii::$app->db->createCommand()->batchInsert(MarketingProductSku::tableName(), array_keys($skuRows[0]), $skuRows)->execute();
    }

    /**
     * 获取预告时间
     *
     * @param $statTime
     * @return float|int
     */
    protected function getPredictionTime($statTime)
    {
        $predictionTime = $statTime;
        // 立即预告
        if ($this->advanceType == MarketingAdvanceTypeEnum::IMMEDIATE_NOTICE) {
            $predictionTime = time();
        }

        // 提前 N 小时预告
        if ($this->advanceType == MarketingAdvanceTypeEnum::ADVANCE_NOTICE && $statTime > 0) {
            $predictionTime = $statTime - 3600 * $this->advanceHour;
        }

        return $predictionTime;
    }
}
