<?php

use yii\db\Migration;

class m191009_090110_addon_shop_product_attribute_value extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_product_attribute_value}}', [
            'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
            'merchant_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '商户id'",
            'product_id' => "int(11) NOT NULL COMMENT '商品编码'",
            'base_attribute_value_id' => "int(11) NOT NULL DEFAULT '0' COMMENT '属性编码'",
            'title' => "varchar(125) NOT NULL DEFAULT '' COMMENT '参数名称'",
            'value' => "varchar(125) NOT NULL DEFAULT '' COMMENT '参数值'",
            'sort' => "int(11) NOT NULL DEFAULT '999' COMMENT '排序'",
            'status' => "tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态(-1:已删除,0:禁用,1:正常)'",
            'created_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '修改时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='扩展_微商城_商品_自定义属性表'");
        
        /* 索引设置 */
        $this->createIndex('product_supplier_attribute_name_product_id_index','{{%addon_shop_product_attribute_value}}','title, product_id',0);
        
        
        /* 表数据 */
        $this->insert('{{%addon_shop_product_attribute_value}}',['id'=>'88','merchant_id'=>'1','product_id'=>'12','base_attribute_value_id'=>'35','title'=>'版型','value'=>'宽松','sort'=>'999','status'=>'1','created_at'=>'1557468306','updated_at'=>'1557468306']);
        $this->insert('{{%addon_shop_product_attribute_value}}',['id'=>'89','merchant_id'=>'1','product_id'=>'12','base_attribute_value_id'=>'37','title'=>'其他','value'=>'','sort'=>'999','status'=>'1','created_at'=>'1557468306','updated_at'=>'1557468306']);
        $this->insert('{{%addon_shop_product_attribute_value}}',['id'=>'92','merchant_id'=>'1','product_id'=>'21','base_attribute_value_id'=>'35','title'=>'版型','value'=>'宽松','sort'=>'999','status'=>'1','created_at'=>'1557729106','updated_at'=>'1557729106']);
        $this->insert('{{%addon_shop_product_attribute_value}}',['id'=>'93','merchant_id'=>'1','product_id'=>'21','base_attribute_value_id'=>'37','title'=>'其他','value'=>'','sort'=>'999','status'=>'1','created_at'=>'1557729106','updated_at'=>'1557729106']);
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_product_attribute_value}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

