<?php

namespace addons\TinyShop\merchant\modules\common\forms;

use yii\base\Model;

/**
 * Class MaintenanceForm
 * @package addons\TinyShop\merchant\modules\common\forms
 * @author jianyan74 <751393839@qq.com>
 */
class MaintenanceForm extends Model
{
    public $site_status = 1;
    public $site_close_explain;

    public function rules()
    {
        return [
            ['site_status', 'integer'],
            [
                ['site_close_explain'],
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
            'site_status' => '站点状态',
            'site_close_explain' => '站点维护说明',
        ];
    }
}
