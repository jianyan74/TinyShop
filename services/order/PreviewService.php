<?php

namespace addons\TinyShop\services\order;

use Yii;
use common\models\api\AccessToken;
use common\helpers\ArrayHelper;
use common\enums\StatusEnum;
use addons\TinyShop\common\forms\PreviewForm;
use addons\TinyShop\common\forms\SettingForm;

/**
 * Class PreviewService
 * @package addons\TinyShop\services\order
 */
class PreviewService
{
    /**
     * 初始化预览
     *
     * @param $merchant_id
     * @return PreviewForm
     */
    public function initModel($merchant_id)
    {
        /** @var AccessToken $identity */
        $identity = Yii::$app->user->identity;
        /** @var SettingForm $setting */
        $setting = Yii::$app->tinyShopService->config->setting();

        $orderInvoiceContent = explode(',', $setting->order_invoice_content);

        $model = new PreviewForm();
        $model = $model->loadDefaultValues();
        $model->merchant_id = $merchant_id;
        $model->buyer_id = $identity->member_id ?? 0;
        $model->member = $identity;
        $model->config = [
            // 物流配送
            'logistics' => $setting->logistics, // 物流配送
            'logistics_select' => $setting->logistics_select, // 选择物流
            'logistics_list' => $setting->logistics_select == StatusEnum::ENABLED ? Yii::$app->tinyShopService->expressCompany->getList($merchant_id, ['id', 'title', 'merchant_id']) : [], // 物流列表
            // 同城配送
            'logistics_local_distribution' => $setting->logistics_local_distribution,
            'logistics_local_distribution_config' => ArrayHelper::distributionTime(Yii::$app->tinyShopService->localConfig->findByMerchantId($merchant_id)), // 买家自提配置
            // 门店自提
            'logistics_pick_up' => $setting->logistics_pick_up,
            'logistics_pick_up_config' => Yii::$app->has('tinyStoreService') ? ArrayHelper::distributionTime(Yii::$app->tinyStoreService->config->findOne($merchant_id), false) : [],
            'logistics_pick_up_list' => Yii::$app->has('tinyStoreService') ? Yii::$app->tinyStoreService->store->findValidList($merchant_id, ['id', 'title', 'cover', 'mobile', 'address_name', 'address_details', 'merchant_id']) : [], // 买家自提门店
            'order_min_pay_money' => $setting->order_min_pay_money, // 最低下单金额
            'order_buy_close_time' => $setting->order_buy_close_time, // 订单关闭时间
            'order_back_points' => 0, // 赠送积分时间
            'order_back_growth' => 0, // 赠送成长值时间
            'product_give_point' => 0,
            'product_point_give_type' => 0,
            'product_give_growth' => 0,
            'product_growth_give_type' => 0,
            'order_invoice_content' => $orderInvoiceContent, // 开票内容
        ];

        return $model;
    }
}
