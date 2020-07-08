<?php

use yii\db\Migration;

class m200529_160734_addon_shop_supplier extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_supplier}}', [
            'id' => "int(11) NOT NULL AUTO_INCREMENT",
            'merchant_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '商户id'",
            'name' => "varchar(50) NOT NULL DEFAULT '' COMMENT '供货商名称'",
            'desc' => "varchar(1000) NOT NULL DEFAULT '' COMMENT '供货商描述'",
            'linkman_tel' => "varchar(255) NOT NULL DEFAULT '' COMMENT '联系人电话'",
            'linkman_name' => "varchar(50) NOT NULL DEFAULT '' COMMENT '联系人姓名'",
            'linkman_address' => "varchar(255) NOT NULL DEFAULT '' COMMENT '联系人地址'",
            'sort' => "int(10) NULL DEFAULT '0' COMMENT '排序'",
            'status' => "tinyint(4) NULL DEFAULT '1' COMMENT '状态'",
            'created_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '更新时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='扩展_微商城_供货商表'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_supplier}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

