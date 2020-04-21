<?php

use yii\db\Migration;

class m200420_084203_addon_shop_order_product_virtual extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_order_product_virtual}}', [
            'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id'",
            'merchant_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '商户id'",
            'sku_id' => "int(11) NULL DEFAULT '0' COMMENT '规格id'",
            'product_id' => "int(11) NULL DEFAULT '0' COMMENT '商品id'",
            'product_name' => "varchar(255) NULL DEFAULT '' COMMENT '虚拟商品名称'",
            'product_group' => "varchar(50) NULL DEFAULT '' COMMENT '商品类型'",
            'code' => "varbinary(255) NULL COMMENT '虚拟码'",
            'money' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '虚拟商品金额'",
            'member_id' => "int(11) NULL DEFAULT '0' COMMENT '买家id'",
            'member_nickname' => "varchar(255) NULL DEFAULT '' COMMENT '买家名称'",
            'order_product_id' => "int(11) NULL DEFAULT '0' COMMENT '关联订单项id'",
            'order_sn' => "varchar(255) NULL DEFAULT '' COMMENT '订单编号'",
            'period' => "int(11) NULL DEFAULT '0' COMMENT '有效期/天(0表示不限制)'",
            'start_time' => "int(11) NULL DEFAULT '0' COMMENT '有效期开始时间'",
            'end_time' => "int(11) NULL DEFAULT '0' COMMENT '有效期结束时间'",
            'use_number' => "int(11) NULL DEFAULT '0' COMMENT '使用次数'",
            'confine_use_number' => "int(11) NULL DEFAULT '0' COMMENT '限制使用次数'",
            'remark' => "varchar(255) NULL DEFAULT '' COMMENT '备注'",
            'state' => "tinyint(1) NULL DEFAULT '0' COMMENT '使用状态(-1:已过期,0:未使用,1:已使用)'",
            'created_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '更新时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='虚拟商品列表(用户下单支成功后存放)'");
        
        /* 索引设置 */
        $this->createIndex('code','{{%addon_shop_order_product_virtual}}','code',0);
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_order_product_virtual}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

