<?php

use yii\db\Migration;

class m230619_064744_addon_tiny_shop_common_nav extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_tiny_shop_common_nav}}', [
            'id' => "int(11) NOT NULL AUTO_INCREMENT COMMENT '序号'",
            'merchant_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '商户id'",
            'type' => "tinyint(4) NULL DEFAULT '3' COMMENT '类型'",
            'name' => "varchar(50) NOT NULL DEFAULT '' COMMENT '标识'",
            'data' => "json NULL COMMENT '内容'",
            'status' => "tinyint(4) NULL DEFAULT '1' COMMENT '状态'",
            'created_at' => "int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COMMENT='扩展_微商城_导航'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_tiny_shop_common_nav}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

