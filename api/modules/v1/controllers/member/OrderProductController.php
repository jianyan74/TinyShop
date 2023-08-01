<?php

namespace addons\TinyShop\api\modules\v1\controllers\member;

use Yii;
use api\controllers\UserAuthController;
use addons\TinyShop\common\models\order\OrderProduct;
use addons\TinyShop\common\enums\RefundStatusEnum;

/**
 * Class OrderProductController
 * @package addons\TinyShop\api\modules\v1\controllers\member
 * @author jianyan74 <751393839@qq.com>
 */
class OrderProductController extends UserAuthController
{
    /**
     * @var OrderProduct
     */
    public $modelClass = OrderProduct::class;

    /**
     * @return array|\yii\data\ActiveDataProvider|\yii\db\ActiveRecord[]
     */
    public function actionIndex()
    {
        $order_id = Yii::$app->request->get('order_id');
        $is_evaluate = Yii::$app->request->get('is_evaluate');

        return $this->modelClass::find()
            ->where([
                'order_id' => $order_id,
                'buyer_id' => Yii::$app->user->identity->member_id
            ])
            ->andWhere(['in', 'refund_status', RefundStatusEnum::evaluate()])
            ->andFilterWhere(['is_evaluate' => $is_evaluate])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->asArray()
            ->all();
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
