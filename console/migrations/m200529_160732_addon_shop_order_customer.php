<?php

use yii\db\Migration;

class m200529_160732_addon_shop_order_customer extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_order_customer}}', [
            'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id'",
            'merchant_id' => "int(11) NULL DEFAULT '1' COMMENT '店铺ID'",
            'product_id' => "int(11) NULL DEFAULT '0' COMMENT '商品id'",
            'order_id' => "int(11) NULL DEFAULT '0' COMMENT '订单id'",
            'member_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '用户id'",
            'order_sn' => "varchar(30) NULL DEFAULT '' COMMENT '订单编号'",
            'order_product_id' => "int(11) NULL DEFAULT '0' COMMENT '订单项id'",
            'product_name' => "varchar(200) NULL DEFAULT '' COMMENT '商品名称'",
            'sku_id' => "int(11) NULL DEFAULT '0' COMMENT 'skuID'",
            'sku_name' => "varchar(50) NULL DEFAULT '' COMMENT 'sku名称'",
            'price' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '商品价格'",
            'product_picture' => "varchar(100) NULL DEFAULT '0' COMMENT '商品图片'",
            'num' => "int(11) NULL DEFAULT '0' COMMENT '购买数量'",
            'order_type' => "tinyint(4) NULL DEFAULT '0' COMMENT '订单类型'",
            'refund_require_money' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '退款金额'",
            'refund_type' => "varchar(255) NULL DEFAULT '' COMMENT '退款方式  退款退货'",
            'refund_reason' => "varchar(255) NULL DEFAULT '' COMMENT '退款原因'",
            'refund_explain' => "varchar(200) NULL DEFAULT '' COMMENT '退款说明'",
            'refund_evidence' => "json NULL COMMENT '退款凭证'",
            'refund_status' => "int(11) NULL DEFAULT '0' COMMENT '退款状态'",
            'refund_time' => "int(11) NULL DEFAULT '0' COMMENT '退款时间'",
            'refund_shipping_code' => "varchar(100) NULL DEFAULT '' COMMENT '退款物流单号'",
            'refund_shipping_company' => "varchar(100) NULL DEFAULT '' COMMENT '退款物流公司名称'",
            'refund_balance_money' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '订单退款余额'",
            'order_from' => "varchar(255) NULL DEFAULT '' COMMENT '订单来源'",
            'user_name' => "varchar(50) NULL DEFAULT '' COMMENT '买家会员名称'",
            'receiver_name' => "varchar(50) NULL DEFAULT '' COMMENT '收货人姓名'",
            'receiver_province' => "int(11) NULL DEFAULT '0' COMMENT '收货人所在省'",
            'receiver_city' => "int(11) NULL DEFAULT '0' COMMENT '收货人所在城市'",
            'receiver_area' => "int(11) NULL DEFAULT '0' COMMENT '收货人所在街道'",
            'receiver_address' => "varchar(255) NULL DEFAULT '' COMMENT '收货人详细地址'",
            'receiver_region_name' => "varchar(200) NULL DEFAULT '' COMMENT '收货人详细地址'",
            'receiver_mobile' => "varchar(20) NULL DEFAULT '' COMMENT '收货人的手机号码'",
            'payment_type' => "tinyint(4) NULL DEFAULT '0' COMMENT '支付类型。取值范围：...'",
            'shipping_type' => "tinyint(4) NULL DEFAULT '1' COMMENT '订单配送方式'",
            'product_money' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '商品总价'",
            'fixed_telephone' => "varchar(50) NULL DEFAULT '' COMMENT '固定电话'",
            'memo' => "varchar(255) NULL DEFAULT '' COMMENT '备注'",
            'status' => "tinyint(4) NULL DEFAULT '1' COMMENT '状态[-1:删除;0:禁用;1启用]'",
            'created_at' => "int(10) NULL DEFAULT '0'",
            'updated_at' => "int(10) NULL DEFAULT '0'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='售后记录表'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_order_customer}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

