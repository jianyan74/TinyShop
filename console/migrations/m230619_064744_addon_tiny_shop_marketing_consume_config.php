<?php

use yii\db\Migration;

class m230619_064744_addon_tiny_shop_marketing_consume_config extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_tiny_shop_marketing_consume_config}}', [
            'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键'",
            'merchant_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '商户id'",
            'store_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '门店id'",
            'give_event' => "tinyint(4) NULL DEFAULT '1' COMMENT '赠送事件'",
            'point_give_type' => "tinyint(4) NULL DEFAULT '0' COMMENT '积分赠送类型'",
            'give_point' => "decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '赠送积分'",
            'growth_give_type' => "tinyint(4) NULL DEFAULT '0' COMMENT '成长值赠送类型'",
            'give_growth' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '赠送成长值'",
            'is_refund' => "tinyint(4) NULL DEFAULT '0' COMMENT '是否退回[1:退回;0:不退回]'",
            'status' => "tinyint(4) NULL DEFAULT '0' COMMENT '状态[-1:删除;0:禁用;1启用]'",
            'created_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '添加时间'",
            'updated_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '修改时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='扩展_商城_消费奖励'");
        
        /* 索引设置 */
        $this->createIndex('merchant_id','{{%addon_tiny_shop_marketing_consume_config}}','merchant_id',0);
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_tiny_shop_marketing_consume_config}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

