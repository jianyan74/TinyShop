<?php

use yii\db\Migration;

class m230619_064744_addon_tiny_shop_product_sku extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_tiny_shop_product_sku}}', [
            'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
            'merchant_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '商户id'",
            'product_id' => "int(11) unsigned NULL DEFAULT '0' COMMENT '商品编码'",
            'name' => "varchar(255) NULL DEFAULT '' COMMENT 'sku名称'",
            'picture' => "varchar(255) NULL DEFAULT '' COMMENT '商品主图'",
            'price' => "decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '价格'",
            'market_price' => "decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '市场价格'",
            'cost_price' => "decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '成本价'",
            'stock' => "int(11) NOT NULL DEFAULT '0' COMMENT '库存'",
            'sku_no' => "varchar(100) NULL DEFAULT '' COMMENT '商品编码'",
            'barcode' => "varchar(100) NULL DEFAULT '' COMMENT '商品条码'",
            'weight' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '商品重量'",
            'volume' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '商品体积'",
            'sort' => "int(11) NULL DEFAULT '1999' COMMENT '排序'",
            'data' => "varchar(500) NULL DEFAULT '' COMMENT 'sku串'",
            'status' => "tinyint(4) NULL DEFAULT '1' COMMENT '状态[-1:删除;0:禁用;1启用]'",
            'created_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '更新时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='扩展_微商城_商品_sku表'");
        
        /* 索引设置 */
        $this->createIndex('product_id','{{%addon_tiny_shop_product_sku}}','product_id',0);
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_tiny_shop_product_sku}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

