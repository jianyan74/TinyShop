<?php

namespace addons\TinyShop\common\models\forms;

use Yii;
use yii\base\Model;
use common\enums\SortEnum;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;

/**
 * 产品快速搜索表单
 *
 * Class ProductSearch
 * @package addons\TinyShop\common\models\forms
 * @author jianyan74 <751393839@qq.com>
 */
class ProductSearch extends Model
{
    /*-- 查询 --*/

    public $keyword;
    public $is_hot;
    public $is_recommend;
    public $is_new;
    public $cate_id;
    public $brand_id;
    public $is_open_presell;
    public $is_integral;

    /*-- 排序 --*/

    public $collect;
    public $view;
    public $total_sales;
    public $price;

    public $page_size = 12;

    /*-- 区间 --*/
    public $min_price;
    public $max_price;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['min_price', 'max_price'], 'number', 'min' => 0],
            [['is_hot', 'is_recommend', 'is_new', 'brand_id', 'cate_id', 'page_size', 'is_open_presell', 'is_integral'], 'integer'],
            [['collect', 'view', 'total_sales', 'price', 'keyword'], 'string'],
        ];
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

        return $order;
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

        return $cate_ids;
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
}