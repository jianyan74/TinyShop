<?php

namespace addons\TinyShop\common\components\marketing;

use Yii;
use yii\web\UnprocessableEntityHttpException;
use common\helpers\ArrayHelper;
use common\enums\StatusEnum;
use common\helpers\BcHelper;
use addons\TinyShop\common\models\order\OrderProduct;
use addons\TinyShop\common\forms\PreviewForm;
use addons\TinyShop\common\components\PreviewInterface;
use addons\TinyShop\common\enums\MarketingEnum;

/**
 * 统一处理数据
 *
 * Class AfterHandler
 * @package addons\TinyShop\common\components\marketing
 * @author jianyan74 <751393839@qq.com>
 */
class AfterHandler extends PreviewInterface
{
    /**
     * @param PreviewForm $form
     * @return PreviewForm|mixed
     */
    public function execute(PreviewForm $form): PreviewForm
    {
        foreach ($form->marketingDetails as &$detail) {
            empty($detail['marketing_name']) && $detail['marketing_name'] = MarketingEnum::getValue($detail['marketing_type']);
        }

        // 下单金额判断
        if (
            $this->isNewRecord == true &&
            $form->marketing_type != MarketingEnum::BARGAIN &&
            $form->product_money < $form->config['order_min_pay_money']
        ) {
            throw new UnprocessableEntityHttpException('最低下单金额为' . $form->order_buy_min_pay_money . '元');
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
        return 'after';
    }
}
