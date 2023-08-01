<?php

use yii\db\Migration;

class m230619_064744_addon_tiny_shop_common_search_history extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_tiny_shop_common_search_history}}', [
            'id' => "int(11) NOT NULL AUTO_INCREMENT",
            'merchant_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '商户id'",
            'member_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '用户id'",
            'store_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '店铺ID'",
            'keyword' => "varchar(200) NULL DEFAULT '' COMMENT '关键字'",
            'num' => "int(11) NULL DEFAULT '0' COMMENT '搜索次数'",
            'search_date' => "date NULL COMMENT '搜索日期'",
            'ip' => "varchar(50) NULL DEFAULT '' COMMENT 'ip地址'",
            'req_id' => "varchar(50) NULL DEFAULT '' COMMENT '对外id'",
            'status' => "tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态[-1:删除;0:禁用;1启用]'",
            'created_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '修改时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4");
        
        /* 索引设置 */
        $this->createIndex('keyword','{{%addon_tiny_shop_common_search_history}}','keyword',0);
        $this->createIndex('member_id','{{%addon_tiny_shop_common_search_history}}','member_id',0);
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_tiny_shop_common_search_history}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

