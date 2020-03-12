<?php

namespace addons\TinyShop\services\product;

use common\components\Service;
use common\enums\StatusEnum;
use addons\TinyShop\common\models\product\Evaluate;

/**
 * Class ProductEvaluateService
 * @package addons\TinyShop\services\product
 * @author jianyan74 <751393839@qq.com>
 */
class EvaluateService extends Service
{
    /**
     * @param $order_product_id
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findByOrderProductId($order_product_id)
    {
        return Evaluate::find()
            ->where(['order_product_id' => $order_product_id])
            ->andWhere(['status' => StatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->one();
    }

    /**
     * @return int|string
     */
    public function getCount()
    {
        return Evaluate::find()
            ->select('id')
            ->andWhere(['>', 'status', StatusEnum::DISABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->count();
    }
}