<?php

namespace addons\TinyShop\services\express;

use common\enums\StatusEnum;
use common\components\Service;
use common\helpers\ArrayHelper;
use addons\TinyShop\common\models\express\Company;

/**
 * Class CompanyService
 * @package addons\TinyShop\services\express
 * @author jianyan74 <751393839@qq.com>
 */
class CompanyService extends Service
{
    /**
     * @param $id
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findById($id)
    {
        return Company::find()
            ->where(['id' => $id, 'status' => StatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->one();
    }

    /**
     * @return array
     */
    public function getMapList()
    {
        return ArrayHelper::map($this->getList(), 'id', 'title');
    }

    /**
     * 获取默认物流
     *
     * @return array|null|\yii\db\ActiveRecord
     */
    public function getDefault()
    {
        return Company::find()
            ->where(['is_default' => StatusEnum::ENABLED, 'status' => StatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->asArray()
            ->one();
    }

    /**
     * 获取列表
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getList()
    {
        return Company::find()
            ->where(['status' => StatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->orderBy('is_default desc, sort asc, id desc')
            ->asArray()
            ->all();
    }
}