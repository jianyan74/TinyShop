<?php

namespace addons\TinyShop\merchant\forms;

use addons\TinyShop\common\models\common\Notify;

/**
 * Class NotifyAnnounceForm
 * @package backend\modules\base\forms
 * @author jianyan74 <751393839@qq.com>
 */
class NotifyAnnounceForm extends Notify
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'content'], 'required'],
            [['content'], 'string'],
            [['title'], 'string', 'max' => 150],
            [['cover'], 'string', 'max' => 100],
            [['synopsis'], 'string', 'max' => 255],
        ];
    }
}