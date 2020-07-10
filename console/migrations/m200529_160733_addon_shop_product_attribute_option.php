<?php

use yii\db\Migration;

class m200529_160733_addon_shop_product_attribute_option extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_product_attribute_option}}', [
            'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
            'sku_id' => "int(11) NULL COMMENT 'sku编码'",
            'product_id' => "int(11) NOT NULL COMMENT '商品编码'",
            'system_attribute_id' => "int(11) NOT NULL COMMENT '属性编码'",
            'system_option_id' => "int(11) NOT NULL DEFAULT '0' COMMENT '属性选项编码'",
            'title' => "varchar(125) NULL DEFAULT '' COMMENT '属性标题'",
            'value' => "varchar(125) NULL DEFAULT '' COMMENT '属性值例如颜色'",
            'sort' => "int(11) NOT NULL DEFAULT '999' COMMENT '排序'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='扩展_微商城_商品规格属性表'");
        
        /* 索引设置 */
        $this->createIndex('product_attribute_and_option_sku_id_option_id_attribute_id_index','{{%addon_shop_product_attribute_option}}','sku_id, system_option_id, system_attribute_id',0);
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_product_attribute_option}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

