<?php

namespace addons\TinyShop\common\models\virtual;

use common\helpers\ArrayHelper;

/**
 * Class Virtual
 * @package addons\TinyShop\common\models\virtual
 * @author jianyan74 <751393839@qq.com>
 */
class Virtual extends BaseVirtual
{
    public $text_use_time;
    public $text_rule;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['text_use_time', 'text_rule'], 'string'],
            [['text_rule', 'text_use_time'], 'required'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'text_use_time' => '可用时间说明',
            'text_rule' => '使用规则',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeHints()
    {
        return ArrayHelper::merge(parent::attributeHints(), [
            'text_use_time' => '例如: 周六、日、法定节假日不可用',
            'text_rule' => '例如: 无需预约，高峰时段可能需要等位',
        ]);
    }
}