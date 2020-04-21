<?php

namespace addons\TinyShop\common\components\delivery;

use addons\TinyShop\common\models\forms\PreviewForm;
use addons\TinyShop\common\components\PreviewInterface;
use yii\web\UnprocessableEntityHttpException;

/**
 * 虚拟配送(上门验证)
 *
 * Class VirtualDelivery
 * @package addons\TinyShop\common\components\delivery
 * @author jianyan74 <751393839@qq.com>
 */
class VirtualDelivery extends PreviewInterface
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

        if (!$form->receiver_mobile) {
            throw new UnprocessableEntityHttpException('请填写手机号码');
        }

        if (!$form->receiver_name) {
            throw new UnprocessableEntityHttpException('请填写姓名');
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
        return 'virtual';
    }
}