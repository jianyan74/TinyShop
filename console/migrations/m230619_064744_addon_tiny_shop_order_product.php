<?php

use yii\db\Migration;

class m230619_064744_addon_tiny_shop_order_product extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');

        /* 创建表 */
        $this->createTable('{{%addon_tiny_shop_order_product}}', [
            'id' => "int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '订单项ID'",
            'merchant_id' => "int(11) NULL DEFAULT '1' COMMENT '店铺ID'",
            'buyer_id' => "int(11) NULL DEFAULT '0' COMMENT '购买人ID'",
            'order_id' => "int(11) NULL DEFAULT '0' COMMENT '订单ID'",
            'order_sn' => "varchar(100) NULL DEFAULT '' COMMENT '订单编号'",
            'store_id' => "int(11) NULL DEFAULT '0' COMMENT '门店id'",
            'product_id' => "int(11) NULL COMMENT '商品ID'",
            'product_name' => "varchar(100) NULL DEFAULT '' COMMENT '商品名称'",
            'product_money' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '商品优惠后总价'",
            'product_original_money' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '商品原本总价'",
            'product_picture' => "varchar(255) NULL DEFAULT '' COMMENT '商品图片'",
            'sku_id' => "int(11) NULL DEFAULT '0' COMMENT 'skuID'",
            'sku_name' => "varchar(100) NULL DEFAULT '' COMMENT 'sku名称'",
            'sku_no' => "varchar(100) NULL DEFAULT '' COMMENT '商品编码'",
            'barcode' => "varchar(100) NULL DEFAULT '' COMMENT '商品条码'",
            'price' => "decimal(19,2) NULL DEFAULT '0.00' COMMENT '商品价格'",
            'cost_price' => "decimal(19,2) NULL DEFAULT '0.00' COMMENT '商品成本价'",
            'profit_price' => "decimal(19,2) NULL DEFAULT '0.00' COMMENT '商品利润'",
            'num' => "int(10) unsigned NULL DEFAULT '0' COMMENT '购买数量'",
            'adjust_money' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '调整金额'",
            'point_exchange_type' => "int(11) NULL DEFAULT '0' COMMENT '积分兑换类型0.非积分兑换1.积分兑换'",
            'product_type' => "int(10) NULL DEFAULT '0' COMMENT '虚拟商品类型'",
            'stock_deduction_type' => "tinyint(4) NULL DEFAULT '1' COMMENT '库存扣减类型'",
            'marketing_id' => "int(11) NULL DEFAULT '0' COMMENT '促销ID'",
            'marketing_type' => "varchar(50) NULL DEFAULT '' COMMENT '促销类型'",
            'marketing_product_id' => "int(10) NULL DEFAULT '0' COMMENT '营销活动产品id'",
            'order_type' => "int(11) NULL DEFAULT '1' COMMENT '订单类型'",
            'order_status' => "int(11) NULL DEFAULT '0' COMMENT '订单状态'",
            'give_point' => "int(11) NULL DEFAULT '0' COMMENT '积分数量'",
            'give_growth' => "int(11) NULL DEFAULT '0' COMMENT '赠送成长值'",
            'give_coin' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '订单成功之后返购物币'",
            'shipping_status' => "int(11) NULL DEFAULT '0' COMMENT '物流状态'",
            'is_oversold' => "tinyint(4) NULL DEFAULT '0' COMMENT '是否超卖'",
            'is_evaluate' => "tinyint(4) NULL DEFAULT '0' COMMENT '是否评价 0为未评价 1为已评价 2为已追评'",
            'supplier_id' => "int(11) NULL DEFAULT '0' COMMENT '供应商'",
            'supplier_name' => "varchar(50) NULL DEFAULT '' COMMENT '供货商名称'",
            'gift_flag' => "int(11) NULL DEFAULT '0' COMMENT '赠品标识，0:不是赠品，大于0：赠品id'",
            'refund_money' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '退款金额'",
            'refund_num' => "int(10) NULL DEFAULT '0' COMMENT '退款数量'",
            'refund_type' => "tinyint(4) NULL DEFAULT '0' COMMENT '退款方式'",
            'refund_status' => "int(10) NULL DEFAULT '0' COMMENT '售后状态'",
            'after_sale_id' => "int(10) NULL DEFAULT '0' COMMENT '售后ID'",
            'is_commission' => "tinyint(4) NULL DEFAULT '0' COMMENT '是否支持分销'",
            'status' => "tinyint(4) NULL DEFAULT '1' COMMENT '状态[-1:删除;0:禁用;1启用]'",
            'created_at' => "int(10) NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) NULL DEFAULT '0' COMMENT '修改时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='扩展_微商城_订单商品表'");

        /* 索引设置 */
        $this->createIndex('UK_ns_order_goods_buyer_id','{{%addon_tiny_shop_order_product}}','buyer_id',0);
        $this->createIndex('UK_ns_order_goods_goods_id','{{%addon_tiny_shop_order_product}}','product_id',0);
        $this->createIndex('UK_ns_order_goods_order_id','{{%addon_tiny_shop_order_product}}','order_id',0);
        $this->createIndex('UK_ns_order_goods_sku_id','{{%addon_tiny_shop_order_product}}','sku_id',0);
        $this->createIndex('UK_ns_order_goods_promotion_id','{{%addon_tiny_shop_order_product}}','marketing_id, marketing_type',0);


        /* 表数据 */

        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_tiny_shop_order_product}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

