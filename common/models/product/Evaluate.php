<?php

namespace addons\TinyShop\common\models\product;

use common\behaviors\MerchantBehavior;
use common\models\member\Member;
use addons\TinyShop\common\models\order\Order;
use addons\TinyShop\common\models\order\OrderProduct;

/**
 * This is the model class for table "{{%addon_shop_product_evaluate}}".
 *
 * @property string $id 评价ID
 * @property string $merchant_id 商户id
 * @property string $merchant_name 商户店铺名称
 * @property int $order_id 订单ID
 * @property string $order_sn 订单编号
 * @property int $order_product_id 订单项ID
 * @property int $product_id 商品ID
 * @property string $product_name 商品名称
 * @property string $product_price 商品价格
 * @property string $product_picture 商品图片
 * @property string $sku_name sku名称
 * @property string $content 评价内容
 * @property array $covers 评价图片
 * @property string $video 视频地址
 * @property string $explain_first 解释内容
 * @property int $member_id 评价人编号
 * @property string $member_nickname 评价人名称
 * @property string $member_head_portrait 头像
 * @property int $is_anonymous 0表示不是 1表示是匿名评价
 * @property int $scores 1-5分
 * @property string $again_content 追加评价内容
 * @property array $again_covers 追评评价图片
 * @property string $again_explain 追加解释内容
 * @property int $again_addtime 追加评价时间
 * @property int $explain_type 1好评2中评3差评
 * @property int $has_again 是否追加 0 否 1 是
 * @property int $has_content 是否有内容 0 否 1 是
 * @property int $has_cover 是否有图 0 否 1 是
 * @property int $has_video 是否视频 0 否 1 是
 * @property int $status 状态
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class Evaluate extends \common\models\base\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_shop_product_evaluate}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'merchant_id',
                    'order_id',
                    'order_sn',
                    'order_product_id',
                    'product_id',
                    'member_id',
                    'is_anonymous',
                    'scores',
                    'again_addtime',
                    'explain_type',
                    'has_again',
                    'has_content',
                    'has_cover',
                    'has_video',
                    'status',
                    'created_at',
                    'updated_at',
                ],
                'integer',
            ],
            [['product_price'], 'number'],
            [['scores'], 'integer', 'min' => 1, 'max' => 5],
            [['covers', 'again_covers'], 'safe'],
            [['scores', 'order_product_id'], 'required'],
            [['member_head_portrait'], 'string', 'max' => 150],
            [['product_name'], 'string', 'max' => 200],
            [['merchant_name', 'member_nickname'], 'string', 'max' => 100],
            [
                ['product_picture', 'sku_name', 'content', 'video', 'explain_first', 'again_content', 'again_explain'],
                'string',
                'max' => 255,
            ],
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
            'merchant_name' => '商户店铺名称',
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
            'username',
            'nickname',
            'realname',
            'head_portrait',
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
