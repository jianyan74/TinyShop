<?php

use yii\db\Migration;

class m200529_160732_addon_shop_order_invoice extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_order_invoice}}', [
            'id' => "int(11) unsigned NOT NULL AUTO_INCREMENT",
            'merchant_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '商户id'",
            'order_id' => "int(11) NULL DEFAULT '0' COMMENT '订单id'",
            'order_sn' => "varchar(100) NOT NULL DEFAULT '' COMMENT '订单编号'",
            'member_id' => "int(11) unsigned NULL DEFAULT '0' COMMENT '用户id'",
            'user_name' => "varchar(50) NULL DEFAULT '' COMMENT '买家会员名称'",
            'title' => "varchar(200) NULL DEFAULT '' COMMENT '公司抬头'",
            'duty_paragraph' => "varchar(200) NULL DEFAULT '' COMMENT '税号'",
            'opening_bank' => "varchar(255) NULL DEFAULT '' COMMENT '开户行'",
            'address' => "varchar(255) NULL DEFAULT '' COMMENT '地址及电话'",
            'content' => "varchar(500) NULL DEFAULT '' COMMENT '内容'",
            'tax_money' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '金额'",
            'type' => "tinyint(4) NULL DEFAULT '1' COMMENT '类型 1企业 2个人'",
            'status' => "tinyint(4) NULL DEFAULT '1' COMMENT '状态(-1:已删除,0:禁用,1:正常)'",
            'created_at' => "int(10) unsigned NULL COMMENT '创建时间'",
            'updated_at' => "int(10) unsigned NULL COMMENT '修改时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_order_invoice}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

