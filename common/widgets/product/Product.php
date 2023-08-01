<?php

namespace addons\TinyShop\common\widgets\product;

use Yii;
use common\helpers\BcHelper;
use common\helpers\Html;
use common\enums\StatusEnum;
use common\helpers\StringHelper;
use common\helpers\ArrayHelper;
use unclead\multipleinput\MultipleInputColumn;
use addons\TinyShop\common\enums\DiscountTypeEnum;
use addons\TinyShop\common\models\marketing\MarketingProduct;

/**
 * Class Product
 * @package addons\TinyShop\common\widgets\product
 * @author jianyan74 <751393839@qq.com>
 */
class Product extends \yii\widgets\InputWidget
{
    /**
     * 唯一ID
     *
     * @var string
     */
    public $box_id = '';

    /**
     * 判断可选的商品上限
     *
     * 0 为不限制
     *
     * @var int
     */
    public $max = 5000;

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
     * 设置 skus
     *
     * @var bool
     */
    public $setting_sku = true;

    /**
     * @var array|MarketingProduct
     */
    public $value = [];

    /**
     * 虚拟商品可选开启
     *
     * @var bool
     */
    public $is_virtual = false;

    /**
     * 字段列表
     *
     * @var array
     */
    public $columns = [];

    /**
     * @var string
     */
    public $url = '';

    /**
     * 计算
     *
     * @var string
     */
    public $calculation = [
        // 计算规格 price * number 的 sku 累加
        'moneyField' => 'price',
        // 计算总数量字段
        'numberField' => 'number',
    ];

    /**
     * 字段差异对比
     *
     * @var string[]
     */
    public $difference = [
        'from' => '',
        'to' => '',
        'relevancy' => '',
    ];

    /**
     * 默认字段值
     *
     * @var array
     */
    protected $column = [
        'label' => '折扣(1-100)%',
        'name' => 'discount',
        'value' => '',
        'valueFieldMap' => '', // 映射默认SKU值, 如果是SKU里面存在的默认用SKU的
        'type' => MultipleInputColumn::TYPE_TEXT_INPUT,
        'items' => [],
        // 属性
        'options' => [
            'sku' => true, // 规格内也显示列表
            'placeholder' => '', // 输入框默认值
            'calculation' => false, // 参与营销计算
            'discountType' => DiscountTypeEnum::FIXATION, // 营销类型
            'style' => '', // 样式
        ],
        // 字段规则验证
        'rule' => [
            'comparisonFieldMin' => '', // 比较字段(不能低于) 多个字段支持 , 分割
            'comparisonFieldMax' => '', // 比较字段(不能超出) 多个字段支持 , 分割
            'min' => 0, // 最小值
            'max' => '', // 最大值
        ],
        // 触发清零字段
        'emptyField' => [],
    ];

    /**
     * @var string
     */
    public $theme = 'product';

    /**
     * @return string
     * @throws \Exception
     */
    public function run()
    {
        $value = $this->hasModel() ? Html::getAttributeValue($this->model, $this->attribute) : $this->value;
        $name = $this->hasModel() ? Html::getInputName($this->model, $this->attribute) : $this->name;
        empty($value) && $value = [];

        foreach ($this->columns as &$column) {
            $column = ArrayHelper::merge($this->column, $column);
        }

        foreach ($value as &$product) {
            $product['name_segmentation'] = $product['name'];
            foreach ($this->columns as &$column) {
                // 自定义值
                if (isset($product['marketing_data'][$column['name']])) {
                    $product[$column['name']] = $product['marketing_data'][$column['name']];
                } else {
                    if ($column['options']['sku'] == true) {
                        $product[$column['name']] = '';
                    }
                }
            }

            unset($product['marketing_data'], $product['delivery_type']);
            empty($product['sku']) && $product['sku'] = [];
            // 只有一个规格, 重新处理库存
            if (count($product['sku']) == 1) {
                $product['marketing_stock'] = $product['marketing_total_stock'] ?? 0;
            }

            // 处理 sku
            foreach ($product['sku'] as &$sku) {
                foreach ($this->columns as &$column) {
                    if (!isset($sku[$column['name']])) {
                        // 自定义值
                        if (isset($sku['marketing_data'][$column['name']])) {
                            $sku[$column['name']] = $sku['marketing_data'][$column['name']];
                        }
                    }

                    // 自动计算价格
                    if ($column['options']['calculation'] == true && isset($sku['discount_type'])) {
                        switch ($sku['discount_type']) {
                            case DiscountTypeEnum::MONEY :
                                $sku['tmp_money'] = BcHelper::sub($sku['price'], $sku['discount']);
                                break;
                            case DiscountTypeEnum::DISCOUNT :
                                $sku['tmp_money'] = BcHelper::mul($sku['price'], $sku['discount'] / 10);
                                break;
                            case DiscountTypeEnum::FIXATION :
                                $sku['tmp_money'] = $sku['discount'];
                                break;
                        }

                        $sku['tmp_money'] = floatval($sku['tmp_money']);
                        $sku['tmp_discount_type'] = $sku['discount_type'];
                    }
                }

                unset($sku['marketing_data']);
            }
        }

        return $this->render('discount', [
            'products' => $value,
            'name' => $name,
            'calculation' => $this->calculation,
            'difference' => $this->difference,
            'columns' => $this->columns,
            'min' => $this->min,
            'max' => $this->max,
            'multiple' => $this->multiple,
            'is_virtual' => $this->is_virtual,
            'url' => $this->url,
            'setting_sku' => !empty($this->setting_sku) ? StatusEnum::ENABLED : StatusEnum::DISABLED,
            'box_id' => !empty($this->box_id) ? $this->box_id : StringHelper::uuid('uniqid'),
        ]);
    }
}
