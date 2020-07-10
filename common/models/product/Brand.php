<?php

namespace addons\TinyShop\common\models\product;

use Yii;
use common\behaviors\MerchantBehavior;

/**
 * This is the model class for table "{{%addon_shop_product_brand}}".
 *
 * @property string $id
 * @property int $cate_id 商品类别编号
 * @property string $title 品牌名称
 * @property string $cover 图片url
 * @property int $sort 排列次序
 * @property int $status 状态
 * @property int $created_at 创建时间
 * @property int $updated_at 修改时间
 */
class Brand extends \common\models\base\BaseModel
{
    use MerchantBehavior;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_shop_product_brand}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'cate_id', 'sort', 'status', 'created_at', 'updated_at'], 'integer'],
            [['title'], 'string', 'max' => 25],
            [['cover'], 'string', 'max' => 125],
            [['title'], 'uniqueTitle'],
            [['title'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cate_id' => '产品分类',
            'title' => '品牌名称',
            'cover' => '封面',
            'sort' => '排序',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    public function uniqueTitle($attribute)
    {
        if (($brand = Yii::$app->tinyShopService->productBrand->findByTitle($this->title)) && $brand['id'] != $this->id) {
            $this->addError($attribute, '品牌名称不能重复');
        }
    }

    /**
     * 关联分类
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCate()
    {
        return $this->hasOne(Cate::class, ['id' => 'cate_id']);
    }
}
