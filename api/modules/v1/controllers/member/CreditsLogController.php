<?php

namespace addons\TinyShop\api\modules\v1\controllers\member;

use Yii;
use yii\data\ActiveDataProvider;
use api\controllers\UserAuthController;
use common\enums\StatusEnum;
use common\enums\CreditsLogTypeEnum;
use common\models\member\CreditsLog;

/**
 * 积分/余额/成长值记录
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
        $type = Yii::$app->request->get('type');
        $numType = Yii::$app->request->get('num_type');
        switch ($type) {
            case 1 :
                // 余额
                $type = [CreditsLogTypeEnum::USER_MONEY, CreditsLogTypeEnum::CONSUME_MONEY];
                break;
            case 2 :
                // 成长值
                $type = [CreditsLogTypeEnum::USER_GROWTH];
                break;
            default :
                // 积分
                $type = [CreditsLogTypeEnum::USER_INTEGRAL];
                break;
        }

        $numWhere = [];
        if (!empty($numType)) {
            // 1: 增加;2：减少;
            $numWhere = $numType == 1 ? ['>', 'num', 0] : ['<=', 'num', 0];
        }

        return new ActiveDataProvider([
            'query' => $this->modelClass::find()
                ->where([
                    'member_id' => Yii::$app->user->identity->member_id,
                    'status' => StatusEnum::ENABLED
                ])
                ->andWhere(['in', 'type', $type])
                ->andFilterWhere($numWhere)
                ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
                ->orderBy('id desc')
                ->asArray(),
            'pagination' => [
                'pageSize' => $this->pageSize,
                'validatePage' => false,// 超出分页不返回data
            ],
        ]);
    }

    /**
     * 权限验证
     *
     * @param string $action 当前的方法
     * @param null $model 当前的模型类
     * @param array $params $_GET变量
     * @throws \yii\web\BadRequestHttpException
     */
    public function checkAccess($action, $model = null, $params = [])
    {
        // 方法名称
        if (in_array($action, ['delete', 'update', 'create'])) {
            throw new \yii\web\BadRequestHttpException('权限不足');
        }
    }
}
