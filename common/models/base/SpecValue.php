<?php

namespace addons\TinyShop\common\models\base;

use common\behaviors\MerchantBehavior;
use Yii;

/**
 * This is the model class for table "{{%addon_shop_base_spec_value}}".
 *
 * @property string $id
 * @property string $merchant_id 商户id
 * @property int $spec_id 属性编码
 * @property string $title 选项名称
 * @property string $data 默认数据
 * @property int $sort 排序
 * @property int $status 状态(-1:已删除,0:禁用,1:正常)
 * @property string $created_at 创建时间
 * @property string $updated_at 修改时间
 */
class SpecValue extends \common\models\base\BaseModel
{
    use MerchantBehavior;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_shop_base_spec_value}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'spec_id', 'sort', 'status', 'created_at', 'updated_at'], 'integer'],
            [['spec_id', 'title'], 'required'],
            [['title'], 'string', 'max' => 125],
            [['data'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'merchant_id' => '商户id',
            'spec_id' => '规格id',
            'title' => '标题',
            'data' => '内容',
            'sort' => '排序',
            'status' => '状态',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * 更新数据
     *
     * @param array $data 提交的数据
     * @param array $oldValues 规格原先的数据
     * @param int $spec_id
     * @param int $merchant_id
     * @throws \yii\db\Exception
     */
    public static function updateData($data, $oldValues, $spec_id, $merchant_id)
    {
        $allIds = [];
        if (isset($data['update'])) {
            foreach ($data['update']['id'] as $key => $datum) {
                if ($model = self::findOne(['id' => $datum, 'spec_id' => $spec_id])) {
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
            !empty($rows) && Yii::$app->db->createCommand()->batchInsert(self::tableName(), $field, $rows)->execute();
        }

        // 删除不存在的内容
        $deleteIds = [];
        foreach ($oldValues as $value) {
            !in_array($value['id'], $allIds) && $deleteIds[] = $value['id'];
        }

        !empty($deleteIds) && self::deleteAll(['in', 'id', $deleteIds]);
    }
}
