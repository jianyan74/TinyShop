<?php

namespace addons\TinyShop\common\models\product;

use Yii;
use common\behaviors\MerchantBehavior;

/**
 * This is the model class for table "{{%addon_tiny_shop_product_brand}}".
 *
 * @property int $id
 * @property int|null $merchant_id 商户id
 * @property string|null $title 品牌名称
 * @property int|null $cate_id 商品类别编号
 * @property string|null $cover 图片url
 * @property int|null $sort 排序
 * @property int|null $status 状态
 * @property int|null $created_at 创建时间
 * @property int|null $updated_at 修改时间
 */
class Brand extends \common\models\base\BaseModel
{
    use MerchantBehavior;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_product_brand}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'cate_id', 'sort', 'status', 'created_at', 'updated_at'], 'integer'],
            [['title'], 'string', 'max' => 50],
            [['title'], 'required'],
            [['title'], 'uniqueTitle'],
            [['cover'], 'string', 'max' => 200],
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
            'title' => '品牌名称',
            'cate_id' => '商品类别编号',
            'cover' => '图片',
            'sort' => '排列',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
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
