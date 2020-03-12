<?php

use yii\db\Migration;

class m191009_090109_addon_shop_product_album extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_product_album}}', [
            'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
            'product_id' => "int(11) NOT NULL COMMENT '商品编号'",
            'name' => "varchar(25) NOT NULL COMMENT '商品名称'",
            'url' => "varchar(45) NULL COMMENT '图片地址'",
            'size' => "int(11) NULL COMMENT '视频大小'",
            'intro' => "varchar(255) NOT NULL COMMENT '图片介绍'",
            'sort' => "int(11) NOT NULL DEFAULT '999' COMMENT '排序'",
            'state' => "tinyint(4) NOT NULL DEFAULT '0' COMMENT '资源类型 0=>图片 1=>视频'",
            'status' => "tinyint(4) NULL DEFAULT '1' COMMENT '状态[-1:删除;0:禁用;1启用]'",
            'created_at' => "int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='扩展_微商城_商品_专辑表'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_product_album}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

