<?php

use yii\db\Migration;

class m230619_064744_addon_tiny_shop_common_protocol extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_tiny_shop_common_protocol}}', [
            'id' => "int(11) NOT NULL AUTO_INCREMENT",
            'merchant_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '商户id'",
            'title' => "varchar(100) NULL DEFAULT '' COMMENT '协议名称'",
            'content' => "text NULL COMMENT '协议内容'",
            'name' => "varchar(50) NULL DEFAULT '' COMMENT '标识'",
            'version_id' => "bigint(20) NULL DEFAULT '0' COMMENT '版本号ID'",
            'version' => "varchar(30) NOT NULL DEFAULT '' COMMENT '新版本号'",
            'status' => "tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态(-1:已删除,0:禁用,1:正常)'",
            'created_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '修改时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_tiny_shop_common_protocol}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

