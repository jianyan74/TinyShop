<?php

namespace addons\TinyShop\common\widgets\product;

use Yii;
use yii\helpers\Html;
use common\helpers\StringHelper;

/**
 * Class ProductSelect
 * @package addons\TinyShop\common\widgets\product
 * @author jianyan74 <751393839@qq.com>
 */
class ProductSelect extends \yii\widgets\InputWidget
{
    /**
     * 判断可选的商品上限
     *
     * 0 为不限制
     *
     * @var int
     */
    public $max = 0;

    /**
     * 判断可选的商品最低
     *
     * 0 为不限制
     */
    public $min = 0;

    /**
     * 多选开启
     *
     * @var bool
     */
    public $multiple = true;

    /**
     * 虚拟商品可选开启
     *
     * @var bool
     */
    public $is_virtual = true;

    /**
     * @return string
     * @throws \Exception
     */
    public function run()
    {
        $value = $this->hasModel() ? Html::getAttributeValue($this->model, $this->attribute) : $this->value;
        $name = $this->hasModel() ? Html::getInputName($this->model, $this->attribute) : $this->name;
        empty($value) && $value = [];

        $products = Yii::$app->tinyShopService->product->findByIds($value);

        return $this->render('product', [
            'products' => $products,
            'name' => $name,
            'min' => $this->min,
            'max' => $this->max,
            'multiple' => $this->multiple,
            'is_virtual' => $this->is_virtual,
            'boxId' => StringHelper::uuid('uniqid'),
        ]);
    }
}