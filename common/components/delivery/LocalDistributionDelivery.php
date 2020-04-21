<?php

namespace addons\TinyShop\common\components\delivery;

use Yii;
use yii\web\UnprocessableEntityHttpException;
use addons\TinyShop\common\models\forms\PreviewForm;
use addons\TinyShop\common\components\PreviewInterface;

/**
 * 本地配送
 *
 * Class LocalDistributionDelivery
 * @package addons\TinyShop\common\components\delivery
 * @author jianyan74 <751393839@qq.com>
 */
class LocalDistributionDelivery extends PreviewInterface
{
    /**
     * @param PreviewForm $form
     * @return PreviewForm
     * @throws UnprocessableEntityHttpException
     */
    public function execute(PreviewForm $form): PreviewForm
    {
        $cashAgainst = Yii::$app->tinyShopService->baseCashAgainstArea->findOne($form->merchant_id);
        if ($form->address && $this->isNewRecord && $cashAgainst && !in_array($form->address->area_id, explode(',', $cashAgainst['area_ids']))) {
            throw new UnprocessableEntityHttpException('暂不支持该地区的配送');
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
        return 'localDistribution';
    }
}