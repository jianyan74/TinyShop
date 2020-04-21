<?php

namespace addons\TinyShop\common\components\delivery;

use Yii;
use yii\web\UnprocessableEntityHttpException;
use addons\TinyShop\common\models\forms\PreviewForm;
use addons\TinyShop\common\components\PreviewInterface;

/**
 * 物流配送
 *
 * Class LogisticsDelivery
 * @package addons\TinyShop\common\components\delivery
 * @author jianyan74 <751393839@qq.com>
 */
class LogisticsDelivery extends PreviewInterface
{
    /**
     * @param PreviewForm $form
     * @return PreviewForm
     * @throws UnprocessableEntityHttpException
     */
    public function execute(PreviewForm $form): PreviewForm
    {
        if ($this->isNewRecord == false) {
            return $form;
        }

        if (empty($form->address)) {
            throw new UnprocessableEntityHttpException('收货地址不存在');
        }

        // 自选物流
        if (!empty($form->is_logistics)) {
            if (!$form->company_id || !Yii::$app->tinyShopService->expressCompany->findById($form->company_id)) {
                throw new UnprocessableEntityHttpException('请选择物流公司');
            }
        }

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
        return 'logistics';
    }
}