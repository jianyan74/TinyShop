<?php

use yii\db\Migration;

class m230619_064744_addon_tiny_shop_common_spec_template extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_tiny_shop_common_spec_template}}', [
            'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
            'merchant_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '商户id'",
            'title' => "varchar(50) NOT NULL COMMENT '规格名称'",
            'spec_ids' => "json NOT NULL COMMENT '关联规格'",
            'sort' => "int(11) NOT NULL DEFAULT '999' COMMENT '排列次序'",
            'status' => "tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态(-1:已删除,0:禁用,1:正常)'",
            'created_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '修改时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='扩展_微商城_公用_规格表'");
        
        /* 索引设置 */
        $this->createIndex('title','{{%addon_tiny_shop_common_spec_template}}','title',0);
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_tiny_shop_common_spec_template}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

