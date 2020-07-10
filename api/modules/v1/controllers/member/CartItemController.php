<?php

namespace addons\TinyShop\api\modules\v1\controllers\member;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;
use common\helpers\ResultHelper;
use common\helpers\ArrayHelper;
use addons\TinyShop\common\models\forms\CartItemForm;
use addons\TinyShop\common\models\member\CartItem;
use addons\TinyShop\common\interfaces\CartItemInterface;
use api\controllers\UserAuthController;

/**
 * 购物车
 *
 * Class CartItemController
 * @package addons\TinyShop\api\controllers
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
     * @return mixed|ActiveDataProvider
     */
    public function actionIndex()
    {
        return Yii::$app->tinyShopService->memberCartItem->all($this->member_id);
    }

    /**
     * 同步
     *
     * @return bool
     * @throws \yii\web\UnprocessableEntityHttpException
     */
    public function actionSync()
    {
        $data = Yii::$app->request->post('all');
        $data = Json::decode($data);

        foreach ($data as $datum) {
            $model = new CartItemForm();
            $model->attributes = $datum;
            if ($model->validate()) {
                Yii::$app->tinyShopService->memberCartItem->create($model->getSku(), $model->num, $this->member_id);
            }
        }

        return true;
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
        if (!$model->validate()) {
            return ResultHelper::json(422, $this->getError($model));
        }

        return Yii::$app->tinyShopService->memberCartItem->create($model->getSku(), $model->num, $this->member_id);
    }

    /**
     * 修改购物车数量
     *
     * @return CartItem|array|mixed|\yii\db\ActiveRecord|null
     * @throws \yii\web\UnprocessableEntityHttpException
     */
    public function actionUpdateNum()
    {
        $model = new CartItemForm();
        $model->attributes = Yii::$app->request->post();
        if (!$model->validate()) {
            return ResultHelper::json(422, $this->getError($model));
        }

        $data = Yii::$app->tinyShopService->memberCartItem->updateNum($model->getSku(), $model->num,
            $this->member_id);

        $sku = $data->sku;
        $data = ArrayHelper::toArray($data);
        $data['ladderPreferential'] = [];
        $data['sku'] = $sku;

        return $data;
    }

    /**
     * 修改购物车sku
     *
     * @param $product_id
     * @param $sku_id
     * @param $member_id
     */
    public function actionUpdateSku()
    {
        $sku_id = Yii::$app->request->post('sku_id', null);
        $new_sku_id = Yii::$app->request->post('new_sku_id', null);

        /** @var CartItem $cartItem */
        $cartItem = Yii::$app->tinyShopService->memberCartItem->findBySukId($sku_id, $this->member_id);
        if (!$cartItem) {
            return ResultHelper::json(422, '购物车找不到该产品');
        }

        // 事务
        $transaction = Yii::$app->db->beginTransaction();
        try {
            Yii::$app->tinyShopService->memberCartItem->deleteBySkuIds([$sku_id], $this->member_id);

            // 判断购物车是否已存在该sku
            $newCartItem = Yii::$app->tinyShopService->memberCartItem->findBySukId($new_sku_id, $this->member_id);
            if ($newCartItem) {
                $sku = Yii::$app->tinyShopService->productSku->findById($newCartItem->sku_id);
                Yii::$app->tinyShopService->memberCartItem->updateNum($sku, $cartItem->number + $newCartItem->number, $this->member_id);
                $transaction->commit();

                $sku = $newCartItem->sku;
                $newCartItem = ArrayHelper::toArray($newCartItem);
                $newCartItem['ladderPreferential'] = [];
                $newCartItem['sku'] = $sku;

                return $newCartItem;
            }

            $model = new CartItemForm();
            $model->sku_id = $new_sku_id;
            $model->num = $cartItem->number;
            if (!$model->validate()) {
                throw new NotFoundHttpException($this->getError($model));
            }

            $data = Yii::$app->tinyShopService->memberCartItem->create($model->getSku(), $model->num,
                $this->member_id);

            $sku = $data->sku;
            $data = ArrayHelper::toArray($data);
            $data['ladderPreferential'] = [];
            $data['sku'] = $sku;

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
        $sku_ids = Yii::$app->request->post('sku_ids', '');

        try {
            $sku_ids = Json::decode($sku_ids);
        } catch (\Exception $e) {
            return ResultHelper::json(422, '请提交正确的 json 格式');
        }

        if (empty($sku_ids)) {
            return ResultHelper::json(422, '请选择要删除的购物车产品');
        }

        return Yii::$app->tinyShopService->memberCartItem->deleteBySkuIds($sku_ids, $this->member_id);
    }

    /**
     * @return int|string
     */
    public function actionCount()
    {
        return Yii::$app->tinyShopService->memberCartItem->count($this->member_id);
    }

    /**
     * 清空购物车
     *
     * @param $member_id
     */
    public function actionClear()
    {
        // 清空失效的购物车产品
        $lose_status = Yii::$app->request->post('lose_status');

        return Yii::$app->tinyShopService->memberCartItem->clear($this->member_id, $lose_status);
    }
}