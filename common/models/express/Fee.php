<?php

namespace addons\TinyShop\common\models\express;

use common\behaviors\MerchantBehavior;
use Yii;

/**
 * This is the model class for table "{{%addon_shop_express_fee}}".
 *
 * @property string $id 运费模板ID
 * @property string $merchant_id 商户id
 * @property int $company_id 物流公司ID
 * @property string $title 运费模板名称
 * @property int $is_default 是否是默认模板
 * @property string $province_ids 省ID组
 * @property string $city_ids 市ID组
 * @property string $area_ids 区县ID组
 * @property int $weight_is_use 是否启用重量运费
 * @property string $weight_snum 首重
 * @property string $weight_sprice 首重运费
 * @property string $weight_xnum 续重
 * @property string $weight_xprice 续重运费
 * @property int $volume_is_use 是否启用体积计算运费
 * @property string $volume_snum 首体积量
 * @property string $volume_sprice 首体积运费
 * @property string $volume_xnum 续体积量
 * @property string $volume_xprice 续体积运费
 * @property int $bynum_is_use 是否启用计件方式运费
 * @property int $bynum_snum 首件
 * @property string $bynum_sprice 首件运费
 * @property int $bynum_xnum 续件
 * @property string $bynum_xprice 续件运费
 * @property int $status 状态[-1:删除;0:禁用;1启用]
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class Fee extends \common\models\base\BaseModel
{
    use MerchantBehavior;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_shop_express_fee}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['is_default'], 'verifyRegion'],
            [['merchant_id', 'company_id', 'is_default', 'status', 'created_at', 'updated_at'], 'integer'],
            [['province_ids', 'city_ids', 'area_ids'], 'string'],
            [['weight_is_use', 'volume_is_use', 'bynum_is_use', 'bynum_snum', 'bynum_xnum'], 'integer', 'min' => 0],
            [
                [
                    'weight_snum',
                    'weight_sprice',
                    'weight_xnum',
                    'weight_xprice',
                    'volume_snum',
                    'volume_sprice',
                    'volume_xnum',
                    'volume_xprice',
                    'bynum_sprice',
                    'bynum_xprice',
                ],
                'number',
                'min' => 0,
            ],
            [['title'], 'string', 'max' => 30],
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
            'company_id' => '快递id',
            'title' => '标题',
            'is_default' => '是否默认',
            'province_ids' => 'Province Ids',
            'city_ids' => 'City Ids',
            'area_ids' => '区域id组',
            'weight_is_use' => '是否启用重量运费',
            'weight_snum' => '首重',
            'weight_sprice' => '首重运费',
            'weight_xnum' => '续重',
            'weight_xprice' => '续重运费',
            'volume_is_use' => '是否启用体积计算运费',
            'volume_snum' => '首体积量',
            'volume_sprice' => '首体积运费',
            'volume_xnum' => '续体积量',
            'volume_xprice' => '续体积运费',
            'bynum_is_use' => '是否启用计件方式运费',
            'bynum_snum' => '首件',
            'bynum_sprice' => '首件运费',
            'bynum_xnum' => '续件',
            'bynum_xprice' => '续件运费',
            'status' => '状态',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * 验证省市区必填
     */
    public function verifyRegion($attribute)
    {
        empty($this->is_default) && $this->is_default = 0;
        if ($this->is_default != true && (!$this->province_ids || !$this->city_ids)) {
            $this->addError($attribute, '省份/城市不能为空');
        }

        if ($this->is_default == true) {
            $this->province_ids = $this->city_ids = $this->area_ids = '';
        }
    }
}
