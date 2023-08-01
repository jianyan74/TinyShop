<?php

namespace addons\TinyShop\common\models\product;

use Yii;

/**
 * This is the model class for table "{{%addon_tiny_shop_product_evaluate_stat}}".
 *
 * @property int $id
 * @property int $merchant_id 商户id
 * @property int|null $product_id 商品ID
 * @property int|null $cover_num 有图数量
 * @property int|null $video_num 视频数量
 * @property int|null $again_num 追加数量
 * @property int|null $good_num 好评数量
 * @property int|null $ordinary_num 中评数量
 * @property int|null $negative_num 差评数量
 * @property int|null $total_num 总数量
 * @property string|null $tags 其他标签
 * @property int|null $status 状态
 */
class EvaluateStat extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_product_evaluate_stat}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'product_id', 'cover_num', 'video_num', 'again_num', 'good_num', 'ordinary_num', 'negative_num', 'total_num', 'status'], 'integer'],
            [['tags'], 'safe'],
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
            'product_id' => '商品ID',
            'cover_num' => '有图数量',
            'video_num' => '视频数量',
            'again_num' => '追加数量',
            'good_num' => '好评数量',
            'ordinary_num' => '中评数量',
            'negative_num' => '差评数量',
            'total_num' => '总数量',
            'tags' => '其他标签',
            'status' => '状态',
        ];
    }
}
