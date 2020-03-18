<?php

namespace addons\TinyShop\common\components\marketing;

use Yii;
use addons\TinyShop\common\models\forms\PreviewForm;
use addons\TinyShop\common\components\PreviewInterface;
use yii\web\UnprocessableEntityHttpException;

/**
 * 运费计算
 *
 * 如果是自提不计算运费
 *
 * Class FeeHandler
 * @package addons\TinyShop\common\components
 * @author jianyan74 <751393839@qq.com>
 */
class FeeHandler extends PreviewInterface
{
    /**
     * @param PreviewForm $form
     * @return PreviewForm|mixed
     * @throws \yii\web\NotFoundHttpException
     * @throws \yii\web\UnprocessableEntityHttpException
     */
    public function execute(PreviewForm $form): PreviewForm
    {
        $form->shipping_money = 0;

        if ($form->address) {
            try {
                $form->shipping_money = Yii::$app->tinyShopService->expressFee->getPrice($form->defaultProducts, $form->company_id, $form->address);
            }catch (\Exception $e) {
                if ($this->isNewRecord) {
                    throw new UnprocessableEntityHttpException($e->getMessage());
                }
            }
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
        return [
            PickupHandler::getName(), // 自提
            FullMailHandler::getName(), // 满包邮
        ];
    }

    /**
     * 营销名称
     *
     * @return string
     */
    public static function getName(): string
    {
        return 'fee';
    }
}