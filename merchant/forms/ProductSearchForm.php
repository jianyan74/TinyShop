<?php

namespace addons\TinyShop\merchant\forms;

use addons\TinyShop\common\enums\ProductMarketingEnum;
use Yii;
use addons\TinyShop\common\enums\PointExchangeTypeEnum;
use addons\TinyShop\common\enums\ProductShippingTypeEnum;
use yii\base\Model;

/**
 * Class ProductSearchForm
 * @package addons\TinyShop\merchant\forms
 * @author jianyan74 <751393839@qq.com>
 */
class ProductSearchForm extends Model
{
    public $name;
    public $cate_id;
    public $is_virtual;
    public $min_sales;
    public $max_sales;
    public $recommend;
    public $marketing;
    public $supplier_id;

    /**
     * @return array|array[]
     */
    public function rules()
    {
        return [
            ['recommend', 'safe'],
            [['name', 'marketing'], 'string'],
            [['cate_id', 'is_virtual', 'min_sales', 'max_sales', 'supplier_id'], 'integer'],
        ];
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
                $where['is_open_commission'] = 1;
            }

            // 预售
            if ($value == 6) {
                $where['is_open_presell'] = 1;
            }
        }

        return $where;
    }

    /**
     * @return array
     */
    public function integral()
    {
        $where = [];
        if (empty($this->recommend)) {
            return $where;
        }

        foreach ($this->recommend as $value) {
            // 积分兑换
            if ($value == 7) {
                $where['point_exchange_type'] = PointExchangeTypeEnum::isIntegral();
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
        return Yii::$app->tinyShopService->productCate->findChildIdsById($this->cate_id);
    }
}