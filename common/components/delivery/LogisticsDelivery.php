<?php

namespace addons\TinyShop\common\components\delivery;

use Yii;
use yii\web\UnprocessableEntityHttpException;
use common\enums\StatusEnum;
use addons\TinyShop\common\forms\PreviewForm;
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

        // 物流配送
        if ($form->config['logistics'] == StatusEnum::DISABLED) {
            throw new UnprocessableEntityHttpException('物流配送已关闭');
        }

        if (empty($form->address)) {
            throw new UnprocessableEntityHttpException('找不到收货地址');
        }

        // 自选物流
        if ($form->config['logistics_select'] == StatusEnum::ENABLED && (!$form->company_id || !Yii::$app->tinyShopService->expressCompany->findById($form->company_id))) {
            throw new UnprocessableEntityHttpException('请选择物流公司');
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
