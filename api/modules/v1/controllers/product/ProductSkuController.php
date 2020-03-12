<?php

namespace addons\TinyShop\api\modules\v1\controllers\product;

use Yii;
use addons\TinyShop\common\models\product\Sku;
use api\controllers\OnAuthController;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;

/**
 * Class ProductSkuController
 * @package addons\TinyShop\api\modules\v1\controllers\product
 * @author jianyan74 <751393839@qq.com>
 */
class ProductSkuController extends OnAuthController
{
    /**
     * @var Sku
     */
    public $modelClass = Sku::class;

    /**
     * @return array|\yii\data\ActiveDataProvider
     * @throws NotFoundHttpException
     */
    public function actionIndex()
    {
        $product_id = Yii::$app->request->get('product_id');
        $product = Yii::$app->tinyShopService->product->findById($product_id);

        if (!$product) {
            throw new NotFoundHttpException('找不到产品信息');
        }

        return [
            'base_attribute_format' => Json::decode($product['base_attribute_format']),
            'sku' => Yii::$app->tinyShopService->productSku->findByProductId($product_id),
        ];
    }
}