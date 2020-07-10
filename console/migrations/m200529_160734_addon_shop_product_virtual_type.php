<?php

use yii\db\Migration;

class m200529_160734_addon_shop_product_virtual_type extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_product_virtual_type}}', [
            'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '虚拟商品类型id'",
            'merchant_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '商户id'",
            'product_id' => "int(11) NULL DEFAULT '0' COMMENT '关联商品id'",
            'group' => "varchar(100) NULL DEFAULT '0' COMMENT '关联虚拟商品组别'",
            'period' => "int(11) NULL DEFAULT '0' COMMENT '有效期/天(0表示不限制)'",
            'confine_use_number' => "int(11) NULL DEFAULT '0' COMMENT '限制使用次数'",
            'value' => "json NULL COMMENT '值详情'",
            'created_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '更新时间'",
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
        $this->dropTable('{{%addon_shop_product_virtual_type}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

