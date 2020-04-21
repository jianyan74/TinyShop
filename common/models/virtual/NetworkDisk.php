<?php

namespace addons\TinyShop\common\models\virtual;

use common\helpers\ArrayHelper;

/**
 * 网盘商品
 *
 * 不支持多规格
 *
 * Class NetworkDisk
 * @package addons\TinyShop\common\models\virtual
 * @author jianyan74 <751393839@qq.com>
 */
class NetworkDisk extends BaseVirtual
{
    public $cloud_address;
    public $cloud_password;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['cloud_address', 'cloud_password'], 'string'],
            [['cloud_address', 'cloud_password'], 'required'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'cloud_address' => '网盘地址',
            'cloud_password' => '网盘密码',
        ]);
    }
}