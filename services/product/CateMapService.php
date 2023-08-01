<?php

namespace addons\TinyShop\services\product;

use Yii;
use common\components\Service;
use addons\TinyShop\common\models\product\CateMap;

/**
 * Class CateMapService
 * @package addons\TinyShop\services\product
 * @author jianyan74 <751393839@qq.com>
 */
class CateMapService extends Service
{
    /**
     * 创建关联
     *
     * @param $product_id
     * @param array $cate_ids
     */
    public function create($product_id, array $cate_ids, $merchant_id = 0)
    {
        CateMap::deleteAll(['product_id' => $product_id, 'merchant_id' => $merchant_id]);

        $rows = [];
        $cate = Yii::$app->tinyShopService->productCate->findAllByIds($cate_ids);
        foreach ($cate as $item) {
            $rows[] = [
                'cate_id' => $item['id'],
                'product_id' => $product_id,
                'merchant_id' => $item['merchant_id'],
            ];
        }

        // 判断插入
        $field = ['cate_id', 'product_id', 'merchant_id'];
        !empty($rows) && Yii::$app->db->createCommand()->batchInsert(CateMap::tableName(), $field, $rows)->execute();
    }

    /**
     * 获取所有的分类id
     *
     * @param $product_id
     * @return array
     */
    public function findByProductId($product_id, $merchant_id = '')
    {
        return CateMap::find()
            ->select(['cate_id'])
            ->where(['product_id' => $product_id])
            ->andFilterWhere(['merchant_id' => $merchant_id])
            ->column();
    }

    /**
     * 获取所有的分类id
     *
     * @param $product_id
     * @return array|CateMap
     */
    public function findOneByProductId($product_id)
    {
        return CateMap::find()
            ->with(['cate'])
            ->where(['product_id' => $product_id])
            ->andWhere(['merchant_id' => 0])
            ->one();
    }

    /**
     * 获取所有的商品id
     *
     * @param $product_id
     * @return array
     */
    public function findByCateIds($cate_ids)
    {
        return CateMap::find()
            ->select(['product_id'])
            ->where(['in', 'cate_id', $cate_ids])
            ->column();
    }

    /**
     * @param $cate_id
     * @param $product_id
     */
    public function findSaveById($cate_id, $product_id)
    {
        CateMap::deleteAll(['product_id' => $product_id, 'merchant_id' => 0]);

        $model = new CateMap();
        $model->merchant_id = 0;
        $model->cate_id = $cate_id;
        $model->product_id = $product_id;
        $model->save();
    }
}