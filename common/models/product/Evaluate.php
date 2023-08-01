<?php

namespace addons\TinyShop\common\models\product;

use common\models\member\Member;
use addons\TinyShop\common\models\order\Order;
use addons\TinyShop\common\models\order\OrderProduct;

/**
 * This is the model class for table "{{%addon_tiny_shop_product_evaluate}}".
 *
 * @property int $id 评价ID
 * @property int $merchant_id 商户id
 * @property int|null $order_id 订单ID
 * @property string|null $order_sn 订单编号
 * @property int|null $order_product_id 订单项ID
 * @property int|null $product_id 商品ID
 * @property string|null $product_name 商品名称
 * @property float|null $product_price 商品价格
 * @property string|null $product_picture 商品图片
 * @property string|null $sku_name sku名称
 * @property string $content 评价内容
 * @property string|null $covers 评价图片
 * @property string|null $video 视频地址
 * @property string|null $explain_first 解释内容
 * @property int|null $member_id 评价人编号
 * @property string|null $member_nickname 评价人名称
 * @property string|null $member_head_portrait 头像
 * @property int|null $is_anonymous 0表示不是 1表示是匿名评价
 * @property int $scores 1-5分
 * @property string|null $again_content 追加评价内容
 * @property string|null $again_covers 追评评价图片
 * @property string|null $again_explain 追加解释内容
 * @property int|null $again_addtime 追加评价时间
 * @property int|null $explain_type 1好评2中评3差评
 * @property int|null $has_again 是否追加 0 否 1 是
 * @property int|null $has_content 是否有内容 0 否 1 是
 * @property int|null $has_cover 是否有图 0 否 1 是
 * @property int|null $has_video 是否视频 0 否 1 是
 * @property int|null $is_auto 是否自动评价 0 否 1 是
 * @property int|null $status 状态
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class Evaluate extends \common\models\base\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_product_evaluate}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'order_id', 'order_product_id', 'product_id', 'member_id', 'is_anonymous', 'scores', 'again_addtime', 'explain_type', 'has_again', 'has_content', 'has_cover', 'has_video', 'is_auto', 'status', 'created_at', 'updated_at'], 'integer'],
            [['product_price'], 'number'],
            [['covers', 'again_covers'], 'safe'],
            [['scores'], 'required'],
            [['order_sn'], 'string', 'max' => 30],
            [['product_name', 'member_nickname'], 'string', 'max' => 100],
            [['product_picture', 'content', 'video', 'explain_first', 'again_content', 'again_explain'], 'string', 'max' => 255],
            [['sku_name'], 'string', 'max' => 50],
            [['member_head_portrait'], 'string', 'max' => 150],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'merchant_id' => '商户id',
            'order_id' => '订单ID',
            'order_sn' => '订单编号',
            'order_product_id' => '订单项ID',
            'product_id' => '商品ID',
            'product_name' => '商品名称',
            'product_price' => '商品价格',
            'product_picture' => '商品图片',
            'sku_name' => 'sku名称',
            'content' => '评价内容',
            'covers' => '评价图片',
            'video' => '视频地址',
            'explain_first' => '解释内容',
            'member_id' => '评价人编号',
            'member_nickname' => '评价人名称',
            'member_head_portrait' => '头像',
            'is_anonymous' => '0表示不是 1表示是匿名评价',
            'scores' => '1-5分',
            'again_content' => '追加评价内容',
            'again_covers' => '追评评价图片',
            'again_explain' => '追加解释内容',
            'again_addtime' => '追加评价时间',
            'explain_type' => '1好评2中评3差评',
            'has_again' => '是否追加 0 否 1 是',
            'has_content' => '是否有内容 0 否 1 是',
            'has_cover' => '是否有图 0 否 1 是',
            'has_video' => '是否视频 0 否 1 是',
            'is_auto' => '是否自动评价 0 否 1 是',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * 关联订单
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }

    /**
     * 用户
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasOne(Member::class, ['id' => 'member_id'])->select([
            'id',
            'nickname',
            'head_portrait',
            'type',
        ]);
    }

    /**
     * 关联商品
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    /**
     * 关联订单商品
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrderProduct()
    {
        return $this->hasOne(OrderProduct::class, ['id' => 'order_product_id']);
    }
}
