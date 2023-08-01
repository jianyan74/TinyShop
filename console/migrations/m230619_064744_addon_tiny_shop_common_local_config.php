<?php

use yii\db\Migration;

class m230619_064744_addon_tiny_shop_common_local_config extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_tiny_shop_common_local_config}}', [
            'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
            'merchant_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '商户id'",
            'order_money' => "decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '订单金额'",
            'freight' => "decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '运费'",
            'distribution_time' => "json NULL COMMENT '配送时间'",
            'shipping_fee' => "json NULL COMMENT '阶梯配送费用'",
            'make_day' => "int(10) NULL DEFAULT '1' COMMENT '可预约天数'",
            'interval_time' => "int(11) NULL DEFAULT '0' COMMENT '配送间隔时间'",
            'auto_order_receiving' => "tinyint(4) NULL DEFAULT '0' COMMENT '自动接单'",
            'is_start' => "int(11) NOT NULL DEFAULT '0' COMMENT '是否是起步价'",
            'created_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '更新时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='扩展_微商城_配送费用设置'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_tiny_shop_common_local_config}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

