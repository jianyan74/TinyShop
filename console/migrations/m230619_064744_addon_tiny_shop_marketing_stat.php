<?php

use yii\db\Migration;

class m230619_064744_addon_tiny_shop_marketing_stat extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_tiny_shop_marketing_stat}}', [
            'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键'",
            'merchant_id' => "int(11) NULL DEFAULT '1' COMMENT '店铺ID'",
            'marketing_id' => "int(11) unsigned NULL DEFAULT '0' COMMENT '对应活动'",
            'marketing_type' => "varchar(60) NULL DEFAULT '' COMMENT '活动类型'",
            'total_customer_num' => "int(10) NULL DEFAULT '0' COMMENT '总客户数量'",
            'new_customer_num' => "int(10) NULL DEFAULT '0' COMMENT '新客户数量'",
            'old_customer_num' => "int(10) NULL DEFAULT '0' COMMENT '老客户数量'",
            'pay_money' => "int(10) NULL DEFAULT '0' COMMENT '订单实付金额'",
            'order_count' => "int(10) NULL DEFAULT '0' COMMENT '订单数量'",
            'product_count' => "int(10) NULL DEFAULT '0' COMMENT '订单产品数量'",
            'discount_money' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '优惠总金额'",
            'status' => "tinyint(4) NULL DEFAULT '1' COMMENT '状态'",
            'created_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '修改时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='扩展_微商城_营销统计'");
        
        /* 索引设置 */
        $this->createIndex('marketing_id','{{%addon_tiny_shop_marketing_stat}}','marketing_id, marketing_type',0);
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_tiny_shop_marketing_stat}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

