<?php

namespace addons\TinyShop\common\forms;

use Yii;
use yii\base\Model;
use common\enums\SortEnum;
use common\helpers\ArrayHelper;
use common\enums\StatusEnum;
use addons\TinyShop\common\enums\MarketingEnum;

/**
 * Class ProductSearch
 * @package addons\TinyShop\common\forms
 */
class ProductSearchForm extends Model
{
    /*-- 查询 --*/
    public $keyword;
    public $is_hot;
    public $is_recommend;
    public $is_commission;
    public $is_member_discount;
    public $is_new;
    public $cate_id;
    public $brand_id;
    public $coupon_type_id;
    public $ids = [];
    public $current_level;
    public $member_id;
    public $merchant_id;

    /*-- 排序 --*/
    public $collect;
    public $view;
    public $total_sales;
    public $price;
    public $created_at;

    public $limit = 12;

    /*-- 区间 --*/
    public $min_price;
    public $max_price;

    /*-- 集合 --*/
    public $gather; // excellent 精选

    /*-- 关联 --*/
    public $with = ['marketing', 'sku'];

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['min_price', 'max_price'], 'number', 'min' => 0],
            [['is_hot', 'is_recommend', 'is_new', 'is_member_discount', 'is_commission', 'brand_id', 'cate_id', 'limit', 'coupon_type_id'], 'integer'],
            [['collect', 'view', 'total_sales', 'price', 'keyword', 'gather'], 'string'],
        ];
    }

    /**
     * @return false|string[]
     */
    public function getBrandIds()
    {
        if (empty($this->brand_id)) {
            return [];
        }

        $arr =  explode(',', $this->brand_id);
        foreach ($arr as &$item) {
            $item = (int) $item;
        }

        return $arr;
    }

    /**
     * @return array
     */
    public function getIds()
    {
        if (!empty($this->coupon_type_id)) {
            $products = Yii::$app->tinyShopService->marketingProduct->findByMarketing($this->coupon_type_id, MarketingEnum::COUPON, 0, []);
            $ids = ArrayHelper::getColumn($products, 'product_id');
            !empty($ids) && $this->ids = ArrayHelper::merge($this->ids, $ids);
        }

        if (empty($this->ids)) {
            return [];
        }

        return $this->ids;
    }

    /**
     * @return false|string[]
     */
    public function getCateIds()
    {
        if (empty($this->cate_id)) {
            return [];
        }

        $cate = Yii::$app->tinyShopService->productCate->findAll();

        $arr = explode(',', $this->cate_id);
        $cate_ids = $arr;
        foreach ($arr as $item) {
            $cate_ids = ArrayHelper::merge(ArrayHelper::getChildIds($cate, $item), $cate_ids);
        }

        $where = ['in', 'cate_id', $cate_ids];
        $ids = Yii::$app->tinyShopService->productCateMap->findByCateIds($cate_ids);
        !empty($ids) && $where = ['in', 'id', $ids];

        return $where;
    }

    /**
     * 或查询
     */
    public function getOrCondition()
    {
        $or = ['or'];
        !empty($this->is_hot) && $or[] =  ['is_hot' => StatusEnum::ENABLED];
        !empty($this->is_recommend) && $or[] =  ['is_recommend' => StatusEnum::ENABLED];
        !empty($this->is_new) && $or[] =  ['is_new' => StatusEnum::ENABLED];
        if ($or == ['or']) {
            return [];
        }

        return $or;
    }

    /**
     * @return array
     */
    public function getOrderBy()
    {
        // 排序
        $order = [];

        $this->collect == SortEnum::ASC && $order[] = 'collect_num ' . SortEnum::ASC;
        $this->collect == SortEnum::DESC && $order[] = 'collect_num ' . SortEnum::DESC;
        $this->view == SortEnum::ASC && $order[] = 'view ' . SortEnum::ASC;
        $this->view == SortEnum::DESC && $order[] = 'view ' . SortEnum::DESC;
        $this->total_sales == SortEnum::ASC && $order[] = 'total_sales ' . SortEnum::ASC;
        $this->total_sales == SortEnum::DESC && $order[] = 'total_sales ' . SortEnum::DESC;
        $this->price == SortEnum::ASC && $order[] = 'price ' . SortEnum::ASC;
        $this->price == SortEnum::DESC && $order[] = 'price ' . SortEnum::DESC;
        $this->created_at == SortEnum::ASC && $order[] = 'created_at ' . SortEnum::ASC;
        $this->created_at == SortEnum::DESC && $order[] = 'created_at ' . SortEnum::DESC;

        return $order;
    }

    /**
     * 精选
     *
     * @return false|string[]
     */
    public function getGather()
    {
        if (empty($this->gather)) {
            return [];
        }

        $condition = [];
        switch ($this->gather) {
            // 精选
            case 'excellent' :
                $condition = [
                    'and',
                    ['>', 'comment_num', 0],
                    ['>', 'match_ratio', 99],
                ];
                $this->with[] = 'evaluateStat';
                $this->with[] = 'firstEvaluate';
                break;
        }

        return $condition;
    }

    public function getWith()
    {
        $with = $this->with;
        if ($this->is_commission) {
            $with = ArrayHelper::merge($with, ['commissionRate']);
        }

        if (Yii::$app->services->devPattern->isB2B2C()) {
            $with = ArrayHelper::merge($with, ['baseMerchant']);
        }

        return $with;
    }
}
