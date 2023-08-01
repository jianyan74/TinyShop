<?php

namespace addons\TinyShop\common\models\product;

use common\traits\Tree;
use common\behaviors\MerchantBehavior;

/**
 * This is the model class for table "{{%addon_tiny_shop_product_cate}}".
 *
 * @property int $id 主键
 * @property int|null $merchant_id 商户id
 * @property string $title 标题
 * @property string|null $subhead 副标题
 * @property string|null $cover 封面图
 * @property int|null $sort 排序
 * @property int|null $level 级别
 * @property int|null $pid 上级id
 * @property string|null $tree 树
 * @property int|null $is_recommend 推荐
 * @property int|null $status 状态[-1:删除;0:禁用;1启用]
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class Cate extends \common\models\base\BaseModel
{
    use MerchantBehavior, Tree;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_product_cate}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'sort', 'level', 'pid', 'is_recommend', 'status', 'created_at', 'updated_at'], 'integer'],
            [['tree'], 'string'],
            [['title'], 'required'],
            [['title', 'subhead'], 'string', 'max' => 50],
            [['cover'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键',
            'merchant_id' => '商户id',
            'title' => '标题',
            'subhead' => '副标题',
            'cover' => '封面图',
            'sort' => '排序',
            'level' => '级别',
            'pid' => '上级分类',
            'tree' => '树',
            'is_recommend' => '推荐',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
