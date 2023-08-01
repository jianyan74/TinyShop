<?php

namespace addons\TinyShop\services\product;

use addons\TinyShop\common\models\order\Order;
use Yii;
use yii\db\ActiveQuery;
use yii\helpers\Json;
use yii\web\UnprocessableEntityHttpException;
use common\helpers\ArrayHelper;
use common\enums\StatusEnum;
use common\components\Service;
use addons\TinyShop\common\models\product\Product;
use addons\TinyShop\common\models\product\Sku;

/**
 * Class SkuService
 * @package addons\TinyShop\services\product
 * @author jianyan74 <751393839@qq.com>
 */
class SkuService extends Service
{
    /**
     * @param Product $product
     * @param $data
     * @throws \yii\db\Exception
     */
    public function create(Product $product, $data)
    {
        !is_array($data) && $data = Json::decode($data);
        if (empty($data)) {
            throw new UnprocessableEntityHttpException('请至少添加一个规格');
        }

        $skus = $this->findByProductId($product->id);
        $oldData = ArrayHelper::getColumn($skus, 'data');
        $newData = ArrayHelper::getColumn($data, 'data');
        list($updatedIds, $deleteData) = ArrayHelper::comparisonIds($oldData, $newData);
        // 获取规格Map
        $skus = ArrayHelper::arrayKey($skus, 'data');
        // 让购物车里面的sku失效
        if (!empty($deleteData)) {
            $deleteIds = [];
            foreach ($deleteData as $deleteDatum) {
               isset($skus[$deleteDatum]['id']) && $deleteIds[] = $skus[$deleteDatum]['id'];
            }

            // 删除失效的sku
            Sku::deleteAll([
                'and',
                ['product_id' => $product->id, 'merchant_id' => $product->merchant_id],
                ['in', 'id', $deleteIds],
            ]);

            Yii::$app->tinyShopService->memberCartItem->loseBySkus($deleteIds);
        }

        $rows = [];
        $field = [];
        $using = false;
        foreach ($data as $datum) {
            $datum['name'] = implode(' ', ArrayHelper::getColumn($datum['child'], 'title'));
            $datum['product_id'] = $product->id;
            $datum['merchant_id'] = $product->merchant_id;
            $datum['created_at'] = time();
            $datum['updated_at'] = time();
            // 校验数据是否规范
            $model = new Sku();
            $model->loadDefaultValues();
            $model->attributes = $datum;
            $model->sku_no = (string)$model->sku_no;
            !$model->validate() && $this->error($model);
            if ($model->status == StatusEnum::ENABLED) {
                $using = true;
            }

            // 更新数据
            $row = ArrayHelper::toArray($model);
            if (in_array($model->data, $updatedIds)) {
                $skuModel = $skus[$datum['data']];
                $skuModel->attributes = $row;
                !$skuModel->save() && $this->error($skuModel);
            } else {
                $rows[] = $row;
                empty($field) && $field = array_keys($row);
            }
        }

        if ($using === false) {
            throw new UnprocessableEntityHttpException('请至少启用一个规格');
        }

        !empty($rows) && Yii::$app->db->createCommand()->batchInsert(Sku::tableName(), $field, $rows)->execute();
    }

    /**
     * @param $product_id
     * @param $data
     * @throws \yii\db\Exception
     */
    public function createByCopy($product_id, $data)
    {
        $rows = $field = [];
        foreach ($data as $datum) {
            $rows[] = [
                'product_id' => $product_id,
                'merchant_id' => $datum['merchant_id'],
                'name' => $datum['name'],
                'picture' => $datum['picture'],
                'price' => $datum['price'],
                'data' => $datum['data'],
                'market_price' => $datum['market_price'],
                'cost_price' => $datum['cost_price'],
                'stock' => $datum['stock'],
                'sku_no' => $datum['sku_no'],
                'barcode' => $datum['barcode'],
                'weight' => $datum['weight'],
                'volume' => $datum['volume'],
                'status' => $datum['status'],
                'created_at' => time(),
                'updated_at' => time(),
            ];

            empty($field) && $field = array_keys($rows[0]);
        }

        !empty($rows) && Yii::$app->db->createCommand()->batchInsert(Sku::tableName(), $field, $rows)->execute();
    }

