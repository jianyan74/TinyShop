<?php

use yii\db\Migration;

class m200529_160733_addon_shop_product_attribute extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_product_attribute}}', [
            'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
            'merchant_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '商户id'",
            'product_id' => "int(11) NOT NULL COMMENT '商品编码'",
            'system_attribute_id' => "int(11) NOT NULL DEFAULT '0' COMMENT '属性编码'",
            'name' => "varchar(125) NOT NULL DEFAULT '' COMMENT '规格名称'",
            'sort' => "int(11) NOT NULL DEFAULT '999' COMMENT '排序'",
            'show_type' => "tinyint(4) NULL DEFAULT '1' COMMENT '展示方式 1 文字 2 颜色 3 图片'",
            'is_visible' => "tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否可视'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='扩展_微商城_商品_自定义属性表'");
        
        /* 索引设置 */
        $this->createIndex('product_supplier_attribute_name_product_id_index','{{%addon_shop_product_attribute}}','name, product_id',0);
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_product_attribute}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

