<?php

use yii\db\Migration;

class m200420_084203_addon_shop_order_product_virtual_verification extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_order_product_virtual_verification}}', [
            'id' => "int(11) NOT NULL AUTO_INCREMENT",
            'merchant_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '商户id'",
            'member_id' => "int(11) NOT NULL DEFAULT '0' COMMENT '商品所有人'",
            'merchant_name' => "varchar(50) NULL DEFAULT '' COMMENT '虚拟商品所有者'",
            'product_virtual_id' => "int(11) NOT NULL DEFAULT '0' COMMENT '用户虚拟商品id'",
            'product_virtual_state' => "int(11) NULL DEFAULT '0' COMMENT '用户虚拟商品使用状态'",
            'action' => "varchar(255) NULL DEFAULT '' COMMENT '动作内容'",
            'num' => "int(11) NULL DEFAULT '0' COMMENT '核销次数'",
            'product_id' => "int(11) NOT NULL DEFAULT '0' COMMENT '商品id'",
            'product_name' => "varchar(50) NOT NULL DEFAULT '' COMMENT '虚拟商品名称'",
            'auditor_id' => "int(11) NOT NULL DEFAULT '0' COMMENT '核销人员id'",
            'auditor_name' => "varchar(50) NULL DEFAULT '' COMMENT '核销员'",
            'created_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '修改时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='虚拟商品核销记录表'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_order_product_virtual_verification}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

