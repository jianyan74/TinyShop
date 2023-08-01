<?php

namespace addons\TinyShop\common\traits;

use Yii;
use yii\web\NotFoundHttpException;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use common\models\base\SearchModel;
use addons\TinyShop\common\enums\ProductTypeEnum;
use addons\TinyShop\common\models\product\Product;

/**
 * Trait ProductSelectTrait
 * @package addons\TinyShop\common\traits
 */
trait ProductSelectTrait
{
    /**
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionSelect()
    {
        $this->layout = '@backend/views/layouts/blank';
        $multiple = Yii::$app->request->get('multiple');
        $is_virtual = Yii::$app->request->get('is_virtual');
        $type = ProductTypeEnum::entity();
        $is_virtual == StatusEnum::ENABLED && $type = [];

        $searchModel = new SearchModel([
            'model' => Product::class,
            'scenario' => 'default',
            'partialMatchAttributes' => ['name'], // 模糊查询
            'defaultOrder' => [
                'sort' => SORT_ASC,
                'id' => SORT_DESC,
            ],
            'pageSize' => $this->pageSize,
        ]);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query
            ->select([
                'id',
                'name',
                'type',
                'cost_price',
                'price',
                'unit',
                'real_sales',
                'is_member_discount',
                'is_spec',
                'cate_id',
                'stock',
                'sku_no',
                'barcode',
                'picture'
            ])
            ->andWhere(['status' => StatusEnum::ENABLED])
            ->andWhere(['audit_status' => StatusEnum::ENABLED])
            ->andFilterWhere(['in', 'type', $type])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->with(['cate', 'sku'])
            ->asArray();

        /** @var  $gridSelectType */
        $gridSelectType = [
            'class' => 'yii\grid\CheckboxColumn',
            'property' => 'checkboxOptions',
        ];

        if ($multiple == false) {
            $gridSelectType = [
                'class' => 'yii\grid\RadioButtonColumn',
                'property' => 'radioOptions',
            ];
        }

        // 获取正在参与的营销
        $models = $dataProvider->getModels();
        $marketing = Yii::$app->tinyShopService->marketingProduct->getMarketingType(ArrayHelper::getColumn($models, 'id'));

        return $this->render('@addons/TinyShop/merchant/modules/product/views/product/select', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'gridSelectType' => $gridSelectType,
            'marketing' => $marketing,
            'cate' => Yii::$app->tinyShopService->productCate->getMapList(),
        ]);
    }
}
