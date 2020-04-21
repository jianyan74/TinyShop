<?php

namespace addons\TinyShop\services\product;

use common\enums\StatusEnum;
use yii\web\UnprocessableEntityHttpException;
use common\components\Service;
use addons\TinyShop\common\enums\PointExchangeTypeEnum;
use addons\TinyShop\common\models\product\LadderPreferential;

/**
 * Class LadderPreferentialService
 * @package addons\TinyShop\services\product
 * @author jianyan74 <751393839@qq.com>
 */
class LadderPreferentialService extends Service
{
    /**
     * @param array $data
     * @param $product_id
     * @param int $max_buy 最大可购买数量
     * @param int $is_open_presell 预售
     * @param int $point_exchange_type 产品类型
     * @param $min_price
     * @throws UnprocessableEntityHttpException
     */
    public function create(array $data, $product_id, $max_buy, $is_open_presell, $point_exchange_type, $min_price)
    {
        LadderPreferential::deleteAll(['product_id' => $product_id]);

        foreach ($data as $datum) {
            if ($max_buy > 0  && $max_buy < $datum['quantity']) {
                throw new UnprocessableEntityHttpException('阶梯优惠数量不可超出限购数量');
            }

            if (!empty($datum['quantity']) && !empty($datum['price'])) {
                if ($is_open_presell == StatusEnum::ENABLED) {
                    throw new UnprocessableEntityHttpException('预售产品不可参与阶梯优惠');
                }

                if (PointExchangeTypeEnum::isIntegralBuy($point_exchange_type)) {
                    throw new UnprocessableEntityHttpException('积分产品不可参与阶梯优惠');
                }

                if ($datum['quantity'] < 2) {
                    throw new UnprocessableEntityHttpException('阶梯优惠数量不可少于 2');
                }

                if (($datum['price'] / $datum['quantity']) > $min_price) {
                    throw new UnprocessableEntityHttpException('阶梯优惠金额不可大于 ' . $min_price * $datum['quantity']);
                }

                $model = new LadderPreferential();
                $model->product_id = $product_id;
                $model->attributes = $datum;

                if (!$model->save()) {
                    throw new UnprocessableEntityHttpException('阶梯优惠' . $this->getError($model));
                }
            }
        }
    }

    /**
     * 阶梯金额
     *
     * @param $data
     * @param $total_num
     * @return bool
     */
    public function getPrice($data, $total_num)
    {
        foreach ($data as $datum) {
            if ($datum['quantity'] <= $total_num) {
                return $datum;
            }
        }

        return false;
    }
}