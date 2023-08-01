<?php

use yii\db\Migration;

class m230619_064744_addon_tiny_shop_marketing_recharge_config extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_tiny_shop_marketing_recharge_config}}', [
            'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
            'merchant_id' => "int(11) NULL DEFAULT '0' COMMENT '商户'",
            'price' => "decimal(10,2) unsigned NULL DEFAULT '0.00' COMMENT '充值金额'",
            'give_price' => "decimal(10,2) unsigned NULL DEFAULT '0.00' COMMENT '赠送金额'",
            'give_point' => "int(11) unsigned NULL DEFAULT '0' COMMENT '赠送积分'",
            'give_growth' => "int(11) unsigned NULL DEFAULT '0' COMMENT '赠送成长值'",
            'sort' => "int(5) NULL DEFAULT '0' COMMENT '排序'",
            'status' => "tinyint(4) NULL DEFAULT '1' COMMENT '状态'",
            'created_at' => "int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='扩展_微商城_充值套餐'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_tiny_shop_marketing_recharge_config}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

