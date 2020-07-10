<?php

use yii\db\Migration;

class m200529_160733_addon_shop_order_product extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_order_product}}', [
            'id' => "int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '订单项ID'",
            'order_id' => "int(11) NULL COMMENT '订单ID'",
            'member_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '用户id'",
            'merchant_id' => "int(11) NULL DEFAULT '1' COMMENT '店铺ID'",
            'product_id' => "int(11) NULL COMMENT '商品ID'",
            'product_name' => "varchar(200) NULL DEFAULT '' COMMENT '商品名称'",
            'sku_id' => "int(11) NULL DEFAULT '0' COMMENT 'skuID'",
            'sku_name' => "varchar(50) NULL DEFAULT '' COMMENT 'sku名称'",
            'price' => "decimal(19,2) NULL DEFAULT '0.00' COMMENT '商品价格'",
            'cost_price' => "decimal(19,2) NULL DEFAULT '0.00' COMMENT '商品成本价'",
            'num' => "int(10) unsigned NULL DEFAULT '0' COMMENT '购买数量'",
            'adjust_money' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '调整金额'",
            'product_money' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '商品优惠后总价'",
            'product_original_money' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '商品原本总价'",
            'product_picture' => "varchar(100) NULL DEFAULT '' COMMENT '商品图片'",
            'buyer_id' => "int(11) NULL DEFAULT '0' COMMENT '购买人ID'",
            'point_exchange_type' => "int(11) NULL DEFAULT '0' COMMENT '积分兑换类型0.非积分兑换1.积分兑换'",
            'product_virtual_group' => "varchar(50) NULL DEFAULT '' COMMENT '虚拟商品类型'",
            'marketing_id' => "int(11) NULL DEFAULT '0' COMMENT '促销ID'",
            'marketing_type' => "varchar(50) NULL DEFAULT '0' COMMENT '促销类型'",
            'order_type' => "int(11) NULL DEFAULT '1' COMMENT '订单类型'",
            'order_status' => "int(11) NULL DEFAULT '0' COMMENT '订单状态'",
            'give_point' => "int(11) NULL DEFAULT '0' COMMENT '积分数量'",
            'shipping_status' => "int(11) NULL DEFAULT '0' COMMENT '物流状态'",
            'refund_type' => "int(11) NULL DEFAULT '1' COMMENT '退款方式'",
            'refund_require_money' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '退款金额'",
            'refund_reason' => "varchar(255) NULL DEFAULT '' COMMENT '退款原因'",
            'refund_explain' => "varchar(200) NULL DEFAULT '' COMMENT '退款说明'",
            'refund_evidence' => "json NULL COMMENT '退款凭证'",
            'refund_shipping_code' => "varchar(200) NULL DEFAULT '' COMMENT '退款物流单号'",
            'refund_shipping_company' => "varchar(200) NULL DEFAULT '0' COMMENT '退款物流公司名称'",
            'refund_real_money' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '实际退款金额'",
            'refund_status' => "int(1) NULL DEFAULT '0' COMMENT '退款状态'",
            'refund_time' => "int(11) NULL DEFAULT '0' COMMENT '退款时间'",
            'memo' => "varchar(200) NULL DEFAULT '' COMMENT '备注'",
            'is_evaluate' => "smallint(6) NULL DEFAULT '0' COMMENT '是否评价 0为未评价 1为已评价 2为已追评'",
            'refund_balance_money' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '订单退款余额'",
            'tmp_express_company' => "varchar(200) NULL DEFAULT '' COMMENT '批量打印时添加的临时物流公司'",
            'tmp_express_company_id' => "int(11) NULL DEFAULT '0' COMMENT '批量打印时添加的临时物流公司id'",
            'tmp_express_no' => "varchar(50) NULL DEFAULT '' COMMENT '批量打印时添加的临时订单编号'",
            'gift_flag' => "int(11) NULL DEFAULT '0' COMMENT '赠品标识，0:不是赠品，大于0：赠品id'",
            'is_customer' => "tinyint(1) unsigned NULL DEFAULT '0' COMMENT '是否售后 1:申请了售后;0:未申请'",
            'is_virtual' => "tinyint(1) NULL DEFAULT '0' COMMENT '是否包含 虚拟商品   0 不包含  1  包含'",
            'is_open_commission' => "tinyint(4) NULL DEFAULT '0' COMMENT '是否支持分销'",
            'status' => "tinyint(4) NULL DEFAULT '1' COMMENT '状态[-1:删除;0:禁用;1启用]'",
            'created_at' => "int(10) NULL DEFAULT '0'",
            'updated_at' => "int(10) NULL DEFAULT '0'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='扩展_微商城_订单商品表'");
        
        /* 索引设置 */
        $this->createIndex('UK_ns_order_goods_buyer_id','{{%addon_shop_order_product}}','buyer_id',0);
        $this->createIndex('UK_ns_order_goods_goods_id','{{%addon_shop_order_product}}','product_id',0);
        $this->createIndex('UK_ns_order_goods_order_id','{{%addon_shop_order_product}}','order_id',0);
        $this->createIndex('UK_ns_order_goods_promotion_id','{{%addon_shop_order_product}}','marketing_id',0);
        $this->createIndex('UK_ns_order_goods_sku_id','{{%addon_shop_order_product}}','sku_id',0);
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_order_product}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

