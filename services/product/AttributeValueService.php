<?php

namespace addons\TinyShop\services\product;

use Yii;
use yii\helpers\Json;
use common\enums\StatusEnum;
use addons\TinyShop\common\enums\AttributeValueTypeEnum;
use addons\TinyShop\common\models\product\AttributeValue;

/**
 * Class AttributeValueService
 * @package addons\TinyShop\services\product
 * @author jianyan74 <751393839@qq.com>
 */
class AttributeValueService
{
    /**
     * @param $product_id
     * @param $merchant_id
     * @param $data
     */
    public function create($product_id, $merchant_id, $data)
    {
        AttributeValue::deleteAll(['product_id' => $product_id]);
        if (empty($data)) {
            return;
        }

        $rows = [];
        !is_array($data) && $data = Json::decode($data);
        foreach ($data as $key => $item) {
            if (!empty($item['title'])) {
                if ($item['data'] && is_array($item['data'])) {
                    $item['data'] = implode(',', $item['data']);
                }

                if (isset($item['value']) && is_array($item['value'])) {
                    $item['value'] = implode(',', $item['value']);
                }

                empty($item['data']) && $item['data'] = '';
                empty($item['value']) && $item['value'] = '';

                $row = [
                    'merchant_id' => $merchant_id,
                    'product_id' => $product_id,
                    'title' => $item['title'],
                    'type' => $item['type'] ?? 1,
                    'sort' => $item['sort'],
                    'data' => $item['data'] ?? '',
                    'value' => $item['value'] ?? '',
                ];

                $rows[] = $row;
            }
        }

        // 判断插入
        $field = ['merchant_id', 'product_id', 'title', 'type', 'sort', 'data', 'value'];
        !empty($rows) && Yii::$app->db->createCommand()->batchInsert(AttributeValue::tableName(), $field, $rows)->execute();
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
                'merchant_id' => $datum['merchant_id'],
                'product_id' => $product_id,
                'type' => $datum['type'],
                'data' => $datum['data'],
                'title' => $datum['title'],
                'value' => $datum['value'],
                'sort' => $datum['sort'],
                'status' => $datum['status'],
                'created_at' => time(),
                'updated_at' => time(),
            ];

            empty($field) && $field = array_keys($rows[0]);
        }

        !empty($rows) && Yii::$app->db->createCommand()->batchInsert(AttributeValue::tableName(), $field, $rows)->execute();
    }

    /**
     * @param $product_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findByProductId($product_id)
    {
        $data = AttributeValue::find()
            ->where([
                'product_id' => $product_id,
                'status' => StatusEnum::ENABLED
            ])
            ->orderBy('sort asc')
            ->asArray()
            ->all();

        foreach ($data as &$datum) {
            if ($datum['type'] != AttributeValueTypeEnum::TEXT) {
                $datum['value'] = explode(',', $datum['value']);
            }

            if ($datum['type'] == AttributeValueTypeEnum::CHECK) {
                $datum['data'] = explode(',', $datum['data']);
            }
        }

        return $data;
    }
}
