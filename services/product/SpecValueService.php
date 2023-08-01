<?php

namespace addons\TinyShop\services\product;

use Yii;
use yii\helpers\Json;
use common\enums\StatusEnum;
use addons\TinyShop\common\models\product\SpecValue;
use addons\TinyShop\common\models\product\Product;

/**
 * Class SpecValueService
 * @package addons\TinyShop\services\product
 */
class SpecValueService
{
    /**
     * @param Product $product
     * @param $data
     */
    public function create(Product $product, $data)
    {
        !is_array($data) && $data = Json::decode($data);
        SpecValue::deleteAll(['product_id' => $product->id]);

        $rows = [];
        $field = [];
        foreach ($data as $datum) {
            $row = [
                'product_id' => $product->id,
                'merchant_id' => $product->merchant_id,
                'common_spec_id' => $datum['spec_id'],
                'common_spec_value_id' => $datum['id'],
                'title' => $datum['title'],
                'data' => $datum['data'] ?? '',
                'sort' => $datum['sort'] ?? 999,
                'pitch_on' => $datum['pitch_on'] ?? 0,
                'status' => StatusEnum::ENABLED,
                'created_at' => time(),
                'updated_at' => time(),
            ];

            $rows[] = $row;
            empty($field) && $field = array_keys($row);
        }

        !empty($rows) && Yii::$app->db->createCommand()->batchInsert(SpecValue::tableName(), $field, $rows)->execute();
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
                'common_spec_id' => $datum['common_spec_id'],
                'common_spec_value_id' => $datum['common_spec_value_id'],
                'title' => $datum['title'],
                'data' => $datum['data'],
                'sort' => $datum['sort'],
                'status' => $datum['status'],
                'pitch_on' => $datum['pitch_on'],
                'created_at' => time(),
                'updated_at' => time(),
            ];

            empty($field) && $field = array_keys($rows[0]);
        }

        !empty($rows) && Yii::$app->db->createCommand()->batchInsert(SpecValue::tableName(), $field, $rows)->execute();
    }

    /**
     * @param $product_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findByProductId($product_id)
    {
        return SpecValue::find()
            ->where(['status' => StatusEnum::ENABLED])
            ->andWhere(['product_id' => $product_id])
            ->asArray()
            ->all();
    }
}
