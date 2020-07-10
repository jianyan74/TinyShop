<?php

use yii\db\Migration;

class m200529_160734_addon_shop_product_brand extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_product_brand}}', [
            'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
            'merchant_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '商户id'",
            'cate_id' => "int(11) NULL DEFAULT '0' COMMENT '商品类别编号'",
            'title' => "varchar(25) NOT NULL DEFAULT '' COMMENT '品牌名称'",
            'cover' => "varchar(125) NOT NULL DEFAULT '' COMMENT '图片url'",
            'sort' => "int(11) NOT NULL DEFAULT '999' COMMENT '排列次序'",
            'status' => "tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态'",
            'created_at' => "int(10) NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) NULL DEFAULT '0' COMMENT '修改时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='扩展_微商城_商品_品牌表'");
        
        /* 索引设置 */
        $this->createIndex('product_brand_name_unique','{{%addon_shop_product_brand}}','title',0);
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_product_brand}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

