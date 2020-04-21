<?php

namespace addons\TinyShop\services\product;

use Yii;
use common\enums\StatusEnum;
use common\components\Service;
use addons\TinyShop\common\models\product\Sku;
use addons\TinyShop\common\models\product\Product;
use yii\db\ActiveQuery;

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
            ->with(['product.ladderPreferential', 'product.myGet' => function(ActiveQuery $query) use ($member_id) {
                return $query->andWhere(['member_id' => $member_id]);
            }])
            ->asArray()
            ->one();
    }

    /**
     * 扣减库存
     *
     * @param $skuNums
     */
    public function decrRepertory($skuNums)
    {
        $ids = array_keys($skuNums);
        $models = Sku::find()
            ->where(['in', 'id', $ids])
            ->with(['product'])
            ->all();

        foreach ($models as $model) {
            $num = $skuNums[$model['id']];
            $model->stock -= $num;
            $model->save();

            /** @var Product $product */
            $product = $model->product;
            $product->stock -= $num;
            $product->real_sales += $num;
            $product->save();
        }
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
}