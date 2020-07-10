<?php

use yii\db\Migration;

class m200529_160730_addon_shop_marketing_coupon extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_marketing_coupon}}', [
            'id' => "int(11) NOT NULL AUTO_INCREMENT COMMENT '优惠券id'",
            'title' => "varchar(50) NOT NULL DEFAULT '' COMMENT '优惠券名称'",
            'coupon_type_id' => "int(11) unsigned NOT NULL DEFAULT '0' COMMENT '优惠券类型id'",
            'merchant_id' => "int(11) unsigned NULL DEFAULT '0' COMMENT '店铺Id'",
            'type' => "tinyint(4) unsigned NULL DEFAULT '1' COMMENT '优惠券类型 1:满减;2:折扣'",
            'code' => "varchar(100) NULL DEFAULT '' COMMENT '优惠券编码'",
            'member_id' => "int(11) NULL DEFAULT '0' COMMENT '领用人'",
            'use_order_id' => "int(11) NULL DEFAULT '0' COMMENT '优惠券使用订单id'",
            'create_order_id' => "int(11) NULL DEFAULT '0' COMMENT '创建订单id(优惠券只有是完成订单发放的优惠券时才有值)'",
            'money' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '面额'",
            'at_least' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '满多少元使用 0代表无限制'",
            'discount' => "int(10) NULL DEFAULT '100' COMMENT '折扣'",
            'state' => "tinyint(4) NULL DEFAULT '0' COMMENT '优惠券状态 0未领用 1已领用（未使用） 2已使用 3已过期'",
            'get_type' => "int(11) NULL DEFAULT '0' COMMENT '获取方式1订单2.首页领取'",
            'fetch_time' => "int(11) NULL DEFAULT '0' COMMENT '领取时间'",
            'use_time' => "int(11) NULL DEFAULT '0' COMMENT '使用时间'",
            'start_time' => "int(11) NULL DEFAULT '0' COMMENT '有效期开始时间'",
            'end_time' => "int(11) NULL DEFAULT '0' COMMENT '有效期结束时间'",
            'status' => "int(4) NULL DEFAULT '1' COMMENT '状态'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='扩展_微商城_优惠券表'");
        
        /* 索引设置 */
        $this->createIndex('coupon_type_id','{{%addon_shop_marketing_coupon}}','coupon_type_id',0);
        $this->createIndex('merchant_id','{{%addon_shop_marketing_coupon}}','merchant_id',0);
        $this->createIndex('use_order_id','{{%addon_shop_marketing_coupon}}','use_order_id',0);
        $this->createIndex('code','{{%addon_shop_marketing_coupon}}','code',0);
        $this->createIndex('state','{{%addon_shop_marketing_coupon}}','state, end_time',0);
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_marketing_coupon}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

