<?php

namespace addons\TinyShop\api\modules\v1\controllers\member;

use Yii;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;
use api\controllers\UserAuthController;
use common\helpers\ResultHelper;
use common\enums\StatusEnum;
use addons\TinyShop\api\modules\v1\forms\EvaluateForm;
use addons\TinyShop\common\models\product\Evaluate;
use addons\TinyShop\api\modules\v1\forms\EvaluateStatForm;

/**
 * 评论
 *
 * Class EvaluateController
 * @package addons\TinyShop\api\modules\v1\controllers\member
 * @author jianyan74 <751393839@qq.com>
 */
class EvaluateController extends UserAuthController
{
    /**
     * @var Evaluate
     */
    public $modelClass = Evaluate::class;

    /**
     * @return mixed|\yii\db\ActiveRecord
     */
    public function actionCreate()
    {
        $data = Yii::$app->request->post('evaluate');
        if (!$data) {
            return ResultHelper::json(422, '找不到数据');
        }

        try {
            $data = Json::decode($data);
        } catch (\Exception $e) {
            return ResultHelper::json(422, '提交的数据格式有误');
        }

        // 开始事物
        $transaction = Yii::$app->db->beginTransaction();
        try {
            foreach ($data as $datum) {
                $model = new EvaluateForm();
                $model = $model->loadDefaultValues();
                $model->attributes = $datum;

                if (!$model->save()) {
                    return ResultHelper::json(422, $this->getError($model));
                }
            }

            $transaction->commit();

            return ResultHelper::json(200, '评价成功');
        } catch (\Exception $e) {
            $transaction->rollBack();

            // 返回数据验证失败
            return ResultHelper::json(422, $e->getMessage());
        }
    }

    /**
     * 追加评价
     *
     * @param $id
     * @return mixed|\yii\db\ActiveRecord
     * @throws NotFoundHttpException
     */
    public function actionAgain()
    {
        $order_product_id = Yii::$app->request->get('order_product_id');
        $again_covers = Yii::$app->request->post('again_covers');
        $again_content = Yii::$app->request->post('again_content');

        /** @var Evaluate $model */
        if (!($model = Yii::$app->tinyShopService->productEvaluate->findByOrderProductId($order_product_id))) {
            return ResultHelper::json(422, '请先评价再来追加');
        }

        if ($model['member_id'] != Yii::$app->user->identity->member_id) {
            return ResultHelper::json(422, '权限不足');
        }

        if (!$again_content) {
            return ResultHelper::json(422, '请填写追加内容');
        }

        if (!empty($model->again_content)) {
            return ResultHelper::json(422, '您已追加评价');
        }

        $model->has_again = StatusEnum::ENABLED;
        $model->again_content = $again_content;
        $model->again_covers = Json::decode($again_covers);
        $model->again_addtime = time();
        if (!$model->save()) {
            return ResultHelper::json(422, $this->getError($model));
        }

        // 更新商品评价标签
        Yii::$app->tinyShopService->productEvaluateStat->updateNum(new EvaluateStatForm([
            'has_again' => true,
        ]), $model->product_id);
        // 更新评价的追加状态
        Yii::$app->tinyShopService->orderProduct->superadditionEvaluate($model->order_product_id);

        return $model;
    }
}