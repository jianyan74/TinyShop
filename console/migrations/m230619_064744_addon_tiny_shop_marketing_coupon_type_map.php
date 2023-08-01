<?php

use yii\db\Migration;

class m230619_064744_addon_tiny_shop_marketing_coupon_type_map extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_tiny_shop_marketing_coupon_type_map}}', [
            'id' => "int(11) NOT NULL AUTO_INCREMENT",
            'merchant_id' => "int(10) NULL DEFAULT '0' COMMENT '商户ID'",
            'coupon_type_id' => "int(11) NOT NULL COMMENT '优惠券类型id'",
            'marketing_id' => "int(11) unsigned NULL DEFAULT '0' COMMENT '对应活动'",
            'marketing_type' => "varchar(60) NULL DEFAULT '' COMMENT '活动类型'",
            'number' => "int(11) NULL DEFAULT '1' COMMENT '数量'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='扩展_微商城_会员注册赠送优惠券'");
        
        /* 索引设置 */
        $this->createIndex('marketing_id','{{%addon_tiny_shop_marketing_coupon_type_map}}','marketing_id, marketing_type',0);
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_tiny_shop_marketing_coupon_type_map}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

