<?php

use yii\db\Migration;

class m191009_090108_addon_shop_marketing_coupon_product extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_marketing_coupon_product}}', [
            'id' => "int(11) NOT NULL AUTO_INCREMENT",
            'coupon_type_id' => "int(11) NOT NULL COMMENT '优惠券类型id'",
            'product_id' => "int(11) NOT NULL COMMENT '商品id'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AVG_ROW_LENGTH=606 ROW_FORMAT=DYNAMIC COMMENT='优惠券使用商品表'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_marketing_coupon_product}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

