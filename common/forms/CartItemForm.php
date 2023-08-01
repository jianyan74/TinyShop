<?php

namespace addons\TinyShop\common\forms;

use Yii;
use yii\base\Model;
use common\enums\StatusEnum;
use common\enums\AuditStatusEnum;
use addons\TinyShop\common\models\product\Sku;
use addons\TinyShop\common\models\product\Product;

/**
 * Class CartItemForm
 * @package addons\TinyShop\common\forms
 * @author jianyan74 <751393839@qq.com>
 */
class CartItemForm extends Model
{
    public $id;

    public $sku_id;

    public $member_id;

    /**
     * @var int
     */
    public $marketing_id = 0;

    /**
     * @var string
     */
    public $marketing_type;

    /**
     * @var int
     */
    public $number;

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
            [['sku_id', 'number'], 'required', 'on' => 'create'],
            [['id', 'number'], 'required', 'on' => 'updateNumber'],
            [['id', 'sku_id'], 'required', 'on' => 'updateSku'],
            [['id', 'sku_id', 'marketing_id', 'member_id'], 'integer', 'min' => 0],
            [['number'], 'integer', 'min' => 1],
            [['sku_id'], 'verifyExist'],
            [['marketing_type'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'number' => '数量',
            'sku_id' => '规格',
            'marketing_id' => '营销ID',
            'marketing_type' => '营销类型',
            'member_id' => '用户',
        ];
    }

    /**
     * @throws \yii\web\NotFoundHttpException
     */
    public function verifyExist($attribute)
    {
        $model = Yii::$app->tinyShopService->productSku->findById($this->sku_id);
        if (!$model) {
            $this->addError($attribute, '找不到商品规格');

            return;
        }

        /** @var $product Product */
        if (empty($product = $model['product'])) {
            $this->addError($attribute, '找不到商品');

            return;
        }

        if (
            $product['audit_status'] == AuditStatusEnum::DELETE ||
            $product['status'] != StatusEnum::ENABLED
        ) {
            $this->addError($attribute, '商品已下架');

            return;
        }

        $this->_sku = $model;
    }

    public function verifyValid($number)
    {

    }

    /**
     * @return Sku
     */
    public function getSku()
    {
        return $this->_sku;
    }
}
