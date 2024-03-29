<?php

use yii\db\Migration;

class m230619_064744_addon_tiny_shop_product_spec_value extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_tiny_shop_product_spec_value}}', [
            'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
            'merchant_id' => "int(10) NULL DEFAULT '0' COMMENT '商户id'",
            'product_id' => "int(11) NULL COMMENT '商品编码'",
            'title' => "varchar(125) NULL DEFAULT '' COMMENT '属性标题'",
            'common_spec_id' => "int(11) unsigned NULL DEFAULT '0' COMMENT '系统规格id'",
            'common_spec_value_id' => "int(11) unsigned NULL DEFAULT '0' COMMENT '系统规格值id'",
            'data' => "varchar(125) NULL DEFAULT '' COMMENT '属性值例如颜色'",
            'sort' => "int(11) NULL DEFAULT '999' COMMENT '排序'",
            'pitch_on' => "tinyint(4) unsigned NULL DEFAULT '0' COMMENT '选中'",
            'status' => "tinyint(4) NULL DEFAULT '1' COMMENT '状态(-1:已删除,0:禁用,1:正常)'",
            'created_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '修改时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='扩展_微商城_商品规格属性表'");
        
        /* 索引设置 */
        $this->createIndex('product_attribute_and_option_sku_id_option_id_attribute_id_index','{{%addon_tiny_shop_product_spec_value}}','common_spec_value_id, common_spec_id',0);
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_tiny_shop_product_spec_value}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

