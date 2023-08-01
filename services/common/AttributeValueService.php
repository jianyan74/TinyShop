<?php

namespace addons\TinyShop\services\common;

use Yii;
use addons\TinyShop\common\models\common\AttributeValue;

/**
 * Class AttributeValueService
 * @package addons\TinyShop\services\common
 */
class AttributeValueService
{
    /**
     * 更新数据
     *
     * @param array $data 提交的数据
     * @param array $oldValues 规格原先的数据
     * @param int $attribute_id
     * @param int $merchant_id
     * @throws \yii\db\Exception
     */
    public function updateData($data, $oldValues, $attribute_id, $merchant_id)
    {
        $allIds = [];
        if (isset($data['update'])) {
            foreach ($data['update']['id'] as $key => $datum) {
                if ($model = AttributeValue::findOne(['id' => $datum, 'attribute_id' => $attribute_id])) {
                    $model->title = $data['update']['title'][$key];
                    $model->type = $data['update']['type'][$key];
                    $model->value = $data['update']['value'][$key];
                    $model->sort = (int)$data['update']['sort'][$key];
                    $model->save();
                    $allIds[] = $model->id;
                }
            }
        }

        // 创建的内容
        if (isset($data['create'])) {
            $rows = [];
            foreach ($data['create']['title'] as $key => $datum) {
                $sort = (int)$data['create']['sort'][$key];
                $value = $data['create']['value'][$key];
                $type = $data['create']['type'][$key];
                $rows[] = [$merchant_id, $attribute_id, $datum, $value, $type, $sort, time(), time()];
            }

            $field = ['merchant_id', 'attribute_id', 'title', 'value', 'type', 'sort', 'created_at', 'updated_at'];
            !empty($rows) && Yii::$app->db->createCommand()->batchInsert(AttributeValue::tableName(), $field, $rows)->execute();
        }

        // 删除不存在的内容
        $deleteIds = [];
        foreach ($oldValues as $value) {
            !in_array($value['id'], $allIds) && $deleteIds[] = $value['id'];
        }

        !empty($deleteIds) && AttributeValue::deleteAll(['in', 'id', $deleteIds]);
    }
}