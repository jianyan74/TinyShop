<?php

use yii\db\Migration;

class m200529_160734_addon_shop_system_attribute_option extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_system_attribute_option}}', [
            'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
            'merchant_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '商户id'",
            'title' => "varchar(125) NOT NULL COMMENT '选项名称'",
            'attribute_id' => "int(11) NOT NULL COMMENT '属性编码'",
            'sort' => "int(11) NOT NULL DEFAULT '999' COMMENT '排序'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='扩展_微商城_系统属性表'");
        
        /* 索引设置 */
        $this->createIndex('product_attribute_option_name_attr_id_index','{{%addon_shop_system_attribute_option}}','title, attribute_id',0);
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_system_attribute_option}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

