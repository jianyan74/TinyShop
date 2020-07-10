<?php

namespace addons\TinyShop\common\models\common;

use common\behaviors\MerchantBehavior;

/**
 * This is the model class for table "{{%addon_shop_common_search_history}}".
 *
 * @property int $id
 * @property int $merchant_id 商户id
 * @property int $member_id 用户id
 * @property string $req_id 对外id
 * @property string $keyword 关键字
 * @property int $num 搜索次数
 * @property string $search_date 搜索日期
 * @property string $ip ip地址
 * @property int $status 状态[-1:删除;0:禁用;1启用]
 * @property int $created_at 创建时间
 * @property int $updated_at 修改时间
 */
class SearchHistory extends \common\models\base\BaseModel
{
    use MerchantBehavior;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_shop_common_search_history}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'member_id', 'num', 'status', 'created_at', 'updated_at'], 'integer'],
            [['search_date'], 'safe'],
            [['req_id', 'ip'], 'string', 'max' => 50],
            [['keyword'], 'string', 'max' => 255],
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
            'req_id' => '对外id',
            'keyword' => '关键字',
            'num' => '搜索次数',
            'search_date' => '搜索日期',
            'ip' => 'ip地址',
            'status' => '状态[-1:删除;0:禁用;1启用]',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }
}
