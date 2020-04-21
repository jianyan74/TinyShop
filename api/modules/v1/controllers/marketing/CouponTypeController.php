<?php

namespace addons\TinyShop\api\modules\v1\controllers\marketing;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\rest\Serializer;
use common\enums\StatusEnum;
use common\enums\WhetherEnum;
use common\helpers\ResultHelper;
use common\helpers\ArrayHelper;
use addons\TinyShop\common\models\forms\CouponTypeForm;
use addons\TinyShop\common\models\marketing\CouponType;
use api\controllers\OnAuthController;
use yii\web\NotFoundHttpException;

/**
 * 优惠券领取列表
 *
 * Class CouponTypeController
 * @package addons\TinyShop\api\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class CouponTypeController extends OnAuthController
{
    /**
     * @var CouponType
     */
    public $modelClass = CouponType::class;

    /**
     * 不用进行登录验证的方法
     * 例如： ['index', 'update', 'create', 'view', 'delete']
     * 默认全部需要验证
     *
     * @var array
     */
    protected $authOptional = ['index', 'view'];

    /**
     * @return mixed|ActiveDataProvider
     */
    public function actionIndex()
    {
        // 关联我已领取的优惠券
        $with = [];
        if (!Yii::$app->user->isGuest) {
            $with = ['myGet' => function(ActiveQuery $query) {
                return $query->andWhere(['member_id' => Yii::$app->user->identity->member_id]);
            }];
        }

        $data = new ActiveDataProvider([
            'query' => $this->modelClass::find()
                ->where([
                    'status' => StatusEnum::ENABLED,
                    'is_show' => WhetherEnum::ENABLED,
                ])
                ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
                ->orderBy('id desc')
                ->with(ArrayHelper::merge($with, ['usableProduct', 'merchant']))
                ->asArray(),
            'pagination' => [
                'pageSize' => $this->pageSize,
                'validatePage' => false,// 超出分页不返回data
            ],
        ]);

        // 主要生成header的page信息
        $models = (new Serializer())->serialize($data);
        foreach ($models as &$model) {
            $model = Yii::$app->tinyShopService->marketingCouponType->regroupShow($model);
        }

        return $models;
    }

    /**
     * @return mixed|\yii\db\ActiveRecord
     */
    public function actionCreate()
    {
        $data = Yii::$app->request->post();
        $model = new CouponTypeForm();
        $model->attributes = $data;
        $model->member_id = Yii::$app->user->identity->member_id;
        if (!$model->validate()) {
            return ResultHelper::json(422, $this->getError($model));
        }

        // 事务
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $model = Yii::$app->tinyShopService->marketingCoupon->give($model->couponType, $model->member_id);
            $transaction->commit();

            return ResultHelper::json(200, '领取成功', $model);
        } catch (\Exception $e) {
            $transaction->rollBack();

            return ResultHelper::json(422, $e->getMessage());
        }
    }

    /**
     * @param $id
     * @return mixed|\yii\db\ActiveRecord
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        // 关联我已领取的优惠券
        $with = [];
        if (!Yii::$app->user->isGuest) {
            $with = ['myGet' => function(ActiveQuery $query) {
                return $query->andWhere(['member_id' => Yii::$app->user->identity->member_id]);
            }];
        }

        $model = $this->modelClass::find()
            ->where([
                'id' => $id,
                'merchant_id' => $this->getMerchantId(),
                'status' => StatusEnum::ENABLED,
            ])
            ->with(ArrayHelper::merge($with, ['usableProduct']))
            ->asArray()
            ->one();

        if (!$model) {
            throw new NotFoundHttpException('请求的数据不存在');
        }

        return Yii::$app->tinyShopService->marketingCouponType->regroupShow($model);
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
        if (in_array($action, ['delete', 'update'])) {
            throw new \yii\web\BadRequestHttpException('权限不足');
        }
    }
}