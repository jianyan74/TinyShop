<?php

namespace addons\TinyShop\services\member;

use common\enums\StatusEnum;
use common\helpers\EchantsHelper;
use common\models\member\Member;
use common\components\Service;

/**
 * Class MemberService
 * @package addons\TinyShop\services\member
 * @author jianyan74 <751393839@qq.com>
 */
class MemberService extends Service
{
    /**
     * @param $id
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findById($id, $select = ['*'])
    {
        return Member::find()
            ->select($select)
            ->where(['id' => $id, 'status' => StatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->one();
    }
}