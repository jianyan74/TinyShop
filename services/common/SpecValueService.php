<?php

namespace addons\TinyShop\services\common;

use Yii;
use common\components\Service;
use addons\TinyShop\common\models\common\SpecValue;

/**
 * Class SpecValueService
 * @package addons\TinyShop\services\common
 */
class SpecValueService extends Service
{
    /**
     * 更新数据
     *
     * @param array $data 提交的数据
     * @param array $oldValues 规格原先的数据
     * @param int $spec_id
     * @param int $merchant_id
     * @throws \yii\db\Exception
     */
    public function updateData($data, $oldValues, $spec_id, $merchant_id)
    {
        $allIds = [];
        if (isset($data['update'])) {
            foreach ($data['update']['id'] as $key => $datum) {
                if ($model = SpecValue::findOne(['id' => $datum, 'spec_id' => $spec_id])) {
                    $model->title = $data['update']['title'][$key];
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
                $rows[] = [$merchant_id, $spec_id, $datum, (int)$data['create']['sort'][$key], time(), time()];
            }

            $field = ['merchant_id', 'spec_id', 'title', 'sort', 'created_at', 'updated_at'];
            !empty($rows) && Yii::$app->db->createCommand()->batchInsert(SpecValue::tableName(), $field, $rows)->execute();
        }

        // 删除不存在的内容
        $deleteIds = [];
        foreach ($oldValues as $value) {
            !in_array($value['id'], $allIds) && $deleteIds[] = $value['id'];
        }

        !empty($deleteIds) && SpecValue::deleteAll(['in', 'id', $deleteIds]);
    }

    /**
     * @param array $ids
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findByIds(array $ids)
    {
        return SpecValue::find()
            ->andWhere(['in', 'id', $ids])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->asArray()
            ->all();
    }
}