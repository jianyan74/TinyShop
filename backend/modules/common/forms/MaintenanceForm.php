<?php

namespace addons\TinyShop\backend\modules\common\forms;

use yii\base\Model;

/**
 * Class MaintenanceForm
 * @package addons\TinyShop\backend\modules\common\forms
 * @author jianyan74 <751393839@qq.com>
 */
class MaintenanceForm extends Model
{
    public $is_open_site = 1;
    public $close_site_date;
    public $close_site_explain;

    public function rules()
    {
        return [
            [['close_site_date', 'close_site_explain'], 'required'],
            ['is_open_site', 'integer'],
            [
                ['close_site_date', 'close_site_explain'],
                'string',
                'max' => 200,
            ],
        ];
    }

    /**
     * @return array|string[]
     */
    public function attributeLabels()
    {
        return [
            'is_open_site' => '开启站点',
            'close_site_date' => '关闭站点时间',
            'close_site_explain' => '关闭站点说明',
        ];
    }
}