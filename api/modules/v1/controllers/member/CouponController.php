<?php

namespace addons\TinyShop\api\modules\v1\controllers\member;

use Yii;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use api\controllers\UserAuthController;
use addons\TinyShop\common\models\marketing\Coupon;
use common\enums\StatusEnum;
use common\enums\UseStateEnum;

/**
 * 我的优惠券
 *
 * Class CouponController
 * @package addons\TinyShop\api\controllers\member
 * @author jianyan74 <751393839@qq.com>
 */
class CouponController extends UserAuthController
{
    /**
     * @var Coupon
     */
    public $modelClass = Coupon::class;

    /**
     * @return array
     */
    public function actionIndex()
    {
        $member_id = Yii::$app->user->identity->member_id;
        $state = Yii::$app->request->get('state', UseStateEnum::GET);

        switch ($state) {
            case UseStateEnum::GET :
                $where = [
                    'and',
                    ['member_id' => Yii::$app->user->identity->member_id],
                    ['state' => $state],
                    ['status' => StatusEnum::ENABLED],
                    ['between', 'start_time', 'end_time', time()],
                ];

                $orderBy = 'fetch_time desc, id desc';
                break;
            case UseStateEnum::PAST_DUE :
                $where = [
                    'and',
                    ['member_id' => Yii::$app->user->identity->member_id],
                    ['status' => StatusEnum::ENABLED],
                    [
                        'or',
                        ['state' => $state],
                        [
                            'and',
                            ['state' => UseStateEnum::GET],
                            ['<', 'end_time', time()],
                        ],
                    ],
                ];

                $orderBy = 'end_time desc, id desc';
                break;
            default :
                $where = [
                    'and',
                    ['member_id' => Yii::$app->user->identity->member_id],
                    ['state' => $state],
                    ['status' => StatusEnum::ENABLED],
                ];

                $orderBy = 'use_time desc, id desc';
                break;
        }

        $data = new ActiveDataProvider([
            'query' => $this->modelClass::find()
                ->where($where)
                ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
                ->with(['couponType', 'baseMerchant'])
                ->orderBy($orderBy)
                ->asArray(),
            'pagination' => [
                'pageSize' => $this->pageSize,
                'validatePage' => false,// 超出分页不返回data
            ],
        ]);

        return [
            'list' => $data->getModels(),
            'groupCount' => Yii::$app->tinyShopService->marketingCoupon->findStateCount($member_id),
        ];
    }

    /**
     * @param $id
     * @return \yii\db\ActiveRecord
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        /* @var $model \yii\db\ActiveRecord */
        if (empty($id) || !($model = $this->modelClass::find()
                ->where([
                    'id' => $id,
                    'status' => StatusEnum::ENABLED,
                ])
                ->with(['couponType', 'baseMerchant'])
                ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
                ->asArray()
                ->one())
        ) {
            throw new NotFoundHttpException('请求的数据不存在');
        }

        return $model;
    }

    /**
     * 清空已过期的优惠券
     *
     * @param $member_id
     */
    public function actionClear()
    {
        return Coupon::updateAll(['status' => StatusEnum::DELETE], [
            'member_id' => Yii::$app->user->identity->member_id,
            'status' => StatusEnum::ENABLED,
            'state' => UseStateEnum::PAST_DUE
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
