<?php

namespace addons\TinyShop\common\models\base;

use common\behaviors\MerchantBehavior;

/**
 * This is the model class for table "{{%addon_shop_base_spec}}".
 *
 * @property string $id
 * @property string $merchant_id 商户id
 * @property string $title 规格名称
 * @property int $sort 排列次序
 * @property string $explain 规格说明
 * @property int $show_type 展示方式[1:文字;2:颜色;3:图片]
 * @property int $status 状态(-1:已删除,0:禁用,1:正常)
 * @property string $created_at 创建时间
 * @property string $updated_at 修改时间
 */
class Spec extends \common\models\base\BaseModel
{
    use MerchantBehavior;

    const SHOW_TYPE_TEXT = 1;
    const SHOW_TYPE_COLOR = 2;
    const SHOW_TYPE_IMAGE = 3;

    /**
     * @var array
     */
    public static $showTypeExplain = [
        self::SHOW_TYPE_TEXT => '文字',
        self::SHOW_TYPE_COLOR => '颜色',
        self::SHOW_TYPE_IMAGE => '图片',
    ];

    public $valueData;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_shop_base_spec}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'sort', 'show_type', 'status', 'created_at', 'updated_at'], 'integer'],
            [['title'], 'required'],
            [['title'], 'string', 'max' => 25],
            [['explain'], 'string', 'max' => 100],
            [['valueData'], 'safe'],
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
            'title' => '标题',
            'explain' => '说明',
            'sort' => '排序',
            'show_type' => '显示类型',
            'status' => '状态',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * 关联规格值
     *
     * @return \yii\db\ActiveQuery
     */
    public function getValue()
    {
        return $this->hasMany(SpecValue::class, ['spec_id' => 'id'])->orderBy('sort asc');
    }
}
