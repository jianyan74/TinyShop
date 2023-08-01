<?php

namespace addons\TinyShop\common\models\common;

use common\traits\Tree;
use common\behaviors\MerchantBehavior;

/**
 * This is the model class for table "{{%addon_tiny_shop_common_helper}}".
 *
 * @property int $id 主键
 * @property int|null $member_id 创建者id
 * @property int|null $merchant_id 商户id
 * @property string $title 标题
 * @property string|null $content 内容管理
 * @property int|null $sort 排序
 * @property int|null $level 级别
 * @property int|null $pid 上级id
 * @property string $tree 树
 * @property int|null $view 浏览量
 * @property int|null $status 状态
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class Helper extends \common\models\base\BaseModel
{
    use MerchantBehavior, Tree;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_common_helper}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['member_id', 'merchant_id', 'sort', 'level', 'pid', 'view', 'status', 'created_at', 'updated_at'], 'integer'],
            [['content'], 'string'],
            [['title'], 'string', 'max' => 50],
            [['tree'], 'string', 'max' => 500],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键',
            'member_id' => '创建者id',
            'merchant_id' => '商户id',
            'title' => '标题',
            'content' => '内容管理',
            'sort' => '排序',
            'level' => '级别',
            'pid' => '上级站点',
            'tree' => '树',
            'view' => '浏览量',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