    /**
     * 扣除库存
     *
     * @param Order $order
     * @param $orderProduct
     * @param $stockDeductionType
     * @param $force
     * @return bool|void
     * @throws UnprocessableEntityHttpException
     */
    public function decrRepertory(Order $order, $orderProducts, $stockDeductionType = null, $force = true)
    {
        // 扣减库存数量
        $skuNums = [];
        foreach ($orderProducts as $item) {
            // 扣减库存类型判断
            if (
                $stockDeductionType != null &&
                $stockDeductionType != $item['stock_deduction_type']
            ) {
                continue;
            }

            if (!isset($skuNums[$item['sku_id']])) {
                $skuNums[$item['sku_id']] = 0;
            }

            $skuNums[$item['sku_id']] += $item['num'];
        }

        if (empty($skuNums)) {
            return false;
        }

        $ids = array_keys($skuNums);
        $models = Sku::find()
            ->select(['id', 'product_id', 'stock', 'price', 'name'])
            ->with(['product'])
            ->where(['in', 'id', $ids])
            ->asArray()
            ->all();

        // 判断是否下架
        foreach ($models as $model) {
            if (
                $model['product']['audit_status'] != StatusEnum::ENABLED ||
                $model['product']['status'] != StatusEnum::ENABLED
            ) {
                throw new UnprocessableEntityHttpException($model['product']['name'] . '已下架或不存在');
            }

            if (
                $model['stock'] < abs($skuNums[$model['id']]) ||
                $model['product']['stock'] < abs($skuNums[$model['id']])
            ) {
                throw new UnprocessableEntityHttpException($model['product']['name'] . '库存不足');
            }
        }

        // 判断是否扣减库存
        if ($force == false) {
            return false;
        }

        // 扣减数量
        foreach ($models as $model) {
            $num = abs($skuNums[$model['id']]);
            if (!Sku::updateAllCounters(['stock' => -$num], [
                    'and',
                    ['id' => $model['id']],
                    ['>=', 'stock', $num],
                ]
            )) {
                throw new UnprocessableEntityHttpException($model['product']['name'] . '库存不足');
            }

            if (!Product::updateAllCounters([
                'stock' => -$num,
                'real_sales' => $num,
                'total_sales' => $num
            ],
                [
                    'and',
                    ['id' => $model['product_id']],
                    ['>=', 'stock', $num],
                ]
            )) {
                throw new UnprocessableEntityHttpException($model['product']['name'] . '库存不足');
            }
        }
    }

    /**
     * @param $productId
     * @param $pitchOn
     * @return array
     */
    public function getJsData($productId, $pitchOn)
    {
        $pitchOn = ArrayHelper::arrayKey($pitchOn, 'id');
        $sku = $this->findByProductId($productId);
        $sku = ArrayHelper::toArray($sku);
        foreach ($sku as &$item) {
            $item['child'] = [];
            $dataArr = explode('-', $item['data']);
            foreach ($dataArr as $value) {
                if (isset($pitchOn[$value])) {
                    $item['child'][] = [
                        'id' => $pitchOn[$value]['id'],
                        'title' => $pitchOn[$value]['title'],
                    ];
                }
            }
        }

        return ArrayHelper::arrayKey($sku, 'data');
    }

    /**
     * 写入
     *
     * @param $product_id
     * @param $data
     */
    public function saveByProductId($product_id, $data)
    {
        if (!($model = Sku::find()->where(['product_id' => $product_id])->one())) {
            $model = new Sku();
            $model = $model->loadDefaultValues();
            $model->merchant_id = Yii::$app->services->merchant->getNotNullId();
        }

        $model->attributes = $data;
        $model->name = '';
        $model->product_id = $product_id;
        $model->save();
    }

    /**
     * 获取单个商品库存
     *
     * @param $product_id
     * @return false|null|string
     */
    public function getStockByProductId($product_id)
    {
        $stock = Sku::find()
            ->select(['sum(stock) as all_stock'])
            ->where(['product_id' => $product_id])
            ->andWhere(['>', 'stock', 0])
            ->asArray()
            ->scalar();

        return $stock ?? 0;
    }

    /**
     * @param $id
     * @return array|null|\yii\db\ActiveRecord
     */
    public function findWithProductById($id, $member_id)
    {
        return Sku::find()
            ->where(['id' => $id])
            ->with([
                'product.marketingProduct',
                'product.cateMap',
                'product.myGet' => function (ActiveQuery $query) use ($member_id) {
                    return $query->andWhere(['buyer_id' => $member_id]);
                }
            ])
            ->asArray()
            ->one();
    }

    /**
     * @param $id
     * @return array|null|\yii\db\ActiveRecord
     */
    public function findWithProductByIds($ids, $member_id)
    {
        return Sku::find()
            ->where(['in', 'id', $ids])
            ->with([
                'product.marketingProduct',
                'product.cateMap',
                'product.myGet' => function (ActiveQuery $query) use ($member_id) {
                    return $query->andWhere(['buyer_id' => $member_id]);
                }
            ])
            ->asArray()
            ->all();
    }

    /**
     * @param $sku_id
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findById($sku_id)
    {
        return Sku::find()
            ->andFilterWhere(['id' => $sku_id])
            ->with('product')
            ->one();
    }

    /**
     * @param array $sku_ids
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findByIds(array $sku_ids)
    {
        return Sku::find()
            ->where(['in', 'id', $sku_ids])
            ->with('product.cateMap')
            ->asArray()
            ->all();
    }

    /**
     * @param $product_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findByProductId($product_id)
    {
        return Sku::find()
            ->where(['product_id' => $product_id])
            ->andWhere(['>=', 'status', StatusEnum::DISABLED])
            ->all();
    }

    /**
     * @param $barcode
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findByBarcode($barcode, $merchant_id, $store_id)
    {
        return Sku::find()
            ->where(['barcode' => $barcode])
            ->andFilterWhere(['merchant_id' => $merchant_id])
            ->with(['product', 'repertoryStock' => function (ActiveQuery $query) use ($merchant_id, $store_id) {
                return $query->andWhere([
                    'merchant_id' => $merchant_id,
                    'store_id' => $store_id
                ]);
            }])
            ->asArray()
            ->one();
    }
}
