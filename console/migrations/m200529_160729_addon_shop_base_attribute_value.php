<?php

use yii\db\Migration;

class m200529_160729_addon_shop_base_attribute_value extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_base_attribute_value}}', [
            'id' => "int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '属性值ID'",
            'merchant_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '商户id'",
            'attribute_id' => "int(11) NOT NULL COMMENT '属性ID'",
            'title' => "varchar(50) NOT NULL DEFAULT '' COMMENT '属性值名称'",
            'value' => "varchar(1000) NOT NULL DEFAULT '' COMMENT '属性对应相关数据'",
            'type' => "int(11) NOT NULL DEFAULT '1' COMMENT '属性对应输入类型1.直接2.单选3.多选'",
            'sort' => "int(11) NOT NULL DEFAULT '999' COMMENT '排序号'",
            'status' => "tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态(-1:已删除,0:禁用,1:正常)'",
            'created_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '修改时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='扩展_微商城_商品属性值'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_base_attribute_value}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

