<?php

use yii\db\Migration;

class m230619_064744_addon_tiny_shop_order_invoice extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_tiny_shop_order_invoice}}', [
            'id' => "int(11) unsigned NOT NULL AUTO_INCREMENT",
            'merchant_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '商户id'",
            'store_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '店铺ID'",
            'member_id' => "int(11) unsigned NULL DEFAULT '0' COMMENT '用户ID'",
            'order_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '订单ID'",
            'order_sn' => "varchar(30) NULL DEFAULT '' COMMENT '订单编号'",
            'title' => "varchar(200) NULL DEFAULT '' COMMENT '公司抬头'",
            'duty_paragraph' => "varchar(200) NULL DEFAULT '' COMMENT '公司税号'",
            'opening_bank' => "varchar(200) NULL DEFAULT '' COMMENT '公司开户行'",
            'opening_bank_account' => "varchar(100) NULL DEFAULT '' COMMENT '公司开户行账号'",
            'address' => "varchar(255) NULL DEFAULT '' COMMENT '公司地址'",
            'phone' => "varchar(50) NULL DEFAULT '' COMMENT '公司电话'",
            'remark' => "varchar(255) NULL DEFAULT '' COMMENT '备注'",
            'explain' => "varchar(255) NULL DEFAULT '' COMMENT '说明'",
            'type' => "tinyint(4) NULL DEFAULT '1' COMMENT '类型 1企业 2个人'",
            'tax_money' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '税费'",
            'audit_status' => "tinyint(4) NULL DEFAULT '0' COMMENT '审核状态'",
            'audit_time' => "int(11) unsigned NULL DEFAULT '0' COMMENT '审核时间'",
            'status' => "tinyint(4) NULL DEFAULT '1' COMMENT '状态'",
            'created_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '修改时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='扩展_微商城_订单_发票'");
        
        /* 索引设置 */
        $this->createIndex('order_id','{{%addon_tiny_shop_order_invoice}}','order_id',0);
        $this->createIndex('member_id','{{%addon_tiny_shop_order_invoice}}','member_id',0);
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_tiny_shop_order_invoice}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

