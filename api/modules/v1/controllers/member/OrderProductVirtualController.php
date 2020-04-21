<?php

namespace addons\TinyShop\api\modules\v1\controllers\member;

use Yii;
use yii\data\ActiveDataProvider;
use common\enums\StatusEnum;
use addons\TinyShop\common\models\order\ProductVirtual;
use api\controllers\UserAuthController;

/**
 * 卡卷详情
 *
 * Class OrderProductVirtualController
 * @package addons\TinyShop\api\modules\v1\controllers\member
 * @author jianyan74 <751393839@qq.com>
 */
class OrderProductVirtualController extends UserAuthController
{
    /**
     * @var ProductVirtual
     */
    public $modelClass = ProductVirtual::class;

    /**
     * 首页
     *
     * @return ActiveDataProvider
     */
    public function actionIndex()
    {
        return new ActiveDataProvider([
            'query' => $this->modelClass::find()
                ->where(['status' => StatusEnum::ENABLED, 'member_id' => Yii::$app->user->identity->member_id])
                ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
                ->with('orderProduct')
                ->orderBy('id desc')
                ->asArray(),
            'pagination' => [
                'pageSize' => $this->pageSize,
                'validatePage' => false,// 超出分页不返回data
            ],
        ]);
    }

    /**
     * @return array
     */
    public function actionDetails()
    {
        $member_id = Yii::$app->user->identity->member_id;
        $order_sn = Yii::$app->request->get('order_sn');
        $order_product_id = Yii::$app->request->get('order_product_id');

        if ($order_sn) {
            return Yii::$app->tinyShopService->orderProductVirtual->findByOrderSnAndMemberId($order_sn, $member_id);
        }

        return Yii::$app->tinyShopService->orderProductVirtual->findByOrderProductIdAndMemberId($order_product_id, $member_id);
    }
}