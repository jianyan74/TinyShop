<?php

namespace addons\TinyShop\api\modules\v1\controllers\member;

use Yii;
use common\enums\StatusEnum;
use api\controllers\UserAuthController;
use addons\TinyShop\common\models\base\NotifySubscriptionConfig;

/**
 * Class NotifySubscriptionConfigController
 * @package addons\TinyShop\api\modules\v1\controllers\member
 * @author jianyan74 <751393839@qq.com>
 */
class NotifySubscriptionConfigController extends UserAuthController
{
    /**
     * @var string
     */
    public $modelClass = '';

    /**
     * @return NotifySubscriptionConfig|array|\yii\data\ActiveDataProvider|\yii\db\ActiveRecord|null
     */
    public function actionIndex()
    {
        $merchant_id = Yii::$app->user->identity->merchant_id;
        $member_id = Yii::$app->user->identity->member_id;

        $config = Yii::$app->tinyShopService->notifySubscriptionConfig->findByMemberId($member_id, $merchant_id);

        return $config->action;
    }

    /**
     * @return NotifySubscriptionConfig|array|\yii\db\ActiveRecord|null
     */
    public function actionUpConfig()
    {
        $all = Yii::$app->request->post('all', StatusEnum::ENABLED);
        $merchant_id = Yii::$app->user->identity->merchant_id;
        $member_id = Yii::$app->user->identity->member_id;

        $config = Yii::$app->tinyShopService->notifySubscriptionConfig->findByMemberId($member_id, $merchant_id);
        $config->action = [
            'all' => !empty($all) ? StatusEnum::ENABLED : StatusEnum::DISABLED,
        ];
        $config->save();

        return $config->action;
    }
}