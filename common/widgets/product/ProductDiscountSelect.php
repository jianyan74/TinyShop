<?php

namespace addons\TinyShop\common\widgets\product;

use Yii;
use yii\helpers\Html;
use common\helpers\StringHelper;
use common\helpers\ArrayHelper;

/**
 * Class ProductDiscountSelect
 * @package addons\TinyShop\common\widgets\product
 * @author jianyan74 <751393839@qq.com>
 */
class ProductDiscountSelect extends \yii\widgets\InputWidget
{
    /**
     * 指定商品
     *
     * [
     *      [
     *          'product_id' => 1,
     *          'type' => 1, // 类型 1:满减;2:折扣
     *          'discount' => 99,
     *      ]
     * ]
     *
     * @var array
     */
    public $value = [];

    /**
     * @return string
     * @throws \Exception
     */
    public function run()
    {
        $value = $this->hasModel() ? Html::getAttributeValue($this->model, $this->attribute) : $this->value;
        $name = $this->hasModel() ? Html::getInputName($this->model, $this->attribute) : $this->name;
        empty($value) && $value = [];

        $products = Yii::$app->tinyShopService->product->findByIds(ArrayHelper::getColumn($value, 'product_id'));
        $products = ArrayHelper::toArray($products);

        $value = ArrayHelper::arrayKey($value, 'product_id');
        foreach ($products as &$product) {
            $product['type'] = $value[$product['id']]['type'] ?? 2;
            $product['discount'] = $value[$product['id']]['discount'] ?? 99;
        }

        return $this->render('product-discount', [
            'products' => $products,
            'name' => $name,
            'boxId' => StringHelper::uuid('uniqid'),
        ]);
    }
}