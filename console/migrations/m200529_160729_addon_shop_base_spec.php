<?php

use yii\db\Migration;

class m200529_160729_addon_shop_base_spec extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_base_spec}}', [
            'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
            'merchant_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '商户id'",
            'title' => "varchar(25) NOT NULL COMMENT '规格名称'",
            'sort' => "int(11) NOT NULL DEFAULT '999' COMMENT '排列次序'",
            'show_type' => "tinyint(255) NULL DEFAULT '1' COMMENT '展示方式[1:文字;2:颜色;3:图片]'",
            'explain' => "varchar(100) NULL DEFAULT '' COMMENT '规格说明'",
            'status' => "tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态(-1:已删除,0:禁用,1:正常)'",
            'created_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '修改时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='扩展_微商城_系统规格表'");
        
        /* 索引设置 */
        $this->createIndex('product_attribute_category_id_name_index','{{%addon_shop_base_spec}}','title',0);
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_base_spec}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

