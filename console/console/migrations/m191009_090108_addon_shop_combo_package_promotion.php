<?php

use yii\db\Migration;

class m191009_090108_addon_shop_combo_package_promotion extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_combo_package_promotion}}', [
            'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id'",
            'combo_package_name' => "varchar(100) NOT NULL DEFAULT '' COMMENT '组合套餐名称'",
            'combo_package_price' => "decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '组合套餐价格'",
            'goods_id_array' => "varchar(255) NOT NULL COMMENT '参与组合套餐的商品集合'",
            'is_shelves' => "tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否上架（0:下架,1:上架）'",
            'shop_id' => "int(11) NOT NULL COMMENT '店铺id'",
            'create_time' => "int(11) NOT NULL DEFAULT '0' COMMENT '创建时间'",
            'update_time' => "int(11) NOT NULL DEFAULT '0' COMMENT '修改时间'",
            'original_price' => "decimal(19,2) NOT NULL DEFAULT '0.00' COMMENT '原价,仅作参考商品原价所取为sku列表中最低价'",
            'save_the_price' => "decimal(19,2) NOT NULL DEFAULT '0.00' COMMENT '节省价,仅作参考不参与实际计算'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='组合套餐促销'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_combo_package_promotion}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

