<?php

use yii\db\Migration;

class m230619_064744_addon_tiny_shop_common_merchant_address extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_tiny_shop_common_merchant_address}}', [
            'id' => "int(11) NOT NULL AUTO_INCREMENT",
            'merchant_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '商户id'",
            'store_id' => "int(11) NULL DEFAULT '0' COMMENT '门店id'",
            'province_id' => "int(10) NULL DEFAULT '0' COMMENT '省'",
            'city_id' => "int(10) NULL DEFAULT '0' COMMENT '城市'",
            'area_id' => "int(10) NULL DEFAULT '0' COMMENT '地区'",
            'address_name' => "varchar(200) NULL DEFAULT '' COMMENT '地址'",
            'address_details' => "varchar(100) NULL DEFAULT '' COMMENT '详细地址'",
            'sort' => "int(10) NULL DEFAULT '999' COMMENT '排序'",
            'longitude' => "varchar(100) NULL DEFAULT '' COMMENT '经度'",
            'latitude' => "varchar(100) NULL DEFAULT '' COMMENT '纬度'",
            'contacts' => "varchar(100) NULL DEFAULT '' COMMENT '联系人'",
            'mobile' => "varchar(100) NULL DEFAULT '' COMMENT '手机号码'",
            'tel_no' => "varchar(20) NULL DEFAULT '' COMMENT '电话号码'",
            'type' => "tinyint(4) NULL DEFAULT '0' COMMENT '地址类型'",
            'status' => "tinyint(4) NULL DEFAULT '1' COMMENT '状态[-1:删除;0:禁用;1启用]'",
            'created_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '更新时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='扩展_微商城_公共_退货地址'");
        
        /* 索引设置 */
        $this->createIndex('merchant_id','{{%addon_tiny_shop_common_merchant_address}}','merchant_id',0);
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_tiny_shop_common_merchant_address}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

