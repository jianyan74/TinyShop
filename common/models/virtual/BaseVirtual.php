<?php

namespace addons\TinyShop\common\models\virtual;

/**
 * Class BaseVirtual
 * @package addons\TinyShop\common\models\virtual
 * @author jianyan74 <751393839@qq.com>
 */
class BaseVirtual extends \yii\base\Model
{
    public $period = 0;
    public $confine_use_number = 1;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['period', 'confine_use_number'], 'integer', 'min' => 0],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'period' => '有效期/天',
            'confine_use_number' => '限制使用次数',
        ];
    }

    public function attributeHints()
    {
        return [
            'period' => '输入0表示不限制'
        ];
    }
}