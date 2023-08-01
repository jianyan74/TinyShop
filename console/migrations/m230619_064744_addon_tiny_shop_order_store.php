<?php

use yii\db\Migration;

class m230619_064744_addon_tiny_shop_order_store extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_tiny_shop_order_store}}', [
            'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
            'merchant_id' => "int(11) NULL DEFAULT '0' COMMENT '店铺ID'",
            'member_id' => "int(10) NULL DEFAULT '0' COMMENT '用户id'",
            'order_id' => "int(10) NULL DEFAULT '0' COMMENT '订单ID'",
            'title' => "varchar(150) NULL COMMENT '自提点名称'",
            'cover' => "varchar(255) NULL DEFAULT '' COMMENT '封面'",
            'contacts' => "varchar(100) NULL DEFAULT '' COMMENT '联系人'",
            'mobile' => "varchar(50) NULL DEFAULT '' COMMENT '联系电话'",
            'tel_no' => "varchar(20) NULL DEFAULT '' COMMENT '电话号码'",
            'province_id' => "int(11) NULL COMMENT '省ID'",
            'city_id' => "int(11) NULL COMMENT '市ID'",
            'area_id' => "int(11) NULL COMMENT '区县ID'",
            'address_name' => "varchar(200) NULL DEFAULT '' COMMENT '地址'",
            'address_details' => "varchar(100) NULL DEFAULT '' COMMENT '详细地址'",
            'longitude' => "varchar(100) NULL DEFAULT '' COMMENT '经度'",
            'latitude' => "varchar(100) NULL DEFAULT '' COMMENT '纬度'",
            'buyer_name' => "varchar(50) NULL DEFAULT '' COMMENT '提货人姓名'",
            'buyer_mobile' => "varchar(30) NULL DEFAULT '' COMMENT '提货人电话'",
            'remark' => "varchar(200) NULL DEFAULT '' COMMENT '提货备注信息'",
            'pickup_code' => "varchar(50) NULL DEFAULT '' COMMENT '自提码'",
            'pickup_time' => "int(11) NULL DEFAULT '0' COMMENT '自提时间'",
            'pickup_status' => "tinyint(4) NULL DEFAULT '0' COMMENT '自提状态 0未自提 1已提货'",
            'store_id' => "int(11) NULL DEFAULT '0' COMMENT '自提点门店id'",
            'auditor_id' => "int(11) NULL DEFAULT '0' COMMENT '审核人id'",
            'status' => "tinyint(4) NULL DEFAULT '1' COMMENT '状态'",
            'updated_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '更新时间'",
            'created_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='扩展_微商城_订单_门店'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_tiny_shop_order_store}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

