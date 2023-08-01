<?php

use yii\db\Migration;

class m230619_064744_addon_tiny_shop_common_product_service_map extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_tiny_shop_common_product_service_map}}', [
            'id' => "int(11) NOT NULL AUTO_INCREMENT",
            'merchant_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '商户id'",
            'service_id' => "int(11) NULL COMMENT '服务ID'",
            'refusal_cause' => "varchar(255) NULL DEFAULT '' COMMENT '拒绝原因'",
            'audit_time' => "int(10) NULL DEFAULT '0' COMMENT '审核时间'",
            'audit_status' => "tinyint(4) NULL DEFAULT '0' COMMENT '审核状态'",
            'status' => "tinyint(4) NULL DEFAULT '1' COMMENT '状态'",
            'created_at' => "int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4");
        
        /* 索引设置 */
        $this->createIndex('merchant_id','{{%addon_tiny_shop_common_product_service_map}}','merchant_id',0);
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_tiny_shop_common_product_service_map}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

