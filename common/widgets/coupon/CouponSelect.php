<?php

namespace addons\TinyShop\common\widgets\coupon;

use Yii;
use yii\helpers\Html;
use common\helpers\ArrayHelper;
use common\helpers\StringHelper;
use unclead\multipleinput\MultipleInputColumn;

/**
 * Class CouponSelect
 * @package addons\TinyShop\common\widgets\coupon
 * @author jianyan74 <751393839@qq.com>
 */
class CouponSelect extends \yii\widgets\InputWidget
{
    /**
     * 唯一ID
     *
     * @var string
     */
    public $box_id = 'couponSelect';

    /**
     * 判断可选的上限
     *
     * 0 为不限制
     *
     * @var int
     */
    public $max = 0;

    /**
     * 判断可选的最低
     *
     * 0 为不限制
     */
    public $min = 0;

    /**
     * @var array
     */
    public $columns = [];

    /**
     * 默认字段值
     *
     * @var array
     */
    protected $column = [
        'label' => '数量',
        'name' => 'number',
        'value' => '',
        'type' => MultipleInputColumn::TYPE_TEXT_INPUT,
        'options' => [
            'style' => ''
        ],
        // 字段规则验证
        'rule' => [
            'comparisonFieldMin' => '', // 比较字段(不能低于) 多个字段支持 , 分割
            'comparisonFieldMax' => '', // 比较字段(不能超出) 多个字段支持 , 分割
            'min' => 0, // 最小值
            'max' => '', // 最大值
        ],
    ];

    /**
     * 多选开启
     *
     * @var bool
     */
    public $multiple = true;

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

        return $this->render('coupon', [
            'couponTypes' => $value,
            'columns' => $this->columns,
            'name' => $name,
            'min' => $this->min,
            'max' => $this->max,
            'multiple' => $this->multiple,
            'box_id' => !empty($this->box_id) ? $this->box_id : StringHelper::uuid('uniqid'),
        ]);
    }
}
