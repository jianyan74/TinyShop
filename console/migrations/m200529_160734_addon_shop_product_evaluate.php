<?php

use yii\db\Migration;

class m200529_160734_addon_shop_product_evaluate extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_product_evaluate}}', [
            'id' => "int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '评价ID'",
            'merchant_id' => "int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商户id'",
            'merchant_name' => "varchar(100) NULL DEFAULT '' COMMENT '商户店铺名称'",
            'order_id' => "int(11) NULL COMMENT '订单ID'",
            'order_sn' => "varchar(30) NULL COMMENT '订单编号'",
            'order_product_id' => "int(11) NULL COMMENT '订单项ID'",
            'product_id' => "int(11) NULL COMMENT '商品ID'",
            'product_name' => "varchar(200) NULL COMMENT '商品名称'",
            'product_price' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '商品价格'",
            'product_picture' => "varchar(255) NULL DEFAULT '' COMMENT '商品图片'",
            'sku_name' => "varchar(50) NULL DEFAULT '' COMMENT 'sku名称'",
            'content' => "varchar(255) NOT NULL DEFAULT '' COMMENT '评价内容'",
            'covers' => "json NULL COMMENT '评价图片'",
            'video' => "varchar(255) NULL DEFAULT '' COMMENT '视频地址'",
            'explain_first' => "varchar(255) NULL DEFAULT '' COMMENT '解释内容'",
            'member_id' => "int(11) NULL COMMENT '评价人编号'",
            'member_nickname' => "varchar(100) NULL DEFAULT '' COMMENT '评价人名称'",
            'member_head_portrait' => "varchar(150) NULL DEFAULT '' COMMENT '头像'",
            'is_anonymous' => "tinyint(1) NULL DEFAULT '0' COMMENT '0表示不是 1表示是匿名评价'",
            'scores' => "tinyint(1) NOT NULL COMMENT '1-5分'",
            'again_content' => "varchar(255) NULL DEFAULT '' COMMENT '追加评价内容'",
            'again_covers' => "json NULL COMMENT '追评评价图片'",
            'again_explain' => "varchar(255) NULL DEFAULT '' COMMENT '追加解释内容'",
            'again_addtime' => "int(11) NULL DEFAULT '0' COMMENT '追加评价时间'",
            'explain_type' => "int(11) NULL DEFAULT '0' COMMENT '1好评2中评3差评'",
            'has_again' => "tinyint(1) unsigned NULL DEFAULT '0' COMMENT '是否追加 0 否 1 是'",
            'has_content' => "tinyint(1) unsigned NULL DEFAULT '0' COMMENT '是否有内容 0 否 1 是'",
            'has_cover' => "tinyint(1) unsigned NULL DEFAULT '0' COMMENT '是否有图 0 否 1 是'",
            'has_video' => "tinyint(1) unsigned NULL DEFAULT '0' COMMENT '是否视频 0 否 1 是'",
            'status' => "tinyint(4) NULL DEFAULT '1' COMMENT '状态'",
            'created_at' => "int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COMMENT='扩展_微商城_商品评价表'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_product_evaluate}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

