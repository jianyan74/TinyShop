<?php

namespace addons\TinyShop\common\models\common;

use common\behaviors\MerchantBehavior;
use Yii;

/**
 * This is the model class for table "{{%addon_tiny_shop_common_express_fee}}".
 *
 * @property int $id 运费模板ID
 * @property int|null $merchant_id 商户id
 * @property int $company_id 物流公司ID
 * @property string $title 运费模板名称
 * @property string|null $province_ids 省ID组
 * @property string|null $city_ids 市ID组
 * @property string|null $area_ids 区县ID组
 * @property int $weight_is_use 是否启用重量运费
 * @property float $weight_snum 首重
 * @property float $weight_sprice 首重运费
 * @property float $weight_xnum 续重
 * @property float $weight_xprice 续重运费
 * @property int $volume_is_use 是否启用体积计算运费
 * @property float $volume_snum 首体积量
 * @property float $volume_sprice 首体积运费
 * @property float $volume_xnum 续体积量
 * @property float $volume_xprice 续体积运费
 * @property int $bynum_is_use 是否启用计件方式运费
 * @property int $bynum_snum 首件
 * @property float $bynum_sprice 首件运费
 * @property int $bynum_xnum 续件
 * @property float $bynum_xprice 续件运费
 * @property int $is_default 是否是默认模板
 * @property int|null $status 状态[-1:删除;0:禁用;1启用]
 * @property int|null $created_at 创建时间
 * @property int|null $updated_at 更新时间
 */
class ExpressFee extends \common\models\base\BaseModel
{
    use MerchantBehavior;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_common_express_fee}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['is_default'], 'verifyRegion'],
            [
                [
                    'merchant_id',
                    'company_id',
                    'weight_is_use',
                    'volume_is_use',
                    'bynum_is_use',
                    'bynum_snum',
                    'bynum_xnum',
                    'is_default',
                    'status',
                    'created_at',
                    'updated_at'
                ],
                'integer',
                'min' => 0
            ],
            [['province_ids', 'city_ids', 'area_ids'], 'safe'],
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
                    'bynum_xprice'
                ],
                'number',
                'min' => 0
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
            'id' => '运费模板ID',
            'merchant_id' => '商户id',
            'company_id' => '物流公司ID',
            'title' => '运费模板名称',
            'province_ids' => '省ID组',
            'city_ids' => '市ID组',
            'area_ids' => '区县ID组',
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
            'is_default' => '是否是默认模板',
            'status' => '状态[-1:删除;0:禁用;1启用]',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
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
