<?php

namespace addons\TinyShop\common\models\common;

use common\behaviors\MerchantStoreBehavior;

/**
 * This is the model class for table "rf_addon_tiny_shop_common_search_history".
 *
 * @property int $id
 * @property int|null $merchant_id 商户id
 * @property int|null $member_id 用户id
 * @property int|null $store_id 店铺ID
 * @property string|null $keyword 关键字
 * @property int|null $num 搜索次数
 * @property string|null $search_date 搜索日期
 * @property string|null $ip ip地址
 * @property string|null $req_id 对外id
 * @property int $status 状态[-1:删除;0:禁用;1启用]
 * @property int|null $created_at 创建时间
 * @property int|null $updated_at 修改时间
 */
class SearchHistory extends \common\models\base\BaseModel
{
    use MerchantStoreBehavior;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_common_search_history}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'member_id', 'store_id', 'num', 'status', 'created_at', 'updated_at'], 'integer'],
            [['search_date'], 'safe'],
            [['keyword'], 'string', 'max' => 200],
            [['ip', 'req_id'], 'string', 'max' => 50],
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
            'member_id' => '用户id',
            'store_id' => '店铺ID',
            'keyword' => '关键字',
            'num' => '搜索次数',
            'search_date' => '搜索日期',
            'ip' => 'ip地址',
            'req_id' => '对外id',
            'status' => '状态[-1:删除;0:禁用;1启用]',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }
}
