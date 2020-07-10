<?php

namespace addons\TinyShop\merchant\forms;

use yii\base\Model;

/**
 * Class ProductInfoForm
 * @package addons\TinyShop\merchant\forms
 * @author jianyan74 <751393839@qq.com>
 */
class ProductInfoForm extends Model
{
    public $price;
    public $price_type;
    public $market_price;
    public $market_price_type;
    public $cost_price;
    public $cost_price_type;
    public $stock;
    public $stock_type;

    /**
     * @return array|array[]
     */
    public function rules()
    {
        return [
            [['price', 'market_price', 'cost_price'], 'number', 'min' => 0],
            [['price_type', 'market_price_type', 'cost_price_type', 'stock_type', 'stock'], 'integer', 'min' => 0],
        ];
    }

    /**
     * @return array|string[]
     */
    public function attributeLabels()
    {
        return [
            'price' => '销售价',
            'market_price' => '市场价',
            'cost_price' => '成本价',
            'price_type' => '变动类型',
            'market_price_type' => '变动类型',
            'cost_price_type' => '变动类型',
            'stock_type' => '变动类型',
            'stock' => '库存数量'
        ];
    }

    /**
     * @return array
     */
    public function getFields()
    {
        $fields = [];
        $where = ['and'];
        if (!empty($this->price)) {
            $fields['price'] = $this->getNum($this->price_type, $this->price);
            $fields['price'] < 0 && $where[] = ['>=', 'price', $this->price];
        }

        if (!empty($this->market_price)) {
            $fields['market_price'] = $this->getNum($this->market_price_type, $this->market_price);
            $fields['market_price'] < 0 && $where[] = ['>=', 'market_price', $this->market_price];
        }

        if (!empty($this->cost_price)) {
            $fields['cost_price'] = $this->getNum($this->cost_price_type, $this->cost_price);
            $fields['cost_price'] < 0 && $where[] = ['>=', 'cost_price', $this->cost_price];
        }

        if (!empty($this->stock)) {
            $fields['stock'] = $this->getNum($this->stock_type, $this->stock);
            $fields['stock'] < 0 && $where[] = ['>=', 'stock', $this->stock];
        }

        return [$fields, $where];
    }

    /**
     * @param $type
     * @param $num
     * @return int
     */
    protected function getNum($type, $num)
    {
        return $type == 1 ? $num : - $num;
    }
}