<?php

use yii\db\Migration;

class m230619_064744_addon_tiny_shop_marketing_point_config extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_tiny_shop_marketing_point_config}}', [
            'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键'",
            'merchant_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '商户id'",
            'convert_rate' => "decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '1积分对应金额'",
            'min_order_money' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '订单金额门槛'",
            'deduction_type' => "tinyint(4) NULL DEFAULT '0' COMMENT '抵现金额上限'",
            'max_deduction_money' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '每笔订单最多抵扣金额'",
            'max_deduction_rate' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '每笔订单最多抵扣比率'",
            'explain' => "text NULL COMMENT '积分说明'",
            'status' => "tinyint(4) NULL DEFAULT '0' COMMENT '状态[-1:删除;0:禁用;1启用]'",
            'created_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '添加时间'",
            'updated_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '修改时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='扩展_微商城_积分设置表'");
        
        /* 索引设置 */
        $this->createIndex('merchant_id','{{%addon_tiny_shop_marketing_point_config}}','merchant_id',0);
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_tiny_shop_marketing_point_config}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

