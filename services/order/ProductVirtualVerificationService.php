<?php

namespace addons\TinyShop\services\order;

use Yii;
use yii\web\UnprocessableEntityHttpException;
use common\components\Service;
use common\models\merchant\Member;
use addons\TinyShop\common\models\order\ProductVirtual;
use addons\TinyShop\common\enums\ProductVirtualStateEnum;
use addons\TinyShop\common\models\order\ProductVirtualVerification;

/**
 * 核销卡卷
 *
 * Class ProductVirtualVerificationService
 * @package addons\TinyShop\services\order
 * @author jianyan74 <751393839@qq.com>
 */
class ProductVirtualVerificationService extends Service
{
    /**
     * @param $code
     * @param $merchant_id
     * @return array|\yii\db\ActiveRecord|null
     * @throws UnprocessableEntityHttpException
     */
    public function verify($code, $merchant_id)
    {
        /** @var $model ProductVirtual */
        if (!($model = Yii::$app->tinyShopService->orderProductVirtual->findByCodeAndMerchantId($code, $merchant_id))) {
            throw new UnprocessableEntityHttpException('核销码无效');
        }

        if ($model->state == ProductVirtualStateEnum::LOSE) {
            throw new UnprocessableEntityHttpException('核销码已过期');
        }

        if ($model->state == ProductVirtualStateEnum::USE) {
            throw new UnprocessableEntityHttpException('核销码已使用');
        }

        if ($model->end_time > 0 &&  $model->end_time < time()) {
            throw new UnprocessableEntityHttpException('核销码已过期');
        }

        $model->use_number += 1;
        // 数量等于最大使用次数,状态变为已使用
        if ($model->use_number == $model->confine_use_number) {
            $model->state = ProductVirtualStateEnum::USE;
        }

        $model->save();

        // 记录日志
        $this->log($model);

        return $model;
    }

    /**
     * @param ProductVirtual $productVirtual
     */
    public function log(ProductVirtual $productVirtual)
    {
        /** @var Member $auditor */
        $auditor = Yii::$app->services->merchantMember->findById(Yii::$app->user->identity->member_id);

        $model = new ProductVirtualVerification();
        $model = $model->loadDefaultValues();
        $model->member_id = $productVirtual->member_id;
        $model->merchant_id = $productVirtual->merchant_id;
        $model->merchant_name = $productVirtual->merchant->title;
        $model->product_virtual_id = $productVirtual->id;
        $model->product_virtual_state = $productVirtual->state;
        $model->num = $productVirtual->use_number;
        $model->product_id = $productVirtual->product_id;
        $model->product_name = $productVirtual->product_name;
        $model->auditor_id = $auditor->id;
        $model->auditor_name = $auditor->realname;
        $model->save();
    }
}