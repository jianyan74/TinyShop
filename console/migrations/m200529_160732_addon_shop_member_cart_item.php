<?php

use yii\db\Migration;

class m200529_160732_addon_shop_member_cart_item extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_member_cart_item}}', [
            'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
            'merchant_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '商户id'",
            'member_id' => "int(11) NOT NULL DEFAULT '0' COMMENT '用户编码'",
            'cart_id' => "int(11) NOT NULL DEFAULT '1' COMMENT '购物车编码'",
            'product_img' => "varchar(255) NOT NULL COMMENT '商品快照'",
            'product_name' => "varchar(255) NOT NULL COMMENT '商品名称'",
            'price' => "decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '价格'",
            'product_id' => "int(11) NOT NULL COMMENT '商品编码'",
            'supplier_id' => "int(11) NOT NULL DEFAULT '0' COMMENT '店铺编码'",
            'sku_id' => "int(10) NULL DEFAULT '0' COMMENT '商品sku编码'",
            'sku_name' => "varchar(255) NULL DEFAULT '' COMMENT '商品sku信息'",
            'number' => "int(11) NOT NULL DEFAULT '0' COMMENT '商品数量'",
            'status' => "tinyint(4) NULL DEFAULT '1' COMMENT '状态[-1:删除;0:禁用;1启用]'",
            'created_at' => "int(10) NULL DEFAULT '0'",
            'updated_at' => "int(10) NULL DEFAULT '0'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='扩展_微商城_购物车表'");
        
        /* 索引设置 */
        $this->createIndex('sku_id','{{%addon_shop_member_cart_item}}','sku_id',0);
        $this->createIndex('member_id','{{%addon_shop_member_cart_item}}','member_id',0);
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_member_cart_item}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

