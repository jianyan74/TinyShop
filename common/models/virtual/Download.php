<?php

namespace addons\TinyShop\common\models\virtual;

use common\helpers\ArrayHelper;

/**
 * 下载商品
 *
 * 不支持多规格
 *
 * Class Download
 * @package addons\TinyShop\common\models\product
 * @author jianyan74 <751393839@qq.com>
 */
class Download extends BaseVirtual
{
    public $text_download_resources;
    public $unzip_password;
    public $confine_use_number = 0;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['text_download_resources', 'unzip_password'], 'string'],
            [['text_download_resources'], 'required'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'text_download_resources' => '下载地址',
            'unzip_password' => '解压密码',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeHints()
    {
        return ArrayHelper::merge(parent::attributeHints(), [
            'text_download_resources' => '上传附件类型必须是zip格式',
        ]);
    }
}

