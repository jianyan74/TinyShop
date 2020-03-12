<?php

namespace addons\TinyShop\services\member;

use common\components\Service;
use common\enums\StatusEnum;
use common\models\member\Address;

/**
 * Class AddressService
 * @package addons\TinyShop\services\member
 * @author jianyan74 <751393839@qq.com>
 */
class AddressService extends Service
{
    /**
     * @param $member_id
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findDefaultByMemberId($member_id)
    {
        return Address::find()
            ->where(['member_id' => $member_id])
            ->andWhere(['status' => StatusEnum::ENABLED])
            ->andWhere(['is_default' => StatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->one();
    }
}