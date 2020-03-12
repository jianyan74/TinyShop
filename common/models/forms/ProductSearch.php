<?php

namespace addons\TinyShop\common\models\forms;

use common\enums\SortEnum;
use yii\base\Model;

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

    /*-- 排序 --*/

    public $collect;
    public $view;
    public $sales;
    public $price;

    public $page_size = 10;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['is_hot', 'is_recommend', 'is_new', 'brand_id', 'cate_id', 'page_size'], 'integer'],
            [['collect', 'view', 'sales', 'price', 'keyword'], 'string'],
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
        $this->sales == SortEnum::ASC && $order[] = 'sales ' . SortEnum::ASC;
        $this->sales == SortEnum::DESC && $order[] = 'sales ' . SortEnum::DESC;
        $this->price == SortEnum::ASC && $order[] = 'price ' . SortEnum::ASC;
        $this->price == SortEnum::DESC && $order[] = 'price ' . SortEnum::DESC;

        return $order;
    }
}