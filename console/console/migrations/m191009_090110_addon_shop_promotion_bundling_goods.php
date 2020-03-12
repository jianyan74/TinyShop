<?php

use yii\db\Migration;

class m191009_090110_addon_shop_promotion_bundling_goods extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_promotion_bundling_goods}}', [
            'bl_goods_id' => "int(11) NOT NULL AUTO_INCREMENT COMMENT '组合商品id'",
            'bl_id' => "int(11) NOT NULL COMMENT '组合id'",
            'goods_id' => "int(10) unsigned NOT NULL COMMENT '商品id'",
            'goods_name' => "varchar(50) NOT NULL COMMENT '商品名称'",
            'goods_picture' => "varchar(100) NOT NULL COMMENT '商品图片'",
            'bl_goods_price' => "decimal(10,2) NOT NULL COMMENT '商品组合价格'",
            'sort' => "int(11) NULL",
            'PRIMARY KEY (`bl_goods_id`)'
        ], "ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='组合销售活动商品表'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_promotion_bundling_goods}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

