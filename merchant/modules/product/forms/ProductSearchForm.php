<?php

namespace addons\TinyShop\merchant\modules\product\forms;

use Yii;
use yii\base\Model;
use addons\TinyShop\common\enums\ProductShippingTypeEnum;

/**
 * Class ProductSearchForm
 * @package addons\TinyShop\merchant\modules\product\forms
 */
class ProductSearchForm extends Model
{
    public $name;
    public $cate_id;
    public $merchant_cate_id;
    public $type;
    public $min_sales;
    public $max_sales;
    public $recommend;
    public $marketing;
    public $supplier_id;
    public $stock_deduction_type;
    public $brand_id;
    public $merchant_id;

    /**
     * @return array|array[]
     */
    public function rules()
    {
        return [
            ['recommend', 'safe'],
            [['name', 'marketing'], 'string'],
            [['cate_id', 'merchant_cate_id', 'merchant_id', 'type', 'stock_deduction_type', 'brand_id', 'min_sales', 'max_sales', 'supplier_id'], 'integer'],
        ];
    }

    public function getMerchant()
    {
        return Yii::$app->services->merchant->findById($this->merchant_id);
    }

    /**
     * @return array
     */
    public function recommend()
    {
        $where = [];
        if (empty($this->recommend)) {
            return $where;
        }

        foreach ($this->recommend as $value) {
            if ($value == 1) {
                $where['is_hot'] = 1;
            }

            if ($value == 2) {
                $where['is_recommend'] = 1;
            }

            if ($value == 3) {
                $where['is_new'] = 1;
            }

            // 包邮
            if ($value == 4) {
                $where['shipping_type'] = ProductShippingTypeEnum::FULL_MAIL;
            }

            // 分销
            if ($value == 5) {
                $where['is_commission'] = 1;
            }
        }

        return $where;
    }

    /**
     * @return array
     */
    public function marketing()
    {
        if (empty($this->marketing)) {
            return [];
        }

        return ['in', 'id', Yii::$app->tinyShopService->marketingProduct->findIsMarketingByType($this->marketing)];
    }

    /**
     * @return array
     */
    public function betweenSales()
    {
        if (!empty($this->min_sales) && !empty($this->max_sales)) {
            return ['between', 'total_sales', $this->min_sales, $this->max_sales];
        }

        if (!empty($this->min_sales)) {
            return ['>=', 'total_sales', $this->min_sales];
        }

        if (!empty($this->max_sales)) {
            return ['<=', 'total_sales', $this->max_sales];
        }

        return [];
    }

    /**
     * 分类id
     *
     * @return array
     */
    public function cateIds()
    {
        if (empty($this->cate_id)) {
            return [];
        }

        $merchant_id = Yii::$app->services->merchant->getNotNullId();
        if (Yii::$app->services->devPattern->isB2B2C() || Yii::$app->services->devPattern->isB2C()) {
            $merchant_id = 0;
        }

        return Yii::$app->tinyShopService->productCate->findChildIdsById($this->cate_id, $merchant_id);
    }

    /**
     * 商家分类id
     *
     * @return array
     */
    public function merchantCateIds()
    {
        if (empty($this->merchant_cate_id)) {
            return [];
        }

        $merchant_id = Yii::$app->services->merchant->getNotNullId();

        return Yii::$app->tinyShopService->productCate->findChildIdsById($this->merchant_cate_id, $merchant_id);
    }
}
