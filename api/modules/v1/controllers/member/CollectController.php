<?php

namespace addons\TinyShop\api\modules\v1\controllers\member;

use Yii;
use yii\data\ActiveDataProvider;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use api\controllers\UserAuthController;
use addons\TinyShop\common\enums\CommonModelMapEnum;
use addons\TinyShop\common\models\common\Collect;
use addons\TinyShop\common\forms\ProductSearchForm;

/**
 * 我的收藏
 *
 * Class CollectController
 * @package addons\TinyShop\api\modules\v1\controllers\member
 * @author jianyan74 <751393839@qq.com>
 */
class CollectController extends UserAuthController
{
    /**
     * @var Collect
     */
    public $modelClass = Collect::class;

    /**
     * @return array|ActiveDataProvider
     */
    public function actionIndex()
    {
        $topic_type = CommonModelMapEnum::PRODUCT;

        $data = new ActiveDataProvider([
            'query' => $this->modelClass::find()
                ->select(['id', 'topic_id', 'updated_at'])
                ->where(['status' => StatusEnum::ENABLED, 'member_id' => Yii::$app->user->identity->member_id])
                ->andWhere(['topic_type' => $topic_type])
                ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
                ->orderBy('updated_at desc')
                ->asArray(),
            'pagination' => [
                'pageSize' => $this->pageSize,
                'validatePage' => false,// 超出分页不返回data
            ],
        ]);

        $models = $data->getModels();
        $productIds = ArrayHelper::getColumn($models, 'topic_id');
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
            isset($products[$model['topic_id']]) && $model = ArrayHelper::merge($model, $products[$model['topic_id']]);
        }

        return $models;
    }

    /**
     * 首页
     *
     * @return ActiveDataProvider
     */
    public function actionMerchant()
    {
        $topic_type = CommonModelMapEnum::MERCHANT;

        $data = new ActiveDataProvider([
            'query' => $this->modelClass::find()
                ->where(['status' => StatusEnum::ENABLED, 'member_id' => Yii::$app->user->identity->member_id])
                ->andWhere(['topic_type' => $topic_type])
                ->orderBy('updated_at desc')
                ->asArray(),
            'pagination' => [
                'pageSize' => $this->pageSize,
                'validatePage' => false,// 超出分页不返回data
            ],
        ]);

        $models = $data->getModels();
        foreach ($models as &$model) {
            $model['merchant'] = Yii::$app->services->merchant->findBaseById($model['topic_id']);

            $form = new ProductSearchForm();
            $form->merchant_id = $model['topic_id'];
            $form->limit = 5;
            $model['products'] = Yii::$app->tinyShopService->product->getListBySearch($form);
            $model['productTotal'] = Yii::$app->tinyShopService->product->findSellCount($form->merchant_id) ?? 0;
        }

        $data->setModels($models);

        return $data;
    }
}
