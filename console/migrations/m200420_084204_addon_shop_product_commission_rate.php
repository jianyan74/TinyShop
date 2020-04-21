<?php

use yii\db\Migration;

class m200420_084204_addon_shop_product_commission_rate extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_product_commission_rate}}', [
            'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
            'merchant_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '商户id'",
            'product_id' => "int(11) NOT NULL COMMENT '商品ID'",
            'distribution_commission_rate' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '分销佣金比率'",
            'regionagent_commission_rate' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '区域代理分红佣金比率'",
            'status' => "tinyint(1) NULL DEFAULT '0' COMMENT '是否启用分销'",
            'created_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '修改时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='商品佣金比率设置'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_product_commission_rate}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

