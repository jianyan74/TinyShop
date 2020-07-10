<?php

use yii\db\Migration;

class m200529_160730_addon_shop_marketing_coupon_product extends Migration
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
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='扩展_微商城_优惠券可使用商品关联表'");
        
        /* 索引设置 */
        $this->createIndex('coupon_type_id','{{%addon_shop_marketing_coupon_product}}','coupon_type_id',0);
        $this->createIndex('product_id','{{%addon_shop_marketing_coupon_product}}','product_id',0);
        
        
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

