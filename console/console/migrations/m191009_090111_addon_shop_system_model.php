<?php

use yii\db\Migration;

class m191009_090111_addon_shop_system_model extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_system_model}}', [
            'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '商品属性ID'",
            'merchant_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '商户id'",
            'name' => "varchar(50) NOT NULL DEFAULT '' COMMENT '属性名称'",
            'attribute_ids' => "varchar(200) NOT NULL DEFAULT '' COMMENT '关联规格'",
            'sort' => "int(11) NULL DEFAULT '999' COMMENT '排序'",
            'status' => "tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态(-1:已删除,0:禁用,1:正常)'",
            'created_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '修改时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AVG_ROW_LENGTH=16384 ROW_FORMAT=DYNAMIC COMMENT='商品相关属性'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_system_model}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

