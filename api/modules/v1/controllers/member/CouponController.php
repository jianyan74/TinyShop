<?php

namespace addons\TinyShop\api\modules\v1\controllers\member;

use Yii;
use yii\data\ActiveDataProvider;
use api\controllers\UserAuthController;
use addons\TinyShop\common\models\marketing\Coupon;
use common\enums\StatusEnum;

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
     * @return ActiveDataProvider
     */
    public function actionIndex()
    {
        $state = Yii::$app->request->get('state', 1);

        switch ($state) {
            case Coupon::STATE_GET :
                $where = [
                    'and',
                    ['member_id' => Yii::$app->user->identity->member_id],
                    ['state' => $state],
                    ['status' => StatusEnum::ENABLED],
                    ['between', 'start_time', 'end_time', time()],
                ];

                $orderBy = 'fetch_time desc, id desc';
                break;
            case Coupon::STATE_PAST_DUE :
                $where = [
                    'and',
                    ['member_id' => Yii::$app->user->identity->member_id],
                    ['status' => StatusEnum::ENABLED],
                    [
                        'or',
                        ['state' => $state],
                        [
                            'and',
                            ['state' => Coupon::STATE_GET],
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

        return new ActiveDataProvider([
            'query' => $this->modelClass::find()
                ->where($where)
                ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
                ->with(['usableProduct', 'couponType', 'merchant'])
                ->orderBy($orderBy)
                ->asArray(),
            'pagination' => [
                'pageSize' => $this->pageSize,
                'validatePage' => false,// 超出分页不返回data
            ],
        ]);
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
            'state' => Coupon::STATE_PAST_DUE
        ]);
    }
}