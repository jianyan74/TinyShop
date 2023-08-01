<?php

namespace addons\TinyShop\api\modules\v1\controllers\member;

use Yii;
use yii\data\ActiveDataProvider;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use api\controllers\UserAuthController;
use addons\TinyShop\common\models\member\Footprint;
use addons\TinyShop\common\forms\ProductSearchForm;

/**
 * 足迹
 *
 * Class FootprintController
 * @package addons\TinyShop\api\modules\v1\controllers\member
 * @author jianyan74 <751393839@qq.com>
 */
class FootprintController extends UserAuthController
{
    /**
     * @var Footprint
     */
    public $modelClass = Footprint::class;

    /**
     * @return array|ActiveDataProvider|\yii\db\ActiveRecord[]
     */
    public function actionIndex()
    {
        $start_time = Yii::$app->request->get('start_time');
        $end_time = Yii::$app->request->get('end_time');

        $data = new ActiveDataProvider([
            'query' => $this->modelClass::find()
                ->select(['id', 'product_id', 'updated_at'])
                ->where(['status' => StatusEnum::ENABLED, 'member_id' => Yii::$app->user->identity->member_id])
                ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
                ->andFilterWhere(['>', 'updated_at', $start_time])
                ->andFilterWhere(['<', 'updated_at', $end_time])
                ->orderBy('updated_at desc')
                ->asArray(),
            'pagination' => [
                'pageSize' => $this->pageSize,
                'validatePage' => false,// 超出分页不返回data
            ],
        ]);

        $models = $data->getModels();
        $productIds = ArrayHelper::getColumn($models, 'product_id');
        if (empty($productIds)) {
            return [];
        }

        // 查询商品
        $model = new ProductSearchForm();
        $model->ids = $productIds;
        $model->current_level = Yii::$app->tinyShopService->member->getCurrentLevel(Yii::$app->user->identity->member_id);
        $products = Yii::$app->tinyShopService->product->getListBySearch($model);
        $products = ArrayHelper::arrayKey($products, 'id');

        // 重新排序
        foreach ($models as &$model) {
            unset($model['id']);
            $model['created_at'] = $model['updated_at'];
            isset($products[$model['product_id']]) && $model = ArrayHelper::merge($model, $products[$model['product_id']]);
        }

        return $models;
    }
}
