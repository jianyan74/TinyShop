<?php

namespace addons\TinyShop\common\components\delivery;

use Yii;
use yii\web\UnprocessableEntityHttpException;
use addons\TinyShop\common\models\forms\PreviewForm;

/**
 * 货到付款
 *
 * Class CashAgainstDelivery
 * @package addons\TinyShop\common\components\marketing
 * @author jianyan74 <751393839@qq.com>
 */
class CashAgainstDelivery extends LogisticsDelivery
{
    /**
     * @param PreviewForm $form
     * @return PreviewForm
     * @throws UnprocessableEntityHttpException
     */
    public function execute(PreviewForm $form): PreviewForm
    {
        $form = parent::execute($form);
        $cashAgainst = Yii::$app->tinyShopService->baseCashAgainst->findOne($form->merchant_id);
        if ($form->address && $this->isNewRecord && $cashAgainst && !in_array($form->address->area_id, explode(',', $cashAgainst['area_ids']))) {
            throw new UnprocessableEntityHttpException('暂不支持该地区的货到付款');
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
        return 'cashAgainst';
    }
}