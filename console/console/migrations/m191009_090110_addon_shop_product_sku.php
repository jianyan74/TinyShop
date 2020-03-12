<?php

use yii\db\Migration;

class m191009_090110_addon_shop_product_sku extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_product_sku}}', [
            'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
            'merchant_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '商户id'",
            'product_id' => "int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商品编码'",
            'name' => "varchar(600) NOT NULL DEFAULT '' COMMENT 'sku名称'",
            'img' => "varchar(200) NULL DEFAULT '' COMMENT '主图'",
            'price' => "decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '价格'",
            'market_price' => "decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '市场价格'",
            'cost_price' => "decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '成本价'",
            'stock' => "int(11) NOT NULL DEFAULT '0' COMMENT '库存'",
            'code' => "varchar(100) NULL DEFAULT '' COMMENT '商品编码'",
            'barcode' => "varchar(100) NULL DEFAULT '' COMMENT '商品条形码'",
            'sort' => "int(11) NOT NULL DEFAULT '1999' COMMENT '排序'",
            'data' => "varchar(300) NULL DEFAULT '' COMMENT 'sku串'",
            'status' => "tinyint(4) NULL DEFAULT '1' COMMENT '状态[-1:删除;0:禁用;1启用]'",
            'created_at' => "int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='扩展_微商城_商品_sku表'");
        
        /* 索引设置 */
        $this->createIndex('product_id','{{%addon_shop_product_sku}}','product_id',0);
        
        
        /* 表数据 */
        $this->insert('{{%addon_shop_product_sku}}',['id'=>'65','merchant_id'=>'0','product_id'=>'22','name'=>'','img'=>'','price'=>'5.00','market_price'=>'10.00','cost_price'=>'100.00','stock'=>'50','code'=>'','barcode'=>'','sort'=>'999','data'=>'','status'=>'0','created_at'=>'1557727594','updated_at'=>'1557728720']);
        $this->insert('{{%addon_shop_product_sku}}',['id'=>'77','merchant_id'=>'1','product_id'=>'21','name'=>'可爱 大 红','img'=>'','price'=>'26.00','market_price'=>'20.00','cost_price'=>'20.00','stock'=>'99804','code'=>'12345678','barcode'=>'','sort'=>'1999','data'=>'50-47-44','status'=>'1','created_at'=>'1557731455','updated_at'=>'1570609674']);
        $this->insert('{{%addon_shop_product_sku}}',['id'=>'80','merchant_id'=>'1','product_id'=>'21','name'=>'可爱 大 黄','img'=>'','price'=>'26.00','market_price'=>'20.00','cost_price'=>'20.00','stock'=>'8674','code'=>'12345678','barcode'=>'','sort'=>'1999','data'=>'50-47-45','status'=>'1','created_at'=>'1557732544','updated_at'=>'1570609674']);
        $this->insert('{{%addon_shop_product_sku}}',['id'=>'81','merchant_id'=>'1','product_id'=>'21','name'=>'可爱 中 红','img'=>'','price'=>'26.00','market_price'=>'20.00','cost_price'=>'20.00','stock'=>'970','code'=>'12345678','barcode'=>'','sort'=>'1999','data'=>'50-48-44','status'=>'1','created_at'=>'1557732544','updated_at'=>'1570604763']);
        $this->insert('{{%addon_shop_product_sku}}',['id'=>'82','merchant_id'=>'1','product_id'=>'21','name'=>'可爱 中 黄','img'=>'','price'=>'26.00','market_price'=>'20.00','cost_price'=>'20.00','stock'=>'1000','code'=>'12345678','barcode'=>'','sort'=>'1999','data'=>'50-48-45','status'=>'1','created_at'=>'1557732544','updated_at'=>'1570604763']);
        $this->insert('{{%addon_shop_product_sku}}',['id'=>'83','merchant_id'=>'1','product_id'=>'21','name'=>'可爱 小 红','img'=>'','price'=>'26.00','market_price'=>'20.00','cost_price'=>'0.00','stock'=>'844','code'=>'12345678','barcode'=>'','sort'=>'1999','data'=>'50-49-44','status'=>'1','created_at'=>'1557732650','updated_at'=>'1570609674']);
        $this->insert('{{%addon_shop_product_sku}}',['id'=>'84','merchant_id'=>'1','product_id'=>'21','name'=>'可爱 小 黄','img'=>'','price'=>'26.00','market_price'=>'20.00','cost_price'=>'0.00','stock'=>'1000','code'=>'12345678','barcode'=>'','sort'=>'1999','data'=>'50-49-45','status'=>'1','created_at'=>'1557732650','updated_at'=>'1570604763']);
        $this->insert('{{%addon_shop_product_sku}}',['id'=>'85','merchant_id'=>'0','product_id'=>'15','name'=>'','img'=>'','price'=>'50.00','market_price'=>'0.00','cost_price'=>'0.00','stock'=>'100','code'=>'','barcode'=>'','sort'=>'999','data'=>'','status'=>'1','created_at'=>'1557724732','updated_at'=>'1563528446']);
        $this->insert('{{%addon_shop_product_sku}}',['id'=>'86','merchant_id'=>'1','product_id'=>'21','name'=>'青春 大 红','img'=>'','price'=>'0.00','market_price'=>'0.00','cost_price'=>'0.00','stock'=>'0','code'=>'0','barcode'=>'','sort'=>'1999','data'=>'51-47-44','status'=>'1','created_at'=>'1570604720','updated_at'=>'1570604763']);
        $this->insert('{{%addon_shop_product_sku}}',['id'=>'87','merchant_id'=>'1','product_id'=>'21','name'=>'青春 大 黄','img'=>'','price'=>'0.00','market_price'=>'0.00','cost_price'=>'0.00','stock'=>'0','code'=>'0','barcode'=>'','sort'=>'1999','data'=>'51-47-45','status'=>'1','created_at'=>'1570604720','updated_at'=>'1570604763']);
        $this->insert('{{%addon_shop_product_sku}}',['id'=>'88','merchant_id'=>'1','product_id'=>'21','name'=>'青春 中 红','img'=>'','price'=>'0.00','market_price'=>'0.00','cost_price'=>'0.00','stock'=>'0','code'=>'0','barcode'=>'','sort'=>'1999','data'=>'51-48-44','status'=>'1','created_at'=>'1570604720','updated_at'=>'1570604763']);
        $this->insert('{{%addon_shop_product_sku}}',['id'=>'89','merchant_id'=>'1','product_id'=>'21','name'=>'青春 中 黄','img'=>'','price'=>'0.00','market_price'=>'0.00','cost_price'=>'0.00','stock'=>'0','code'=>'0','barcode'=>'','sort'=>'1999','data'=>'51-48-45','status'=>'1','created_at'=>'1570604720','updated_at'=>'1570604763']);
        $this->insert('{{%addon_shop_product_sku}}',['id'=>'90','merchant_id'=>'1','product_id'=>'21','name'=>'青春 小 红','img'=>'','price'=>'0.00','market_price'=>'0.00','cost_price'=>'0.00','stock'=>'0','code'=>'0','barcode'=>'','sort'=>'1999','data'=>'51-49-44','status'=>'1','created_at'=>'1570604720','updated_at'=>'1570604763']);
        $this->insert('{{%addon_shop_product_sku}}',['id'=>'91','merchant_id'=>'1','product_id'=>'21','name'=>'青春 小 黄','img'=>'','price'=>'10.00','market_price'=>'0.00','cost_price'=>'0.00','stock'=>'20','code'=>'0','barcode'=>'','sort'=>'1999','data'=>'51-49-45','status'=>'1','created_at'=>'1570604720','updated_at'=>'1570604763']);
        $this->insert('{{%addon_shop_product_sku}}',['id'=>'92','merchant_id'=>'1','product_id'=>'21','name'=>'可爱 大 蓝','img'=>'','price'=>'0.00','market_price'=>'0.00','cost_price'=>'0.00','stock'=>'0','code'=>'0','barcode'=>'','sort'=>'1999','data'=>'50-47-46','status'=>'1','created_at'=>'1570604763','updated_at'=>'1570604763']);
        $this->insert('{{%addon_shop_product_sku}}',['id'=>'93','merchant_id'=>'1','product_id'=>'21','name'=>'可爱 中 蓝','img'=>'','price'=>'0.00','market_price'=>'0.00','cost_price'=>'0.00','stock'=>'0','code'=>'0','barcode'=>'','sort'=>'1999','data'=>'50-48-46','status'=>'1','created_at'=>'1570604763','updated_at'=>'1570604763']);
        $this->insert('{{%addon_shop_product_sku}}',['id'=>'94','merchant_id'=>'1','product_id'=>'21','name'=>'可爱 小 蓝','img'=>'','price'=>'0.00','market_price'=>'0.00','cost_price'=>'0.00','stock'=>'0','code'=>'0','barcode'=>'','sort'=>'1999','data'=>'50-49-46','status'=>'1','created_at'=>'1570604763','updated_at'=>'1570604763']);
        $this->insert('{{%addon_shop_product_sku}}',['id'=>'95','merchant_id'=>'1','product_id'=>'21','name'=>'青春 大 蓝','img'=>'','price'=>'0.00','market_price'=>'0.00','cost_price'=>'0.00','stock'=>'0','code'=>'0','barcode'=>'','sort'=>'1999','data'=>'51-47-46','status'=>'1','created_at'=>'1570604763','updated_at'=>'1570604763']);
        $this->insert('{{%addon_shop_product_sku}}',['id'=>'96','merchant_id'=>'1','product_id'=>'21','name'=>'青春 中 蓝','img'=>'','price'=>'11.00','market_price'=>'0.00','cost_price'=>'0.00','stock'=>'0','code'=>'0','barcode'=>'','sort'=>'1999','data'=>'51-48-46','status'=>'1','created_at'=>'1570604763','updated_at'=>'1570604763']);
        $this->insert('{{%addon_shop_product_sku}}',['id'=>'97','merchant_id'=>'1','product_id'=>'21','name'=>'青春 小 蓝','img'=>'','price'=>'0.00','market_price'=>'0.00','cost_price'=>'0.00','stock'=>'0','code'=>'0','barcode'=>'','sort'=>'1999','data'=>'51-49-46','status'=>'1','created_at'=>'1570604763','updated_at'=>'1570604763']);
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_product_sku}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

