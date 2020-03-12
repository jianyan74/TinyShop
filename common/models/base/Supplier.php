<?php

namespace addons\TinyShop\common\models\base;

use common\behaviors\MerchantBehavior;

/**
 * This is the model class for table "{{%addon_shop_supplier}}".
 *
 * @property int $id
 * @property string $merchant_id 商户id
 * @property string $name 供货商名称
 * @property string $desc 供货商描述
 * @property string $linkman_tel 联系人电话
 * @property string $linkman_name 联系人姓名
 * @property string $linkman_address 联系人地址
 * @property int $sort 排序
 * @property int $status 状态
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class Supplier extends \common\models\base\BaseModel
{
    use MerchantBehavior;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_shop_supplier}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'linkman_name', 'linkman_tel', 'linkman_address'], 'required'],
            [['merchant_id', 'sort', 'status', 'created_at', 'updated_at'], 'integer'],
            [['name', 'linkman_name'], 'string', 'max' => 50],
            [['desc'], 'string', 'max' => 1000],
            [['linkman_tel', 'linkman_address'], 'string', 'max' => 255],
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
            'name' => '供货商名称',
            'desc' => '供货商描述',
            'linkman_tel' => '联系人电话',
            'linkman_name' => '联系人姓名',
            'linkman_address' => '联系人地址',
            'sort' => '排序',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
