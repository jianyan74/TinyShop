<?php

namespace addons\TinyShop\merchant\forms;

use Yii;
use addons\TinyShop\common\models\order\Order;
use addons\TinyShop\common\models\order\ProductExpress;
use common\helpers\ArrayHelper;

/**
 * Class DeliverProductForm
 * @package addons\TinyShop\merchant\forms
 * @author jianyan74 <751393839@qq.com>
 */
class DeliverProductForm extends ProductExpress
{
    /**
     * @var Order
     */
    public $order;

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
        if ($this->shipping_type == ProductExpress::SHIPPING_TYPE_LOGISTICS) {
            if (!$this->express_company_id) {
                return $this->addError($attribute, '请选择快递公司');
            }

            if (!$this->express_no) {
                return $this->addError($attribute, '请填写快递单号');
            }

            if (!($company = Yii::$app->tinyShopService->expressCompany->findById($this->express_company_id))) {
                return $this->addError($attribute, '找不到快递公司信息');
            } else {
                $this->express_name = $company['title'];
                $this->express_company = $company['title'];
            }
        }

        if (!$this->order_product_ids) {
            return $this->addError($attribute, '请勾选商品信息');
        }

        if (Yii::$app->tinyShopService->orderProduct->isNormal($this->order_product_ids, $this->order->id) == false) {
            return $this->addError($attribute, '有商品已发货或有退款在处理，请刷新重试');
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
            Yii::$app->tinyShopService->orderProduct->deliver($this->order_product_ids, $this->order->id);
        }

        return parent::beforeSave($insert);
    }
}