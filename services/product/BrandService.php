<?php

namespace addons\TinyShop\services\product;

use Yii;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use common\components\Service;
use addons\TinyShop\common\models\product\Brand;

/**
 * Class Brand
 * @package addons\TinyShop\common\components\product
 * @author jianyan74 <751393839@qq.com>
 */
class BrandService extends Service
{
    /**
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findByTitle($title)
    {
        return Brand::find()
            ->where(['title' => $title])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->asArray()
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
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getList()
    {
        return Brand::find()
            ->where(['status' => StatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->orderBy('sort asc, id desc')
            ->asArray()
            ->all();
    }
}