<?php

namespace addons\TinyShop\services\product;

use addons\TinyShop\common\enums\ExplainStatusEnum;
use Yii;
use addons\TinyShop\api\modules\v1\forms\EvaluateForm;
use common\components\Service;
use common\enums\StatusEnum;
use addons\TinyShop\common\models\product\Evaluate;
use yii\web\UnprocessableEntityHttpException;

/**
 * Class ProductEvaluateService
 * @package addons\TinyShop\services\product
 * @author jianyan74 <751393839@qq.com>
 */
class EvaluateService extends Service
{
    /**
     * 自动评价
     *
     * @param $evaluate_day
     * @param $evaluate
     */
    public function autoEvaluate($evaluate_day, $evaluate)
    {
        $data = Yii::$app->tinyShopService->order->findEvaluateData($evaluate_day);
        foreach ($data as $datum) {
            foreach ($datum['product'] as $item) {
                if ($item->is_evaluate == ExplainStatusEnum::DEAULT) {
                    $model = new EvaluateForm();
                    $model = $model->loadDefaultValues();
                    $model->setProduct($item);
                    $model->order_product_id = $item['id'];
                    $model->scores = 5;
                    $model->content = $evaluate;

                    if (!$model->save()) {
                        throw new UnprocessableEntityHttpException($this->getError($model));
                    }
                }
            }
        }
    }

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