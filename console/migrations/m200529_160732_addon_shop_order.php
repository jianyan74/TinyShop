<?php

use yii\db\Migration;

class m200529_160732_addon_shop_order extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_order}}', [
            'id' => "int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '订单id'",
            'merchant_id' => "int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商户id'",
            'merchant_name' => "varchar(100) NULL DEFAULT '' COMMENT '商户店铺名称'",
            'order_sn' => "varchar(100) NOT NULL DEFAULT '' COMMENT '订单编号'",
            'out_trade_no' => "varchar(100) NULL DEFAULT '' COMMENT '外部交易号'",
            'order_type' => "tinyint(4) NULL DEFAULT '1' COMMENT '订单类型'",
            'wholesale_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '拼团id'",
            'payment_type' => "tinyint(4) NULL DEFAULT '0' COMMENT '支付类型。取值范围：































WEIXIN (微信自有支付)































WEIXIN_DAIXIAO (微信代销支付)































ALIPAY (支付宝支付)'",
            'shipping_type' => "tinyint(4) NULL DEFAULT '1' COMMENT '订单配送方式'",
            'order_from' => "varchar(200) NULL DEFAULT '' COMMENT '订单来源'",
            'buyer_id' => "int(11) NULL DEFAULT '0' COMMENT '买家id'",
            'user_name' => "varchar(50) NULL DEFAULT '' COMMENT '买家会员名称'",
            'buyer_ip' => "varchar(20) NULL DEFAULT '' COMMENT '买家ip'",
            'buyer_message' => "varchar(200) NULL DEFAULT '' COMMENT '买家附言'",
            'buyer_invoice' => "varchar(200) NULL DEFAULT '' COMMENT '买家发票信息'",
            'receiver_mobile' => "varchar(11) NULL DEFAULT '' COMMENT '收货人的手机号码'",
            'receiver_province' => "int(11) NULL DEFAULT '0' COMMENT '收货人所在省'",
            'receiver_city' => "int(11) NULL DEFAULT '0' COMMENT '收货人所在城市'",
            'receiver_area' => "int(11) NULL DEFAULT '0' COMMENT '收货人所在街道'",
            'receiver_address' => "varchar(200) NULL DEFAULT '' COMMENT '收货人详细地址'",
            'receiver_region_name' => "varchar(200) NULL DEFAULT '' COMMENT '收货人详细地址'",
            'receiver_zip' => "varchar(20) NULL DEFAULT '' COMMENT '收货人邮编'",
            'receiver_name' => "varchar(50) NULL DEFAULT '' COMMENT '收货人姓名'",
            'seller_star' => "tinyint(4) NULL DEFAULT '0' COMMENT '卖家对订单的标注星标'",
            'seller_memo' => "varchar(255) NULL DEFAULT '' COMMENT '卖家对订单的备注'",
            'consign_time_adjust' => "int(11) NULL DEFAULT '0' COMMENT '卖家延迟发货时间'",
            'product_money' => "decimal(19,2) NULL DEFAULT '0.00' COMMENT '商品优惠后总价'",
            'product_original_money' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '商品原本总价'",
            'product_virtual_group' => "varchar(50) NULL DEFAULT '' COMMENT '虚拟商品类型'",
            'order_money' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '订单总价'",
            'point' => "int(11) NULL DEFAULT '0' COMMENT '订单消耗积分'",
            'point_money' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '订单消耗积分抵多少钱'",
            'coupon_money' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '订单代金券支付金额'",
            'coupon_id' => "int(11) NULL DEFAULT '0' COMMENT '订单代金券id'",
            'user_money' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '订单余额支付金额'",
            'user_platform_money' => "decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '用户平台余额支付'",
            'marketing_id' => "int(11) NOT NULL DEFAULT '0' COMMENT '营销活动id'",
            'marketing_type' => "varchar(50) NOT NULL DEFAULT '0' COMMENT '营销活动类型'",
            'marketing_money' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '订单优惠活动金额'",
            'shipping_money' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '订单运费'",
            'pay_money' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '订单实付金额'",
            'refund_money' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '订单退款金额'",
            'final_payment_money' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '预售尾款'",
            'coin_money' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '购物币金额'",
            'give_point' => "int(11) NULL DEFAULT '0' COMMENT '订单赠送积分'",
            'give_coin' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '订单成功之后返购物币'",
            'order_status' => "int(6) NULL DEFAULT '0' COMMENT '订单状态'",
            'pay_status' => "tinyint(4) NULL DEFAULT '0' COMMENT '订单付款状态'",
            'shipping_status' => "tinyint(4) NULL DEFAULT '0' COMMENT '订单配送状态'",
            'review_status' => "tinyint(4) NULL DEFAULT '0' COMMENT '订单评价状态'",
            'feedback_status' => "tinyint(4) NULL DEFAULT '0' COMMENT '订单维权状态'",
            'is_evaluate' => "smallint(6) NULL DEFAULT '0' COMMENT '是否评价 0为未评价 1为已评价 2为已追评'",
            'tax_money' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '税费'",
            'invoice_id' => "int(11) NULL COMMENT '发票id'",
            'company_id' => "int(11) NULL DEFAULT '0' COMMENT '配送物流公司ID'",
            'company_name' => "varchar(200) NULL DEFAULT '' COMMENT '配送物流名称'",
            'give_point_type' => "int(11) NULL DEFAULT '1' COMMENT '积分返还类型 1 订单完成  2 订单收货 3  支付订单'",
            'pay_time' => "int(11) NULL DEFAULT '0' COMMENT '订单付款时间'",
            'shipping_time' => "int(11) NULL DEFAULT '0' COMMENT '买家要求配送时间'",
            'sign_time' => "int(11) NULL DEFAULT '0' COMMENT '买家签收时间'",
            'consign_time' => "int(11) NULL DEFAULT '0' COMMENT '卖家发货时间'",
            'finish_time' => "int(11) NULL DEFAULT '0' COMMENT '订单完成时间'",
            'close_time' => "int(11) NULL DEFAULT '0' COMMENT '关闭的时间'",
            'operator_type' => "int(1) NULL DEFAULT '0' COMMENT '操作人类型  1店铺  2用户'",
            'operator_id' => "int(11) NULL DEFAULT '0' COMMENT '操作人id'",
            'refund_balance_money' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '订单退款余额'",
            'fixed_telephone' => "varchar(50) NULL DEFAULT '' COMMENT '固定电话'",
            'distribution_time_out' => "varchar(50) NULL DEFAULT '' COMMENT '配送时间段'",
            'product_count' => "int(10) NULL DEFAULT '0' COMMENT '订单数量'",
            'is_new_member' => "tinyint(4) NULL DEFAULT '0' COMMENT '是否新顾客'",
            'is_virtual' => "tinyint(1) NULL DEFAULT '0' COMMENT '是否包含 虚拟商品   0 不包含  1  包含'",
            'promo_code' => "varchar(50) NULL DEFAULT '' COMMENT '推广码'",
            'status' => "tinyint(4) NULL DEFAULT '1' COMMENT '状态[-1:删除;0:禁用;1启用]'",
            'created_at' => "int(10) NULL DEFAULT '0'",
            'updated_at' => "int(10) NULL DEFAULT '0'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='扩展_微商城_订单表'");
        
        /* 索引设置 */
        $this->createIndex('UK_ns_order_buyer_id','{{%addon_shop_order}}','buyer_id',0);
        $this->createIndex('UK_ns_order_order_no','{{%addon_shop_order}}','order_sn',0);
        $this->createIndex('UK_ns_order_pay_status','{{%addon_shop_order}}','pay_status',0);
        $this->createIndex('UK_ns_order_status','{{%addon_shop_order}}','order_status',0);
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_order}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

