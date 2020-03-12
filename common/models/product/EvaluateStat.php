<?php

namespace addons\TinyShop\common\models\product;

use yii\db\ActiveRecord;
use common\behaviors\MerchantBehavior;

/**
 * This is the model class for table "{{%addon_shop_product_evaluate_stat}}".
 *
 * @property string $id
 * @property string $merchant_id 商户id
 * @property int $product_id 商品ID
 * @property string $cover_num 有图数量
 * @property int $video_num 视频数量
 * @property string $again_num 追加数量
 * @property int $good_num 好评数量
 * @property int $ordinary_num 中评数量
 * @property int $negative_num 差评数量
 * @property array $tags 其他标签
 */
class EvaluateStat extends ActiveRecord
{
    use MerchantBehavior;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_shop_product_evaluate_stat}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'product_id', 'cover_num', 'video_num', 'again_num', 'good_num', 'ordinary_num', 'negative_num', 'total_num'], 'integer'],
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
        ];
    }
}
