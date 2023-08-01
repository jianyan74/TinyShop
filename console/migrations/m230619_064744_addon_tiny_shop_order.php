<?php

use yii\db\Migration;

class m230619_064744_addon_tiny_shop_order extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');

        /* 创建表 */
        $this->createTable('{{%addon_tiny_shop_order}}', [
            'id' => "int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '订单id'",
            'merchant_id' => "int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商户id'",
            'merchant_title' => "varchar(100) NULL DEFAULT '' COMMENT '商户店铺名称'",
            'order_sn' => "varchar(50) NULL DEFAULT '' COMMENT '订单编号'",
            'order_from' => "varchar(50) NULL DEFAULT '' COMMENT '订单来源'",
            'out_trade_no' => "varchar(50) NULL DEFAULT '' COMMENT '外部交易号'",
            'order_type' => "tinyint(4) NULL DEFAULT '1' COMMENT '订单类型'",
            'pay_type' => "int(10) NULL DEFAULT '0' COMMENT '支付类型'",
            'shipping_type' => "int(10) NULL DEFAULT '1' COMMENT '订单配送方式'",
            'buyer_id' => "int(11) NULL DEFAULT '0' COMMENT '买家id'",
            'buyer_nickname' => "varchar(50) NULL DEFAULT '' COMMENT '买家会员名称'",
            'buyer_ip' => "varchar(20) NULL DEFAULT '' COMMENT '买家ip'",
            'buyer_message' => "varchar(200) NULL DEFAULT '' COMMENT '买家附言'",
            'receiver_id' => "int(11) NULL DEFAULT '0' COMMENT '收货地址ID'",
            'receiver_mobile' => "varchar(20) NULL DEFAULT '' COMMENT '收货人的手机号码'",
            'receiver_province_id' => "int(11) NULL DEFAULT '0' COMMENT '收货人所在省'",
            'receiver_city_id' => "int(11) NULL DEFAULT '0' COMMENT '收货人所在城市'",
            'receiver_area_id' => "int(11) NULL DEFAULT '0' COMMENT '收货人所在街道'",
            'receiver_name' => "varchar(200) NULL DEFAULT '' COMMENT '收货人详细地址'",
            'receiver_details' => "varchar(200) NULL DEFAULT '' COMMENT '收货人详细地址'",
            'receiver_zip' => "varchar(20) NULL DEFAULT '' COMMENT '收货人邮编'",
            'receiver_realname' => "varchar(50) NULL DEFAULT '' COMMENT '收货人姓名'",
            'receiver_longitude' => "varchar(100) NULL DEFAULT '' COMMENT '收货人经度'",
            'receiver_latitude' => "varchar(100) NULL DEFAULT '' COMMENT '收货人纬度'",
            'seller_star' => "tinyint(4) NULL DEFAULT '0' COMMENT '卖家对订单的标注星标'",
            'seller_memo' => "varchar(255) NULL DEFAULT '' COMMENT '卖家对订单的备注'",
            'consign_time_adjust' => "int(11) NULL DEFAULT '0' COMMENT '卖家延迟发货时间'",
            'shipping_money' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '订单运费'",
            'product_money' => "decimal(19,2) NULL DEFAULT '0.00' COMMENT '商品优惠后总价'",
            'product_original_money' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '商品原本总价'",
            'product_profit_price' => "decimal(19,2) NULL DEFAULT '0.00' COMMENT '商品利润'",
            'product_type' => "int(10) NULL DEFAULT '1' COMMENT '商品类型'",
            'product_count' => "int(10) NULL DEFAULT '0' COMMENT '订单数量'",
            'order_money' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '订单总价'",
            'pay_money' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '订单实付金额'",
            'final_money' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '预售尾款'",
            'point' => "int(11) NULL DEFAULT '0' COMMENT '订单消耗积分'",
            'marketing_id' => "int(11) NOT NULL DEFAULT '0' COMMENT '营销活动id'",
            'marketing_type' => "varchar(50) NOT NULL DEFAULT '' COMMENT '营销活动类型'",
            'marketing_product_id' => "int(10) NULL DEFAULT '0' COMMENT '营销活动产品id'",
            'marketing_money' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '订单优惠活动金额'",
            'wholesale_record_id' => "int(10) NULL DEFAULT '0' COMMENT '拼团记录ID'",
            'give_point' => "int(11) NULL DEFAULT '0' COMMENT '订单赠送积分'",
            'give_growth' => "int(11) NULL DEFAULT '0' COMMENT '赠送成长值'",
            'give_coin' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '订单成功之后返购物币'",
            'order_status' => "int(6) NULL DEFAULT '0' COMMENT '订单状态'",
            'pay_status' => "tinyint(4) NULL DEFAULT '0' COMMENT '订单付款状态'",
            'shipping_status' => "tinyint(4) NULL DEFAULT '0' COMMENT '订单配送状态'",
            'feedback_status' => "tinyint(4) NULL DEFAULT '0' COMMENT '订单维权状态'",
            'is_evaluate' => "smallint(6) NULL DEFAULT '0' COMMENT '是否评价 0为未评价 1为已评价 2为已追评'",
            'tax_money' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '税费'",
            'store_id' => "int(11) NULL DEFAULT '0' COMMENT '门店id'",
            'invoice_id' => "int(11) NULL DEFAULT '0' COMMENT '发票id'",
            'express_company_id' => "int(10) NULL DEFAULT '0' COMMENT '物流公司'",
            'give_point_type' => "int(11) NULL DEFAULT '1' COMMENT '积分返还类型 1 订单完成  2 订单收货 3  支付订单'",
            'give_growth_type' => "int(11) NULL DEFAULT '1' COMMENT '成长值返还类型 1 订单完成  2 订单收货 3  支付订单'",
            'caballero_member_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '骑手用户id'",
            'pay_time' => "int(11) NULL DEFAULT '0' COMMENT '订单付款时间'",
            'receiving_time' => "int(11) NULL DEFAULT '0' COMMENT '骑手接单时间'",
            'consign_time' => "int(11) NULL DEFAULT '0' COMMENT '卖家发货时间'",
            'sign_time' => "int(11) NULL DEFAULT '0' COMMENT '买家签收时间'",
            'finish_time' => "int(11) NULL DEFAULT '0' COMMENT '订单完成时间'",
            'close_time' => "int(11) NULL DEFAULT '0' COMMENT '关闭的时间'",
            'auto_sign_time' => "int(11) NULL DEFAULT '0' COMMENT '自动签收时间'",
            'auto_finish_time' => "int(11) NULL DEFAULT '0' COMMENT '自动完成时间'",
            'auto_evaluate_time' => "int(11) NULL DEFAULT '0' COMMENT '自动评价时间'",
            'fixed_telephone' => "varchar(50) NULL DEFAULT '' COMMENT '固定电话'",
            'distribution_time_out' => "varchar(50) NULL DEFAULT '' COMMENT '配送时间段'",
            'subscribe_shipping_start_time' => "int(10) NULL DEFAULT '0' COMMENT '预约配送开始时间'",
            'subscribe_shipping_end_time' => "int(10) NULL DEFAULT '0' COMMENT '预约配送结束时间'",
            'is_new_member' => "tinyint(4) NULL DEFAULT '0' COMMENT '是否新顾客'",
            'is_print' => "tinyint(4) NOT NULL DEFAULT '0' COMMENT '已打印 0未打印1已打印'",
            'is_oversold' => "tinyint(4) NULL DEFAULT '0' COMMENT '是否超卖'",
            'refund_money' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '退款金额'",
            'refund_num' => "int(10) NULL DEFAULT '0' COMMENT '退款数量'",
            'is_after_sale' => "tinyint(4) NULL DEFAULT '0' COMMENT '售后状态'",
            'promoter_code' => "varchar(50) NULL DEFAULT '' COMMENT '推广码'",
            'promoter_id' => "int(10) NULL DEFAULT '0' COMMENT '推广人ID'",
            'promoter_nickname' => "varchar(100) NULL DEFAULT '' COMMENT '推广人昵称'",
            'unite_no' => "varchar(30) NULL DEFAULT '' COMMENT '联合订单号'",
            'status' => "tinyint(4) NULL DEFAULT '1' COMMENT '状态[-1:删除;0:禁用;1启用]'",
            'created_at' => "int(10) NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) NULL DEFAULT '0' COMMENT '修改时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='扩展_微商城_订单表'");

        /* 索引设置 */
        $this->createIndex('UK_ns_order_buyer_id','{{%addon_tiny_shop_order}}','buyer_id',0);
        $this->createIndex('UK_ns_order_order_no','{{%addon_tiny_shop_order}}','order_sn',0);
        $this->createIndex('UK_ns_order_pay_status','{{%addon_tiny_shop_order}}','pay_status',0);
        $this->createIndex('UK_ns_order_status','{{%addon_tiny_shop_order}}','order_status',0);


        /* 表数据 */

        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_tiny_shop_order}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

