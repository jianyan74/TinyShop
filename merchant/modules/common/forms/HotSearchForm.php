<?php

namespace addons\TinyShop\merchant\modules\common\forms;

use yii\base\Model;

/**
 * Class HotSearchForm
 * @package addons\TinyShop\merchant\modules\common\forms
 * @author jianyan74 <751393839@qq.com>
 */
class HotSearchForm extends Model
{
    public $hot_search_default;
    public $hot_search_list = [];

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['hot_search_default'], 'string', 'max' => 100],
            [['hot_search_list'], 'safe'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'hot_search_default' => '默认搜索',
            'hot_search_list' => '热门搜索',
        ];
    }

    /**
     * @return array
     */
    public function attributeHints()
    {
        return [
            'hot_search_list' => '输入后请回车, 将显示在前台搜索框下面，前台点击时直接作为关键词进行搜索',
            'hot_search_default' => '将显示在前台搜索框，前台点击时直接作为关键词进行搜索',
        ];
    }
}
