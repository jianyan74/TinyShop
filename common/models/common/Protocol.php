<?php

namespace addons\TinyShop\common\models\common;

use common\helpers\StringHelper;
use Yii;

/**
 * This is the model class for table "{{%addon_tiny_shop_common_protocol}}".
 *
 * @property int $id
 * @property int|null $merchant_id 商户id
 * @property string|null $title 协议名称
 * @property string|null $content 协议内容
 * @property string|null $name 标识
 * @property string|null $version 版本号
 * @property int|null $version_id 版本ID
 * @property int $status 状态(-1:已删除,0:禁用,1:正常)
 * @property int|null $created_at 创建时间
 * @property int|null $updated_at 修改时间
 */
class Protocol extends \common\models\base\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_common_protocol}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'version_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['content'], 'string'],
            [['content', 'title'], 'required'],
            [['title'], 'string', 'max' => 100],
            [['name'], 'string', 'max' => 50],
            [['version'], 'string', 'max' => 50],
            [['version'], 'processingVersion'],
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
            'title' => '协议名称',
            'content' => '协议内容',
            'name' => '标识',
            'version' => '版本号',
            'version_id' => '版本ID',
            'status' => '状态(-1:已删除,0:禁用,1:正常)',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }


    /**
     * @param $attribute
     */
    public function processingVersion($attribute)
    {
        $version_id = StringHelper::strToInt($this->version);
        if ($version_id === false) {
            $this->addError($attribute, '版本号格式不符');
            return;
        }

        $this->version_id = $version_id;
    }
}
