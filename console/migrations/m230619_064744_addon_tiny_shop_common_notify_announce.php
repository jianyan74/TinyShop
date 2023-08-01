<?php

use yii\db\Migration;

class m230619_064744_addon_tiny_shop_common_notify_announce extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_tiny_shop_common_notify_announce}}', [
            'id' => "bigint(20) NOT NULL AUTO_INCREMENT COMMENT '主键'",
            'member_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '用户id'",
            'merchant_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '商户id'",
            'title' => "varchar(150) NULL DEFAULT '' COMMENT '标题'",
            'content' => "longtext NULL COMMENT '消息内容'",
            'cover' => "varchar(100) NULL DEFAULT '' COMMENT '封面'",
            'synopsis' => "varchar(255) NULL DEFAULT '' COMMENT '概要'",
            'view' => "int(10) NULL DEFAULT '0' COMMENT '浏览量'",
            'status' => "tinyint(4) NULL DEFAULT '1' COMMENT '状态[-1:删除;0:禁用;1启用]'",
            'created_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '修改时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COMMENT='系统_消息公告表'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_tiny_shop_common_notify_announce}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

