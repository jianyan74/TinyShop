<?php

namespace addons\TinyShop\services\common;

use common\enums\StatusEnum;
use common\components\Service;
use common\helpers\ArrayHelper;
use addons\TinyShop\common\models\common\ExpressCompany;

/**
 * Class ExpressCompanyService
 * @package addons\TinyShop\services\common
 * @author jianyan74 <751393839@qq.com>
 */
class ExpressCompanyService extends Service
{
    /**
     * @param $id
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findById($id)
    {
        return ExpressCompany::find()
            ->where(['id' => $id, 'status' => StatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->one();
    }

    /**
     * @return array
     */
    public function getMapList()
    {
        return ArrayHelper::map($this->getList($this->getMerchantId()), 'id', 'title');
    }

    /**
     * @param $id
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findByTitles($titles)
    {
        return ExpressCompany::find()
            ->where(['status' => StatusEnum::ENABLED])
            ->andWhere(['in', 'title', $titles])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->asArray()
            ->all();
    }

    /**
     * 获取默认物流
     *
     * @return array|null|\yii\db\ActiveRecord
     */
    public function getDefault($merchant_id)
    {
        return ExpressCompany::find()
            ->where(['is_default' => StatusEnum::ENABLED, 'status' => StatusEnum::ENABLED])
            ->andWhere(['merchant_id' => $merchant_id])
            ->asArray()
            ->one();
    }

    /**
     * 获取最后添加物流
     *
     * @return array|null|\yii\db\ActiveRecord
     */
    public function getLast($merchant_id)
    {
        return ExpressCompany::find()
            ->where(['status' => StatusEnum::ENABLED])
            ->andWhere(['merchant_id' => $merchant_id])
            ->orderBy('id desc')
            ->asArray()
            ->one();
    }

    /**
     * 获取列表
     *
     * @param $merchant_id
     * @param $select
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getList($merchant_id, $select = ['*'])
    {
        return ExpressCompany::find()
            ->select($select)
            ->where(['status' => StatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $merchant_id])
            ->orderBy('is_default desc, sort asc, id desc')
            ->asArray()
            ->all();
    }
}
