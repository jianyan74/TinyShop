<?php

namespace addons\TinyShop\common\components\delivery;

use Yii;
use yii\web\UnprocessableEntityHttpException;
use addons\TinyShop\common\components\PreviewInterface;
use addons\TinyShop\common\forms\PreviewForm;
use common\enums\StatusEnum;

/**
 * 门店自提
 *
 * Class PickupDelivery
 * @author jianyan74 <751393839@qq.com>
 */
class PickupDelivery extends PreviewInterface
{
    /**
     * @param PreviewForm $form
     * @return PreviewForm
     * @throws UnprocessableEntityHttpException
     */
    public function execute(PreviewForm $form): PreviewForm
    {
        if ($form->config['logistics_local_distribution'] != StatusEnum::ENABLED) {
            throw new UnprocessableEntityHttpException('未开启商品自提');
        }

        if (!$form->store_id) {
            throw new UnprocessableEntityHttpException('请选择自提地点');
        }

        if (!($form->store = Yii::$app->tinyStoreService->store->findById($form->store_id))) {
            throw new UnprocessableEntityHttpException('自提地点不存在');
        }

        if (empty($form->subscribe_shipping_start_time)) {
            throw new UnprocessableEntityHttpException('未选择自提时间');
        }

        // 计算运费
        $form->shipping_money = Yii::$app->tinyStoreService->config->getFreight($form->pay_money, $form->merchant_id);

        return $form;
    }

    /**
     * 排斥营销
     *
     * @return array
     */
    public function rejectNames()
    {
        return [];
    }

    /**
     * 营销名称
     *
     * @return string
     */
    public static function getName(): string
    {
        return 'pickup';
    }
}
