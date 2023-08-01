<?php

use yii\db\Migration;

class m230619_064744_addon_tiny_shop_marketing_combination extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_tiny_shop_marketing_combination}}', [
            'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id'",
            'merchant_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '商户id'",
            'title' => "varchar(100) NULL DEFAULT '' COMMENT '组合套餐名称'",
            'price' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '组合套餐价格'",
            'original_price' => "decimal(19,2) NULL DEFAULT '0.00' COMMENT '原价,仅作参考商品原价所取为sku列表中最低价'",
            'save_the_price' => "decimal(19,2) NULL DEFAULT '0.00' COMMENT '节省价,仅作参考不参与实际计算'",
            'refusal_cause' => "varchar(200) NULL DEFAULT '' COMMENT '拒绝原因'",
            'start_time' => "int(11) unsigned NULL DEFAULT '0' COMMENT '开始时间'",
            'end_time' => "int(11) unsigned NULL DEFAULT '0' COMMENT '结束时间'",
            'audit_status' => "tinyint(4) NOT NULL DEFAULT '1' COMMENT '审核状态[0:申请;1通过;-1失败]'",
            'audit_time' => "int(10) unsigned NULL DEFAULT '0' COMMENT '审核时间'",
            'status' => "tinyint(4) NULL DEFAULT '1' COMMENT '状态'",
            'created_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '更新时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='组合套餐促销'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_tiny_shop_marketing_combination}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

