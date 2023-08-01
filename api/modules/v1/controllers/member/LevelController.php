<?php

namespace addons\TinyShop\api\modules\v1\controllers\member;

use Yii;
use yii\data\ActiveDataProvider;
use api\controllers\OnAuthController;
use common\enums\StatusEnum;
use common\models\member\Level;
use common\enums\MemberLevelUpgradeTypeEnum;

/**
 * Class LevelController
 * @package addons\TinyShop\api\modules\v1\controllers\member
 * @author jianyan74 <751393839@qq.com>
 */
class LevelController extends OnAuthController
{
    /**
     * @var Level
     */
    public $modelClass = Level::class;

    /**
     * @return ActiveDataProvider
     */
    public function actionIndex()
    {
        $data = new ActiveDataProvider([
            'query' => $this->modelClass::find()
                ->where(['status' => StatusEnum::ENABLED])
                ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
                ->orderBy('level asc')
                ->asArray(),
            'pagination' => [
                'pageSize' => $this->pageSize,
                'validatePage' => false,// 超出分页不返回data
            ],
        ]);

        $memberLevelUpgradeType = Yii::$app->debris->backendConfig('member_level_upgrade_type');
        $models = $data->getModels();
        foreach ($models as &$model) {
            $model['remark'] = [];
            switch ($memberLevelUpgradeType) {
                case MemberLevelUpgradeTypeEnum::CONSUMPTION_INTEGRAL :
                    $model['remark'] = '累计积分满 ' . $model['integral'] . ' 积分';
                    break;
                case MemberLevelUpgradeTypeEnum::CONSUMPTION_MONEY :
                    $model['remark'] = '消费金额满 ' . $model['money'] . ' 元';
                    break;
                case MemberLevelUpgradeTypeEnum::CONSUMPTION_GROWTH :
                    $model['remark'] = '消费成长值满 ' . $model['money'];
                    break;
            }
        }

        $data->setModels($models);

        return $data;
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
