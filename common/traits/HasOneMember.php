<?php

namespace addons\TinyShop\common\traits;

use common\models\member\Member;

/**
 * Trait HasOneMember
 * @package addons\TinyShop\common\traits
 */
trait HasOneMember
{
    /**
     * 用户信息
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBaseMember()
    {
        return $this->hasOne(Member::class, ['id' => 'member_id'])->select(['id', 'nickname', 'mobile', 'head_portrait']);
    }
}