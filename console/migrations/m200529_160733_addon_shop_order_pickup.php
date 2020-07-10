<?php

use yii\db\Migration;

class m200529_160733_addon_shop_order_pickup extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_order_pickup}}', [
            'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
            'order_id' => "int(10) NULL DEFAULT '0' COMMENT '订单ID'",
            'merchant_id' => "int(11) NULL DEFAULT '0' COMMENT '商户id'",
            'member_id' => "int(10) NULL DEFAULT '0' COMMENT '用户id'",
            'name' => "varchar(150) NOT NULL COMMENT '自提点名称'",
            'address' => "varchar(200) NOT NULL DEFAULT '' COMMENT '自提点地址'",
            'contact' => "varchar(100) NULL DEFAULT '' COMMENT '联系人'",
            'mobile' => "varchar(50) NOT NULL DEFAULT '' COMMENT '联系电话'",
            'city_id' => "int(11) NOT NULL COMMENT '市ID'",
            'province_id' => "int(11) NOT NULL COMMENT '省ID'",
            'area_id' => "int(11) NOT NULL COMMENT '区县ID'",
            'lng' => "varchar(50) NULL DEFAULT '' COMMENT '经度'",
            'lat' => "varchar(50) NULL DEFAULT '' COMMENT '维度'",
            'buyer_name' => "varchar(50) NULL DEFAULT '' COMMENT '提货人姓名'",
            'buyer_mobile' => "varchar(50) NULL DEFAULT '' COMMENT '提货人电话'",
            'remark' => "varchar(200) NULL DEFAULT '' COMMENT '提货备注信息'",
            'pickup_code' => "varchar(50) NULL DEFAULT '' COMMENT '自提码'",
            'pickup_time' => "int(11) NULL DEFAULT '0' COMMENT '自提时间'",
            'pickup_status' => "int(1) NULL DEFAULT '0' COMMENT '自提状态 0未自提 1已提货'",
            'pickup_id' => "int(11) NOT NULL DEFAULT '0' COMMENT '自提点门店id'",
            'auditor_id' => "int(11) NULL DEFAULT '0' COMMENT '审核人id'",
            'status' => "tinyint(4) NULL DEFAULT '1' COMMENT '状态'",
            'created_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '更新时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='扩展_微商城_订单自提点管理'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_order_pickup}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

