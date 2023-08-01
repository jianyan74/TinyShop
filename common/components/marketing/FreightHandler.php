<?php

namespace addons\TinyShop\common\components\marketing;

use Yii;
use yii\web\UnprocessableEntityHttpException;
use addons\TinyShop\common\components\delivery\PickupDelivery;
use addons\TinyShop\common\components\delivery\LocalDelivery;
use addons\TinyShop\common\forms\PreviewForm;
use addons\TinyShop\common\components\PreviewInterface;

/**
 * 运费计算
 *
 * 如果是自提不计算运费
 *
 * Class FreightHandler
 * @package addons\TinyShop\common\components
 * @author jianyan74 <751393839@qq.com>
 */
class FreightHandler extends PreviewInterface
{
    /**
     * @param PreviewForm $form
     * @return PreviewForm
     * @throws UnprocessableEntityHttpException
     */
    public function execute(PreviewForm $form): PreviewForm
    {
        $form->shipping_money = 0;
        if ($form->freight == true && $form->is_full_mail == false && $form->address) {
            try {
                $form->shipping_money = Yii::$app->tinyShopService->expressFee->getPrice(
                    $form->defaultProducts,
                    $form->fullProductIds,
                    $form->company_id,
                    $form->merchant_id,
                    $form->address,
                    $form->config['logistics_select']
                );
            } catch (\Exception $e) {
                // 下单才开始报错
                if ($this->isNewRecord) {
                    throw new UnprocessableEntityHttpException($e->getMessage());
                }

                return $form;
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
            PickupDelivery::getName(), // 自提
            LocalDelivery::getName(), // 同城配送
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
        return 'freight';
    }
}
