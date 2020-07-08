<?php

namespace addons\TinyShop\merchant\modules\marketing\controllers;

use Yii;
use common\helpers\ResultHelper;
use common\enums\StatusEnum;
use common\models\base\SearchModel;
use common\traits\MerchantCurd;
use addons\TinyShop\merchant\controllers\BaseController;
use addons\TinyShop\common\models\marketing\MiniProgramLive;
use addons\TinyShop\common\models\marketing\MiniProgramLiveGoods;

/**
 * Class MiniProgramLiveController
 * @package addons\TinyShop\merchant\modules\marketing\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class MiniProgramLiveController extends BaseController
{
    use MerchantCurd;

    /**
     * @var MiniProgramLive
     */
    public $modelClass = MiniProgramLive::class;

    /**
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionIndex()
    {
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'relations' => [],
            'partialMatchAttributes' => ['name'], // 模糊查询
            'defaultOrder' => [
                'room_id' => SORT_DESC,
                'id' => SORT_DESC,
            ],
            'pageSize' => $this->pageSize,
        ]);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query
            ->andWhere(['>=', 'status', StatusEnum::DISABLED])
            ->andWhere(['merchant_id' => $this->getMerchantId()]);

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * 同步
     *
     * @param int $offset
     * @param int $count
     * @param int $clear
     * @return array|mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function actionSync($offset = 0, $count = 20, $clear = 0)
    {
        if ($clear == StatusEnum::ENABLED) {
            MiniProgramLive::updateAll(['status' => StatusEnum::DELETE], ['merchant_id' => Yii::$app->services->merchant->getNotNullId()]);
            MiniProgramLiveGoods::updateAll(['status' => StatusEnum::DELETE], ['merchant_id' => Yii::$app->services->merchant->getNotNullId()]);
        }

        try {
            $res = Yii::$app->tinyShopService->marketingMiniProgramLive->sync($offset, $count);
            if (is_array($res)) {
                return ResultHelper::json(200, '同步成功', $res);
            }

            return ResultHelper::json(201, '同步完成');
        } catch (\Exception $e) {
            return ResultHelper::json(422, $e->getMessage());
        }
    }
}