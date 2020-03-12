<?php

namespace addons\TinyShop\services\base;

use Yii;
use common\enums\StatusEnum;
use common\components\Service;
use addons\TinyShop\common\models\base\AttributeValue;

/**
 * Class AttributeValueService
 * @package addons\TinyShop\services\base
 * @author jianyan74 <751393839@qq.com>
 */
class AttributeValueService extends Service
{
    /**
     * 根据id获取数据列表
     *
     * @param $ids
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findByIds(array $ids)
    {
        return AttributeValue::find()
            ->where(['status' => StatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->where(['in', 'id', $ids])
            ->select(['id', 'title'])
            ->orderBy('sort asc, id desc')
            ->asArray()
            ->all();
    }
}