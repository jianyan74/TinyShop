<?php

namespace addons\TinyShop\common\components\delivery;

use yii\web\UnprocessableEntityHttpException;
use addons\TinyShop\common\components\PreviewInterface;
use addons\TinyShop\common\forms\PreviewForm;

/**
 * 到店
 *
 * Class ToStoreDelivery
 * @package addons\TinyShop\common\components\delivery
 * @author jianyan74 <751393839@qq.com>
 */
class ToStoreDelivery extends PreviewInterface
{
    /**
     * @param PreviewForm $form
     * @return PreviewForm
     * @throws UnprocessableEntityHttpException
     */
    public function execute(PreviewForm $form): PreviewForm
    {
        if (!$form->store) {
            throw new UnprocessableEntityHttpException('门店地点不存在');
        }

        $form->shipping_money = 0;

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
        return 'toStore';
    }
}