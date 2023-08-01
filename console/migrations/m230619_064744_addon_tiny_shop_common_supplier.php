<?php

use yii\db\Migration;

class m230619_064744_addon_tiny_shop_common_supplier extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_tiny_shop_common_supplier}}', [
            'id' => "int(11) NOT NULL AUTO_INCREMENT",
            'merchant_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '商户id'",
            'name' => "varchar(50) NOT NULL DEFAULT '' COMMENT '供货商名称'",
            'describe' => "varchar(1000) NOT NULL DEFAULT '' COMMENT '供货商描述'",
            'sort' => "int(10) NULL DEFAULT '0' COMMENT '排序'",
            'province_id' => "int(10) NULL DEFAULT '0' COMMENT '省'",
            'city_id' => "int(10) NULL DEFAULT '0' COMMENT '城市'",
            'area_id' => "int(10) NULL DEFAULT '0' COMMENT '地区'",
            'address_name' => "varchar(200) NULL DEFAULT '' COMMENT '地址'",
            'address_details' => "varchar(100) NULL DEFAULT '' COMMENT '详细地址'",
            'longitude' => "varchar(100) NULL DEFAULT '' COMMENT '经度'",
            'latitude' => "varchar(100) NULL DEFAULT '' COMMENT '纬度'",
            'contacts' => "varchar(100) NULL DEFAULT '' COMMENT '联系人'",
            'mobile' => "varchar(100) NULL DEFAULT '' COMMENT '手机号码'",
            'tel_no' => "varchar(20) NULL DEFAULT '' COMMENT '电话号码'",
            'status' => "tinyint(4) NULL DEFAULT '1' COMMENT '状态'",
            'created_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '更新时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='扩展_微商城_供货商表'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_tiny_shop_common_supplier}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

