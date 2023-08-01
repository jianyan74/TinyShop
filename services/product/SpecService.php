<?php

namespace addons\TinyShop\services\product;

use Yii;
use yii\helpers\Json;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use addons\TinyShop\common\models\product\Product;
use addons\TinyShop\common\models\product\Spec;
use addons\TinyShop\common\enums\SpecTypeEnum;

/**
 * Class SpecService
 * @package addons\TinyShop\services\product
 */
class SpecService
{
    /**
     * @param Product $product
     * @param $data
     */
    public function create(Product $product, $data)
    {
        Spec::deleteAll(['product_id' => $product->id]);
        if (empty($data)) {
            return false;
        }

        !is_array($data) && $data = Json::decode($data);

        $rows = [];
        $field = [];
        $values = [];
        foreach ($data as $datum) {
            $row = [
                'product_id' => $product->id,
                'merchant_id' => $product->merchant_id,
                'common_spec_id' => $datum['id'],
                'title' => $datum['title'],
                'sort' => $datum['sort'] ?? 999,
                'type' => $datum['type'] ?? SpecTypeEnum::TEXT,
                'status' => StatusEnum::ENABLED,
                'created_at' => time(),
                'updated_at' => time(),
            ];

            $values = ArrayHelper::merge($values, $datum['value']);

            $rows[] = $row;
            empty($field) && $field = array_keys($row);
        }

        !empty($rows) && Yii::$app->db->createCommand()->batchInsert(Spec::tableName(), $field, $rows)->execute();
        // 写入规格值
        Yii::$app->tinyShopService->productSpecValue->create($product, $values);
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
                'common_spec_id' => $datum['common_spec_id'],
                'title' => $datum['title'],
                'sort' => $datum['sort'],
                'type' => $datum['type'],
                'status' => $datum['status'],
                'created_at' => time(),
                'updated_at' => time(),
            ];

            empty($field) && $field = array_keys($rows[0]);
        }

        !empty($rows) && Yii::$app->db->createCommand()->batchInsert(Spec::tableName(), $field, $rows)->execute();
    }

    /**
     * @param $product_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getJsData($product_id)
    {
        $data = $this->findByProductId($product_id);
        $values = Yii::$app->tinyShopService->productSpecValue->findByProductId($product_id);

        $pitchOnData = [];
        foreach ($data as &$datum) {
            $datum['id'] = $datum['common_spec_id'];
            $datum['pitch_on_count'] = 0;
            unset($datum['common_spec_id']);

            $datum['value'] = [];
            foreach ($values as &$value) {
                $value['id'] = $value['common_spec_value_id'] ?? 0;
                $value['spec_id'] = $value['common_spec_id'] ?? 0;
                if ($datum['id'] == $value['spec_id']) {
                    unset($value['common_spec_value_id'], $value['common_spec_id']);

                    $datum['value'][] = $value;

                    // 判断选中
                    if ($value['pitch_on'] == StatusEnum::ENABLED) {
                        $pitchOnData[] = [
                            'id' => $value['id'],
                            'title' => $value['title'],
                            'parentId' => $value['spec_id'],
                            'parentTitle' => $datum['title'],
                        ];

                        $datum['pitch_on_count'] += 1;
                    }
                }
            }
        }

        return [$data, $pitchOnData];
    }

    /**
     * 获取规格列表
     *
     * @param $product_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getPitchOnByProductId($product_id)
    {
        $models = Spec::find()
            ->where(['product_id' => $product_id])
            ->select(['id', 'common_spec_id', 'title', 'type'])
            ->with(['valueBySpec' => function($query) use ($product_id) {
                return $query->andWhere([
                    'product_id' => $product_id,
                    'pitch_on' => StatusEnum::ENABLED
                ])->select(['id', 'common_spec_id', 'common_spec_value_id', 'title', 'data', 'status']);
            }])
            ->orderBy('sort asc')
            ->asArray()
            ->all();

        $data = [];
        foreach ($models as $model) {
            if (!empty($model['valueBySpec'])) {
                $data[] = [
                    'id' => $model['id'],
                    'common_spec_id' => $model['common_spec_id'],
                    'title' => $model['title'],
                    'type' => $model['type'],
                    'value' => $model['valueBySpec']
                ];
            }
        }

        return $data;
    }

    /**
     * @param $product_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findByProductId($product_id)
    {
        return Spec::find()
            ->where(['status' => StatusEnum::ENABLED])
            ->andWhere(['product_id' => $product_id])
            ->asArray()
            ->all();
    }
}
