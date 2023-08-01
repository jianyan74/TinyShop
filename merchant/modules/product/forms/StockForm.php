<?php

namespace addons\TinyShop\merchant\modules\product\forms;

use yii\base\Model;
use yii\web\NotFoundHttpException;
use addons\TinyShop\common\models\product\Sku;
use addons\TinyShop\common\models\product\Product;

/**
 * Class StockForm
 * @package addons\TinyShop\merchant\modules\product\forms
 * @author jianyan74 <751393839@qq.com>
 */
class StockForm extends Model
{
    public $productId;
    public $skus;

    public function save()
    {
        $totalStock = 0;
        $minPrice = '';
        foreach ($this->skus as $sku) {
            $skuModel = new Sku();
            $skuModel->attributes = $sku;
            if (!$skuModel->validate()) {
                throw new NotFoundHttpException(\Yii::$app->services->base->analysisErr($skuModel->getFirstErrors()));
            }

            Sku::updateAll([
                'price' => $sku['price'],
                'market_price' => $sku['market_price'],
                'cost_price' => $sku['cost_price'],
                'stock' => $sku['stock'],
                'weight' => $sku['weight'],
                'volume' => $sku['volume'],
                'sku_no' => $sku['sku_no'],
                'barcode' => $sku['barcode'],
                'status' => $sku['status'],
            ], [
                'id' => $sku['id']
            ]);

            $minPrice == '' && $minPrice = $sku['price'];
            $minPrice > $sku['price'] && $minPrice = $sku['price'];

            $totalStock += $sku['stock'];
        }

        Product::updateAll(['price' => $minPrice, 'stock' => $totalStock], ['id' => $this->productId]);

        return true;
    }
}
