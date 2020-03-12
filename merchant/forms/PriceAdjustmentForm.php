<?php

namespace addons\TinyShop\merchant\forms;

use addons\TinyShop\common\models\order\Order;
use addons\TinyShop\common\models\order\OrderProduct;
use yii\base\Model;
use yii\web\NotFoundHttpException;

/**
 * Class PriceAdjustmentForm
 * @package addons\TinyShop\merchant\forms
 * @author jianyan74 <751393839@qq.com>
 */
class PriceAdjustmentForm extends Model
{
    public $order_product_ids = [];
    public $shipping_money;

    public function rules()
    {
        return [
            ['shipping_money', 'number', 'min' => 0],
            ['order_product_ids', 'safe'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'shipping_money' => '运费',
        ];
    }

    /**
     * @throws NotFoundHttpException
     */
    public function verifyNumber()
    {
        $order_product_ids = $this->order_product_ids;
        foreach ($order_product_ids as $key => $value) {
            if (!is_numeric($value)) {
                throw new NotFoundHttpException('调价价格不是一个数字');
            }
        }
    }

    /**
     * @param Order $order
     * @throws NotFoundHttpException
     */
    public function save(Order $order)
    {
        $this->verifyNumber();
        $product = $order->product;

        $all_product_money = 0;
        /** @var OrderProduct $item */
        foreach ($product as $item) {
            if (isset($this->order_product_ids[$item['id']])) {
                $item->adjust_money = $this->order_product_ids[$item['id']];
                $item->product_money = ($item->price * $item->num) + $item->adjust_money;
                $item->save();

                $all_product_money += $item->product_money;
            }
        }

        // 修改订单产品价格
        $order->shipping_money = $this->shipping_money;
        $order->product_money = $all_product_money;
        $order->save();
    }
}