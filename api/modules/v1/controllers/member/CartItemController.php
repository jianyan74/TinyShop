<?php

namespace addons\TinyShop\api\modules\v1\controllers\member;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use common\helpers\ResultHelper;
use api\controllers\UserAuthController;
use addons\TinyShop\common\forms\CartItemForm;
use addons\TinyShop\common\models\member\CartItem;
use addons\TinyShop\common\interfaces\CartItemInterface;

/**
 * 购物车
 *
 * Class CartItemController
 * @package addons\TinyShop\api\modules\v1\controllers\member
 * @author jianyan74 <751393839@qq.com>
 */
class CartItemController extends UserAuthController
{
    /**
     * @var CartItem
     */
    public $modelClass = CartItem::class;

    /**
     * @var CartItemInterface
     */
    public $service;

    /**
     * @var int
     */
    public $member_id;

    /**
     * @param $action
     * @return bool
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\BadRequestHttpException
     * @throws \yii\web\ForbiddenHttpException
     * @throws \yii\web\NotFoundHttpException
     */
    public function beforeAction($action)
    {
        parent::beforeAction($action);

        // 已登录
        $this->member_id = Yii::$app->user->identity->member_id;

        return true;
    }

    /**
     * 购物车
     *
     * @return mixed|ActiveDataProvider
     */
    public function actionIndex()
    {
        list($carts, $loseEfficacy) = Yii::$app->tinyShopService->memberCartItem->all($this->member_id);

        return [
            'carts' => array_merge($carts),
            'lose_efficacy' => $loseEfficacy,
        ];
    }

    /**
     * 创建
     *
     * @return CartItem|array|mixed|\yii\db\ActiveRecord|null
     * @throws \yii\web\UnprocessableEntityHttpException
     */
    public function actionCreate()
    {
        $model = new CartItemForm();
        $model->attributes = Yii::$app->request->post();
        $model->member_id = $this->member_id;
        if (!$model->validate()) {
            return ResultHelper::json(422, $this->getError($model));
        }

        return Yii::$app->tinyShopService->memberCartItem->create($model);
    }

    /**
     * 修改购物车数量
     *
     * @return CartItem|array|mixed|\yii\db\ActiveRecord|null
     * @throws \yii\web\UnprocessableEntityHttpException
     */
    public function actionUpdateNumber()
    {
        $model = new CartItemForm();
        $model->attributes = Yii::$app->request->post();
        $model->member_id = $this->member_id;
        if (!$model->validate()) {
            return ResultHelper::json(422, $this->getError($model));
        }

        return Yii::$app->tinyShopService->memberCartItem->updateNumber($model);
    }

    /**
     * 修改购物车 sku
     *
     * @param $product_id
     * @param $sku_id
     * @param $member_id
     */
    public function actionUpdateSku()
    {
        $model = new CartItemForm();
        $model->attributes = Yii::$app->request->post();
        $model->member_id = $this->member_id;
        if (!$model->validate()) {
            return ResultHelper::json(422, $this->getError($model));
        }

        // 事务
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $data = Yii::$app->tinyShopService->memberCartItem->updateSku($model);
            $transaction->commit();

            return $data;
        } catch (\Exception $e) {
            $transaction->rollBack();

            return ResultHelper::json(422, $e->getMessage());
        }
    }

    /**
     * 删除一组
     *
     * @param $product_id
     * @param $sku_id
     * @param $member_id
     */
    public function actionDeleteIds()
    {
        $ids = Yii::$app->request->post('ids', '');

        try {
            $ids = Json::decode($ids);
        } catch (\Exception $e) {
            return ResultHelper::json(422, '请提交正确的 json 格式');
        }

        if (empty($ids)) {
            return ResultHelper::json(422, '请选择要删除的购物车商品');
        }

        return Yii::$app->tinyShopService->memberCartItem->deleteIds($ids, $this->member_id);
    }

    /**
     * @return int|string
     */
    public function actionCount()
    {
        return Yii::$app->tinyShopService->memberCartItem->findCountByMemberId($this->member_id);
    }

    /**
     * 清空购物车
     *
     * @param $member_id
     */
    public function actionClear()
    {
        // 清空失效的购物车商品
        $lose_status = Yii::$app->request->post('lose_status');

        return Yii::$app->tinyShopService->memberCartItem->clear($this->member_id, $lose_status);
    }
}
