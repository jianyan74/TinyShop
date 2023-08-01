<?php

namespace addons\TinyShop\api\modules\v1\forms;

use addons\TinyShop\common\models\marketing\PeerPay;

/**
 * Class PeerPayForm
 * @package addons\TinyShop\api\modules\v1\forms
 */
class PeerPayForm extends PeerPay
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['leave_word'], 'string', 'max' => 200],
        ];
    }
}