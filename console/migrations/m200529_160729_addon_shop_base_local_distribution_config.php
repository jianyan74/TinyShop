<?php

use yii\db\Migration;

class m200529_160729_addon_shop_base_local_distribution_config extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_base_local_distribution_config}}', [
            'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
            'merchant_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '商户id'",
            'order_money' => "decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '订单金额'",
            'freight' => "decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '运费'",
            'forenoon_start' => "int(10) NULL DEFAULT '32400' COMMENT '上午开始时间'",
            'forenoon_end' => "int(10) NULL DEFAULT '43200' COMMENT '上午结束时间'",
            'afternoon_start' => "int(10) NULL DEFAULT '46800' COMMENT '下午开始时间'",
            'afternoon_end' => "int(10) NULL DEFAULT '68400' COMMENT '下午结束时间'",
            'is_start' => "int(11) NOT NULL DEFAULT '0' COMMENT '是否是起步价'",
            'created_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '更新时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='配送费用设置'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_base_local_distribution_config}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

