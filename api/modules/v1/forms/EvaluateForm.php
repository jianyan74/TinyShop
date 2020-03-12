<?php

namespace addons\TinyShop\api\modules\v1\forms;

use Yii;
use common\helpers\AddonHelper;
use common\helpers\ArrayHelper;
use addons\TinyShop\common\models\product\Evaluate;
use addons\TinyShop\common\enums\ExplainStatusEnum;
use addons\TinyShop\common\enums\ExplainTypeEnum;

/**
 * Class EvaluateForm
 * @package addons\TinyShop\api\modules\v1\forms
 * @author jianyan74 <751393839@qq.com>
 */
class EvaluateForm extends Evaluate
{
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['order_product_id', 'scores'], 'required'],
            [['order_product_id'], 'verifyValid'],
        ]);
    }

    /**
     * @param $attribute
     */
    public function verifyValid($attribute)
    {
        $product = Yii::$app->tinyShopService->orderProduct->findById($this->order_product_id);
        if (!$product) {
            $this->addError($attribute, '找不到订单产品');

            return;
        }

        if ($product->is_evaluate != ExplainStatusEnum::DEAULT) {
            $this->addError($attribute, $product['product_name'] . '已经评价');

            return;
        }

        $merchant = Yii::$app->services->merchant->findById($this->merchant_id);
        $this->merchant_name = $merchant['title'] ?? '';
        $this->order_id = $product['order_id'];
        $this->order_sn = $product->order->order_sn;
        $this->product_id = $product['product_id'];
        $this->product_name = $product['product_name'];
        $this->product_picture = $product['product_picture'];
        $this->sku_name = $product['sku_name'];
        $this->product_price = $product['price'];
        $this->member_id = $product['buyer_id'];
        $this->member_nickname = $product->member->nickname;
        $this->member_head_portrait = $product->member->head_portrait;
        $this->explain_type = ExplainTypeEnum::scoresToType($this->scores);

        // 评价
        Yii::$app->tinyShopService->orderProduct->evaluate($product->id);
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        $this->has_cover = !empty($this->covers) ? 1 : 0;
        $this->has_video = !empty($this->video) ? 1 : 0;
        $this->has_content = !empty($this->content) ? 1 : 0;
        // 默认内容
        empty($this->content) && $this->content = '此用户没有填写评价。';

        return parent::beforeSave($insert);
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            // 增加评价数量
            Yii::$app->tinyShopService->product->commentNumChange($this->product_id, $this->scores, 1);
            // 校验是否全部评价并修改订单已评价
            Yii::$app->tinyShopService->order->evaluate($this->order_id);
            // 更新商品评价标签
            Yii::$app->tinyShopService->productEvaluateStat->updateNum(new EvaluateStatForm([
                'has_cover' => !empty($this->covers),
                'has_video' => !empty($this->video),
                'has_good' => $this->explain_type == ExplainTypeEnum::GOOD,
                'has_ordinary' => $this->explain_type == ExplainTypeEnum::ORDINARY,
                'has_negative' => $this->explain_type == ExplainTypeEnum::NEGATIVE,
            ]), $this->product_id);
        }

        parent::afterSave($insert, $changedAttributes);
    }
}