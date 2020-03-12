<?php

use yii\db\Migration;

class m191009_090109_addon_shop_pickup_point extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_pickup_point}}', [
            'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
            'merchant_id' => "int(11) NULL DEFAULT '0' COMMENT '店铺ID'",
            'name' => "varchar(150) NOT NULL COMMENT '自提点名称'",
            'address' => "varchar(200) NOT NULL DEFAULT '' COMMENT '自提点地址'",
            'contact' => "varchar(100) NULL DEFAULT '' COMMENT '联系人'",
            'mobile' => "varchar(50) NOT NULL DEFAULT '' COMMENT '联系电话'",
            'city_id' => "int(11) NOT NULL COMMENT '市ID'",
            'province_id' => "int(11) NOT NULL COMMENT '省ID'",
            'address_name' => "varchar(200) NULL DEFAULT '' COMMENT '地址解析'",
            'area_id' => "int(11) NOT NULL COMMENT '区县ID'",
            'sort' => "int(10) NULL DEFAULT '999' COMMENT '排序'",
            'lng' => "varchar(50) NULL DEFAULT '' COMMENT '经度'",
            'lat' => "varchar(50) NULL DEFAULT '' COMMENT '维度'",
            'status' => "tinyint(4) NULL DEFAULT '1' COMMENT '状态'",
            'created_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '更新时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AVG_ROW_LENGTH=4096 ROW_FORMAT=DYNAMIC COMMENT='自提点管理'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        $this->insert('{{%addon_shop_pickup_point}}',['id'=>'2','merchant_id'=>'1','name'=>'123','address'=>'某某区12什么鬼的说','contact'=>'gg','mobile'=>'123','city_id'=>'120100','province_id'=>'120000','address_name'=>'天津市 市辖区 南开区 ','area_id'=>'120104','sort'=>'1','lng'=>'121.522731','lat'=>'29.850568','status'=>'1','created_at'=>'1561008278','updated_at'=>'1563517915']);
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_pickup_point}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

