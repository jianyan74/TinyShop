<?php

namespace addons\TinyShop\api\modules\v1\controllers\member;

use Yii;
use api\controllers\UserAuthController;
use common\models\member\Address;

/**
 * 地址
 *
 * Class AddressController
 * @package addons\TinyShop\api\controllers\member
 * @author jianyan74 <751393839@qq.com>
 */
class AddressController extends UserAuthController
{
    /**
     * @var Address
     */
    public $modelClass = Address::class;

    /**
     * @return array|\yii\db\ActiveRecord|null
     */
    public function actionDefault()
    {
        return Yii::$app->tinyShopService->memberAddress->findDefaultByMemberId(Yii::$app->user->identity->member_id);
    }
}