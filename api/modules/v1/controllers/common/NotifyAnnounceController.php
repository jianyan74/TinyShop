<?php

namespace addons\TinyShop\api\modules\v1\controllers\common;

use Yii;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use api\controllers\OnAuthController;
use common\enums\StatusEnum;
use addons\TinyShop\common\models\common\NotifyAnnounce;

/**
 * Class NotifyAnnounceController
 * @package addons\TinyShop\api\modules\v1\controllers\common
 * @author jianyan74 <751393839@qq.com>
 */
class NotifyAnnounceController extends OnAuthController
{
    /**
     * @var NotifyAnnounce
     */
    public $modelClass = NotifyAnnounce::class;

    /**
     * 不用进行登录验证的方法
     * 例如： ['index', 'update', 'create', 'view', 'delete']
     * 默认全部需要验证
     *
     * @var array
     */
    protected $authOptional = ['index', 'view'];

    /**
     * 首页
     *
     * @return ActiveDataProvider
     */
    public function actionIndex()
    {
        return new ActiveDataProvider([
            'query' => $this->modelClass::find()
                ->select(['id', 'title', 'cover', 'synopsis', 'view', 'created_at'])
                ->where(['status' => StatusEnum::ENABLED])
                ->andWhere(['merchant_id' => Yii::$app->services->merchant->getNotNullId()])
                ->orderBy('id desc')
                ->asArray(),
            'pagination' => [
                'pageSize' => $this->pageSize,
                'validatePage' => false,// 超出分页不返回data
            ],
        ]);
    }

    /**
     * @param $id
     * @return \yii\db\ActiveRecord
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        /* @var $model \yii\db\ActiveRecord */
        if (empty($id) || !($model = $this->modelClass::find()->where([
                'id' => $id,
                'status' => StatusEnum::ENABLED,
            ])->andFilterWhere(['merchant_id' => $this->getMerchantId()])->one())) {
            throw new NotFoundHttpException('请求的数据不存在');
        }

        return $model;
    }
}
