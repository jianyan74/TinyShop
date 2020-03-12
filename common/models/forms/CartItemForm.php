<?php

namespace addons\TinyShop\common\models\forms;

use Yii;
use yii\base\Model;
use common\enums\StatusEnum;
use addons\TinyShop\common\models\product\Sku;
use addons\TinyShop\common\models\product\Product;

/**
 * Class CartItemForm
 * @package addons\TinyShop\common\models\forms
 * @author jianyan74 <751393839@qq.com>
 */
class CartItemForm extends Model
{
    public $sku_id;

    /**
     * @var int
     */
    public $num;

    /**
     * @var Sku
     */
    private $_sku;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['sku_id', 'num'], 'required'],
            [['sku_id'], 'integer'],
            [['num'], 'integer', 'min' => 1],
            [['sku_id'], 'verifyExist'],
        ];
    }

    /**
     * @throws \yii\web\NotFoundHttpException
     */
    public function verifyExist($attribute)
    {
        $model = Yii::$app->tinyShopService->productSku->findById($this->sku_id);
        if (!$model) {
            $this->addError($attribute, '找不到产品规格');

            return;
        }

        /** @var $product Product */
        if (empty($product = $model['product'])) {
            $this->addError($attribute, '找不到产品');

            return;
        }

        if ($product['product_status'] == StatusEnum::DELETE || $product['status'] != StatusEnum::ENABLED) {
            $this->addError($attribute, '产品已下架');

            return;
        }

        $this->_sku = $model;
    }

    /**
     * @return Sku
     */
    public function getSku()
    {
        return $this->_sku;
    }
}