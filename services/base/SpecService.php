<?php

namespace addons\TinyShop\services\base;

use Yii;
use common\components\Service;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use addons\TinyShop\common\models\base\Spec;

/**
 * Class Spec
 * @package addons\TinyShop\common\services\base
 * @author jianyan74 <751393839@qq.com>
 */
class SpecService extends Service
{
    /**
     * 根据id数组获取列表并关联规格值
     *
     * @param array $ids
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getListWithValueByIds(array $ids)
    {
        return Spec::find()
            ->where(['status' => StatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->andWhere(['in', 'id', $ids])
            ->with(['value'])
            ->orderBy('sort asc, id desc')
            ->asArray()
            ->all();
    }

    /**
     * 根据id数组获取列表
     *
     * @param $ids
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findByIds(array $ids)
    {
        return Spec::find()
            ->where(['status' => StatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->andWhere(['in', 'id', $ids])
            ->asArray()
            ->all();
    }

    /**
     * @return array
     */
    public function getMapList()
    {
        return ArrayHelper::map($this->getList(), 'id', 'title');
    }

    /**
     * 获取列表
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getList()
    {
        return Spec::find()
            ->where(['status' => StatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->orderBy('sort asc, id desc')
            ->asArray()
            ->all();
    }
}