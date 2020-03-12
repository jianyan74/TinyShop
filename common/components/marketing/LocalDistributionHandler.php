<?php

namespace addons\TinyShop\common\components\marketing;

use yii\web\UnprocessableEntityHttpException;
use addons\TinyShop\common\enums\OrderTypeEnum;
use addons\TinyShop\common\components\PreviewInterface;
use addons\TinyShop\common\enums\ShippingTypeEnum;
use addons\TinyShop\common\models\forms\PreviewForm;

/**
 * 货到付款
 *
 * Class LocalDistributionHandler
 * @package addons\TinyShop\common\components\marketing
 * @author jianyan74 <751393839@qq.com>
 */
class LocalDistributionHandler extends PreviewInterface
{
    /**
     * @param PreviewForm $form
     * @return PreviewForm
     * @throws UnprocessableEntityHttpException
     */
    public function execute(PreviewForm $form): PreviewForm
    {
        // 验证是否货到付款
        if ($form->shipping_type != ShippingTypeEnum::LOCAL_DISTRIBUTION) {
            return $form;
        }

        // 成功触发
        return $this->success($form);
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
        return 'localDistribution';
    }
}