<?php

namespace addons\TinyShop\merchant\modules\order\forms;

use Yii;
use common\helpers\ArrayHelper;
use addons\TinyShop\common\models\order\Order;
use addons\TinyShop\common\models\order\ProductExpress;
use addons\TinyShop\common\enums\ProductExpressShippingTypeEnum;

/**
 * Class ProductExpressForm
 * @package addons\TinyShop\merchant\modules\order\forms
 * @author jianyan74 <751393839@qq.com>
 */
class ProductExpressForm extends ProductExpress
{
    /**
     * @var Order
     */
    public $order;

    /**
     * 批量发货
     *
     * @var bool
     */
    public $is_batch = false;

    /**
     * @return array
     */
    public function rules()
    {
        $rule = parent::rules();

        return ArrayHelper::merge($rule, [
            ['shipping_type', 'verifyCompany'],
        ]);
    }

    /**
     * @param $attribute
     */
    public function verifyCompany($attribute)
    {
        if ($this->is_batch == false && $this->shipping_type == ProductExpressShippingTypeEnum::LOGISTICS) {
            if (!$this->express_company_id) {
                $this->addError($attribute, '请选择快递公司');
                return false;
            }

            if (!$this->express_no) {
                $this->addError($attribute, '请填写快递单号');
                return false;
            }

            if (!($company = Yii::$app->tinyShopService->expressCompany->findById($this->express_company_id))) {
                $this->addError($attribute, '找不到快递公司信息');
                return false;
            } else {
                $this->express_company = $company['title'];
            }
        }

        if (!$this->order_product_ids) {
            $this->addError($attribute, '请勾选商品信息');
            return false;
        }

        if (Yii::$app->tinyShopService->orderProduct->isNormal($this->order_product_ids, $this->order->id) == false) {
            $this->addError($attribute, '有商品已发货或有退款在处理，请刷新重试');
            return false;
        }
    }

    /**
     * @param bool $insert
     * @return bool
     * @throws \yii\web\NotFoundHttpException
     * @throws \yii\web\UnprocessableEntityHttpException
     */
    public function beforeSave($insert)
    {
        $this->order_id = $this->order->id;

        // 发货状态
        if ($this->isNewRecord) {
            Yii::$app->tinyShopService->orderProduct->consign($this->order_product_ids, $this->order->id);
        }

        return parent::beforeSave($insert);
    }
}
