<?php

use yii\db\Migration;

class m200529_160733_addon_shop_order_product_marketing_detail extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_order_product_marketing_detail}}', [
            'id' => "int(11) NOT NULL AUTO_INCREMENT",
            'order_id' => "int(11) NULL DEFAULT '0' COMMENT '订单ID'",
            'product_id' => "int(11) NULL DEFAULT '0' COMMENT '产品id'",
            'sku_id' => "int(11) NULL DEFAULT '0' COMMENT '产品skuid'",
            'marketing_id' => "int(11) NULL DEFAULT '0' COMMENT '优惠ID'",
            'marketing_type' => "varchar(100) NULL DEFAULT '' COMMENT '优惠类型'",
            'marketing_name' => "varchar(100) NULL DEFAULT '' COMMENT '优惠类型名称'",
            'marketing_condition' => "varchar(255) NULL DEFAULT '' COMMENT '优惠说明'",
            'free_shipping' => "tinyint(1) NULL DEFAULT '0' COMMENT '是否包邮 1包邮'",
            'discount_type' => "tinyint(1) NULL DEFAULT '1' COMMENT '优惠金额类型 1满减;2:折扣'",
            'discount_money' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '优惠的金额，单位：元，精确到小数点后两位'",
            'give_point' => "int(255) NULL DEFAULT '0' COMMENT '赠送积分'",
            'give_coupon_type_id' => "int(11) NULL DEFAULT '0' COMMENT '赠送的优惠券id'",
            'gift_id' => "int(11) NULL DEFAULT '0' COMMENT '赠品id'",
            'created_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '修改时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='订单商品优惠详情'");
        
        /* 索引设置 */
        $this->createIndex('IDX_ns_order_goods_promotion_d_order_id','{{%addon_shop_order_product_marketing_detail}}','order_id',0);
        $this->createIndex('IDX_ns_order_goods_promotion_d_promotion_id','{{%addon_shop_order_product_marketing_detail}}','marketing_id',0);
        $this->createIndex('IDX_ns_order_goods_promotion_d_promotion_type','{{%addon_shop_order_product_marketing_detail}}','marketing_type',0);
        $this->createIndex('IDX_ns_order_goods_promotion_d_sku_id','{{%addon_shop_order_product_marketing_detail}}','sku_id',0);
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_order_product_marketing_detail}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

