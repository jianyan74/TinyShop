<?php

use yii\db\Migration;

class m200311_140536_addon_shop_order_product_promotion_details extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_order_product_promotion_details}}', [
            'id' => "int(11) NOT NULL AUTO_INCREMENT",
            'order_id' => "int(11) NOT NULL COMMENT '订单ID'",
            'sku_id' => "int(11) NOT NULL COMMENT '商品skuid'",
            'promotion_type' => "varbinary(255) NOT NULL COMMENT '优惠类型规则ID（满减对应规则）'",
            'promotion_id' => "int(11) NOT NULL COMMENT '优惠ID'",
            'discount_money' => "decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '优惠的金额，单位：元，精确到小数点后两位'",
            'used_time' => "int(11) NULL DEFAULT '0' COMMENT '使用时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='订单商品优惠详情'");
        
        /* 索引设置 */
        $this->createIndex('IDX_ns_order_goods_promotion_d_order_id','{{%addon_shop_order_product_promotion_details}}','order_id',0);
        $this->createIndex('IDX_ns_order_goods_promotion_d_promotion_id','{{%addon_shop_order_product_promotion_details}}','promotion_id',0);
        $this->createIndex('IDX_ns_order_goods_promotion_d_promotion_type','{{%addon_shop_order_product_promotion_details}}','promotion_type',0);
        $this->createIndex('IDX_ns_order_goods_promotion_d_sku_id','{{%addon_shop_order_product_promotion_details}}','sku_id',0);
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_order_product_promotion_details}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

