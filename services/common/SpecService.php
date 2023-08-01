<?php

namespace addons\TinyShop\services\common;

use common\components\Service;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use addons\TinyShop\common\models\common\Spec;

/**
 * Class SpecService
 * @package addons\TinyShop\services\common
 * @author jianyan74 <751393839@qq.com>
 */
class SpecService extends Service
{
    /**
     * 根据id数组获取列表
     *
     * @param $ids
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findByIds(array $ids)
    {
        return Spec::find()
            ->where(['status' => StatusEnum::ENABLED, 'is_tmp' => StatusEnum::DISABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->andWhere(['in', 'id', $ids])
            ->with(['value'])
            ->asArray()
            ->all();
    }

    /**
     * @param $id
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findById($id)
    {
        return Spec::find()
            ->where([
                'id' => $id,
                'status' => StatusEnum::ENABLED,
            ])
            ->asArray()
            ->one();
    }

    /**
     * @return array
     */
    public function getMap()
    {
        return ArrayHelper::map($this->findAll(), 'id', 'title');
    }

    /**
     * 获取列表
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findAll()
    {
        return Spec::find()
            ->where(['status' => StatusEnum::ENABLED])
            ->andWhere(['is_tmp' => StatusEnum::DISABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->orderBy('sort asc, id desc')
            ->asArray()
            ->all();
    }
}
