<?php

namespace addons\TinyShop\services\product;

use Yii;
use common\enums\StatusEnum;
use common\components\Service;
use addons\TinyShop\common\models\product\Sku;
use addons\TinyShop\common\models\product\Product;
use yii\db\ActiveQuery;
use yii\web\UnprocessableEntityHttpException;

/**
 * Class Sku
 * @package addons\TinyShop\common\components\product
 * @author jianyan74 <751393839@qq.com>
 */
class SkuService extends Service
{
    /**
     * @param $id
     * @return array|null|\yii\db\ActiveRecord
     */
    public function findWithProductById($id, $member_id)
    {
        return Sku::find()
            ->where(['id' => $id])
            ->with(['product.myGet' => function(ActiveQuery $query) use ($member_id) {
                return $query->andWhere(['member_id' => $member_id]);
            }])
            ->asArray()
            ->one();
    }

    /**
     * 扣减库存
     *
     * @param $skuNums
     * @throws UnprocessableEntityHttpException
     */
    public function decrRepertory($skuNums, $decr = true)
    {
        $ids = array_keys($skuNums);
        $models = Sku::find()
            ->select(['id', 'product_id', 'stock', 'price', 'name'])
            ->with(['product'])
            ->where(['in', 'id', $ids])
            ->asArray()
            ->all();

        // 判断是否下架
        foreach ($models as $model) {
            if ($model['product']['product_status'] != StatusEnum::ENABLED) {
                throw new UnprocessableEntityHttpException($model['product']['name'] . '已下架');
            }

            if ($model['product']['status'] != StatusEnum::ENABLED) {
                throw new UnprocessableEntityHttpException($model['product']['name'] . '不存在');
            }

            if ($model['stock'] < abs($skuNums[$model['id']])) {
                throw new UnprocessableEntityHttpException($model['product']['name'] . '库存不足');
            }

            if ($model['product']['stock'] < abs($skuNums[$model['id']])) {
                throw new UnprocessableEntityHttpException($model['product']['name'] . '库存不足');
            }
        }

        // 判断是否扣减库存
        if ($decr == false) {
            return false;
        }

        foreach ($models as $model) {
            // 扣减数量
            $num = abs($skuNums[$model['id']]);
            if (!Sku::updateAllCounters(
                [
                    'stock' => - $num,
                ],
                [
                    'and',
                    ['id' => $model['id']],
                    ['>=', 'stock', $num],
                ]
            )) {
                throw new UnprocessableEntityHttpException($model['product']['name'] . '库存不足');
            }

            if (!Product::updateAllCounters(
                [
                    'stock' => - $num,
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

        return $models;
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
            $model->merchant_id = Yii::$app->services->merchant->getId();
        }

        $model->attributes = $data;
        $model->name = '';
        $model->product_id = $product_id;
        $model->save();
    }

    /**
 * @param $sku_id
 * @return array|\yii\db\ActiveRecord|null
 */
    public function findById($sku_id)
    {
        return Sku::find()
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->andFilterWhere(['id' => $sku_id])
            ->with('product')
            ->asArray()
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
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->with('product')
            ->asArray()
            ->all();
    }

    /**
     * 获取单个产品库存
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
     * @param $product_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findByProductId($product_id)
    {
        return Sku::find()
            ->where(['status' => StatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->andWhere(['product_id' => $product_id])
            ->orderBy('id asc')
            ->asArray()
            ->all();
    }

    /**
     * @param $product_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findEditByProductId($product_id)
    {
        return Sku::find()
            ->where(['>=', 'status', StatusEnum::DISABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->andWhere(['product_id' => $product_id])
            ->orderBy('id asc')
            ->asArray()
            ->all();
    }
}