<?php

namespace addons\TinyShop\merchant\forms;

use Yii;
use common\helpers\ArrayHelper;
use addons\TinyShop\common\models\marketing\CouponType;
use addons\TinyShop\common\enums\RangeTypeEnum;

/**
 * Class CouponTypeForm
 * @package addons\TinyShop\merchant\forms
 * @author jianyan74 <751393839@qq.com>
 */
class CouponTypeForm extends CouponType
{
    public $defaultCount = 0;
    public $reissuenNum = 0;
    public $product_ids = [];

    public function rules()
    {
        $rule = parent::rules();
        $rule[] = [['defaultCount', 'reissuenNum'], 'integer', 'min' => 0];
        $rule[] = [['product_ids'], 'safe'];

        return $rule;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'defaultCount' => '已发放数量',
            'reissuenNum' => '补发数量',
            'product_ids' => '指定商品',
        ]);
    }

    public function afterFind()
    {
        $this->product_ids = Yii::$app->tinyShopService->marketingCouponProduct->getProductIds($this->id);

        parent::afterFind();
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->reissuenNum = $this->count;
        }

        if (!$this->isNewRecord && $this->reissuenNum > 0) {
            $this->count += $this->reissuenNum;
        }

        return parent::beforeSave($insert);
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     * @throws \yii\db\Exception
     */
    public function afterSave($insert, $changedAttributes)
    {
        // 创建具体优惠券
        $this->reissuenNum > 0 && Yii::$app->tinyShopService->marketingCoupon->create($this, $this->reissuenNum);
        if ($this->range_type == RangeTypeEnum::ALL) {
            Yii::$app->tinyShopService->marketingCouponProduct->create($this->id, []);
        } else {
            Yii::$app->tinyShopService->marketingCouponProduct->create($this->id, $this->product_ids);
        }

        parent::afterSave($insert, $changedAttributes);
    }
}