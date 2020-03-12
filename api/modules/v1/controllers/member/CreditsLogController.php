<?php

namespace addons\TinyShop\api\modules\v1\controllers\member;

use Yii;
use yii\data\ActiveDataProvider;
use api\controllers\UserAuthController;
use common\enums\StatusEnum;
use common\models\member\CreditsLog;

/**
 * 积分/余额记录
 *
 * Class CreditsLogController
 * @package addons\TinyShop\api\modules\v1\controllers\member
 * @author jianyan74 <751393839@qq.com>
 */
class CreditsLogController extends UserAuthController
{
    /**
     * @var CreditsLog
     */
    public $modelClass = CreditsLog::class;

    /**
     * 首页
     *
     * @return ActiveDataProvider
     */
    public function actionIndex()
    {
        $credit_type = [CreditsLog::CREDIT_TYPE_USER_INTEGRAL, CreditsLog::CREDIT_TYPE_GIVE_INTEGRAL];
        if (Yii::$app->request->get('credit_type') == StatusEnum::ENABLED) {
            $credit_type = [CreditsLog::CREDIT_TYPE_USER_MONEY, CreditsLog::CREDIT_TYPE_GIVE_MONEY, CreditsLog::CREDIT_TYPE_CONSUME_MONEY];
        }

        $num_where = [];
        if (!empty($num_type = Yii::$app->request->get('num_type'))) {
            // 1: 增加;2：减少;
            $num_where = $num_type == 1 ? ['>', 'num', 0] : ['<=', 'num', 0];
        }

        return new ActiveDataProvider([
            'query' => $this->modelClass::find()
                ->where(['status' => StatusEnum::ENABLED, 'member_id' => Yii::$app->user->identity->member_id])
                ->andWhere(['in', 'credit_type', $credit_type])
                ->andFilterWhere($num_where)
                ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
                ->orderBy('id desc')
                ->asArray(),
            'pagination' => [
                'pageSize' => $this->pageSize,
                'validatePage' => false,// 超出分页不返回data
            ],
        ]);
    }
}