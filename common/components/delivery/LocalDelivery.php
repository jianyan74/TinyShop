<?php

namespace addons\TinyShop\common\components\delivery;

use Yii;
use yii\web\UnprocessableEntityHttpException;
use addons\TinyShop\common\forms\PreviewForm;
use addons\TinyShop\common\components\PreviewInterface;

/**
 * 同城配送(本地)
 *
 * Class LocalDelivery
 * @package addons\TinyShop\common\components\delivery
 * @author jianyan74 <751393839@qq.com>
 */
class LocalDelivery extends PreviewInterface
{
    /**
     * @param PreviewForm $form
     * @return PreviewForm
     * @throws UnprocessableEntityHttpException
     */
    public function execute(PreviewForm $form): PreviewForm
    {
        if (empty($form->address)) {
            throw new UnprocessableEntityHttpException('收货地址不存在');
        }

        $cashAgainst = Yii::$app->tinyShopService->localArea->findOne($form->merchant_id);
        if ($form->address && $this->isNewRecord && $cashAgainst && !in_array($form->address->area_id, explode(',', $cashAgainst['area_ids']))) {
            throw new UnprocessableEntityHttpException('暂不支持该地区的配送');
        }

        if (empty($form->subscribe_shipping_start_time)) {
            throw new UnprocessableEntityHttpException('未选择配送时间');
        }

        // 配送费
        $form->shipping_money = Yii::$app->tinyShopService->localConfig->getShippingFeeByMerchantId($form->merchant_id, $form->product_money);

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
        return 'local';
    }
}
