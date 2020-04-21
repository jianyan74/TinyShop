<?php

namespace addons\TinyShop\common\models\virtual;

use common\helpers\ArrayHelper;

/**
 * 点卡商品
 *
 * 不支持多规格
 *
 * Class Card
 * @package addons\TinyShop\common\models\virtual
 * @author jianyan74 <751393839@qq.com>
 */
class Card extends BaseVirtual
{
    public $card_password;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['card_password'], 'string'],
            [['card_password'], 'required'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'card_password' => '卡密',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeHints()
    {
        return ArrayHelper::merge(parent::attributeHints(), [
            'card_password' => '导入格式为卡号+空格+密码(可跟上附加内容)，一行一组，如AAAAA BBBBB',
        ]);
    }
}