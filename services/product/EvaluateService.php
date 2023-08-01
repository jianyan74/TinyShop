<?php

namespace addons\TinyShop\services\product;

use Yii;
use yii\web\UnprocessableEntityHttpException;
use common\components\Service;
use common\enums\StatusEnum;
use addons\TinyShop\common\models\product\Evaluate;
use addons\TinyShop\common\forms\EvaluateForm;
use addons\TinyShop\common\enums\ExplainStatusEnum;
use addons\TinyShop\common\forms\SettingForm;

/**
 * Class EvaluateService
 * @package addons\TinyShop\services\product
 * @author jianyan74 <751393839@qq.com>
 */
class EvaluateService extends Service
{
    /**
     * 自动评价
     *
     * @throws UnprocessableEntityHttpException
     */
    public function autoEvaluate()
    {
        $data = Yii::$app->tinyShopService->order->findEvaluateData();
        foreach ($data as $datum) {
            /** @var SettingForm $setting */
            $setting = Yii::$app->tinyShopService->config->setting();
            foreach ($datum['product'] as $item) {
                if ($item->is_evaluate == ExplainStatusEnum::DEAULT) {
                    $model = new EvaluateForm();
                    $model = $model->loadDefaultValues();
                    $model->setProduct($item);
                    $model->order_product_id = $item['id'];
                    $model->scores = 5;
                    $model->is_auto = StatusEnum::ENABLED;
                    $model->content = $setting->order_evaluate;
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
     * 获取评价的头像
     *
     * @param $order_product_id
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findHeadPortraitByProductId($product_id, $limit = 3)
    {
        return Evaluate::find()
            ->select(['member_head_portrait'])
            ->where(['product_id' => $product_id])
            ->andWhere(['status' => StatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->limit($limit)
            ->column();
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
