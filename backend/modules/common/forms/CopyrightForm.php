<?php

namespace addons\TinyShop\backend\modules\common\forms;

use yii\base\Model;

/**
 * Class CopyrightForm
 * @package addons\TinyShop\backend\modules\common\forms
 * @author jianyan74 <751393839@qq.com>
 */
class CopyrightForm extends Model
{
    public $copyright_logo;
    public $copyright_companyname;
    public $copyright_url;
    public $copyright_desc;

    public function rules()
    {
        return [
            [['copyright_logo', 'copyright_companyname'], 'string', 'max' => 200,],
            [['copyright_url'], 'url'],
            [['copyright_desc'], 'string', 'max' => 500],
        ];
    }

    /**
     * @return array|string[]
     */
    public function attributeLabels()
    {
        return [
            'copyright_logo' => '版权logo',
            'copyright_companyname' => '公司名称',
            'copyright_url' => '版权链接',
            'copyright_desc' => '版权信息',
        ];
    }

    /**
     * @return array
     */
    public function attributeHints()
    {
        return [
            'copyright_logo' => '建议使用宽280像素-高50像素内的GIF或PNG透明图片',
        ];
    }
}