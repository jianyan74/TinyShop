<?php

namespace addons\TinyShop\api\modules\v1\controllers\member;

use Yii;
use yii\web\NotFoundHttpException;
use api\controllers\UserAuthController;
use common\enums\StatusEnum;
use common\models\member\Account;

/**
 * Class AccountController
 * @package addons\TinyShop\api\modules\v1\controllers\member
 * @author jianyan74 <751393839@qq.com>
 */
class AccountController extends UserAuthController
{
    /**
     * @var Account
     */
    public $modelClass = Account::class;

    /**
     * @return \yii\db\ActiveRecord
     * @throws NotFoundHttpException
     */
    public function actionDetail()
    {
        /* @var $model \yii\db\ActiveRecord */
        if (empty($id) || !($model = $this->modelClass::find()->where([
                'member_id' => Yii::$app->user->identity->member_id,
                'status' => StatusEnum::ENABLED,
            ])->andFilterWhere(['merchant_id' => $this->getMerchantId()])->one())) {
            throw new NotFoundHttpException('请求的数据不存在');
        }

        return $model;
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
        if (in_array($action, ['delete', 'index', 'view', 'update', 'create'])) {
            throw new \yii\web\BadRequestHttpException('权限不足');
        }
    }
}
