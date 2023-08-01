<?php

use yii\db\Migration;

class m230619_064744_addon_tiny_shop_order_after_sale extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');

        /* 创建表 */
        $this->createTable('{{%addon_tiny_shop_order_after_sale}}', [
            'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id'",
            'merchant_id' => "int(11) NULL DEFAULT '1' COMMENT '店铺ID'",
            'type' => "tinyint(4) NULL DEFAULT '0' COMMENT '售后类型[1:售中;2:售后]'",
            'order_id' => "int(11) NULL DEFAULT '0' COMMENT '订单id'",
            'order_sn' => "varchar(30) NULL DEFAULT '' COMMENT '订单编号'",
            'order_product_id' => "int(11) NULL DEFAULT '0' COMMENT '订单项id'",
            'store_id' => "int(11) NULL DEFAULT '0' COMMENT '门店id'",
            'buyer_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '用户id'",
            'buyer_nickname' => "varchar(100) NULL DEFAULT '' COMMENT '用户昵称'",
            'product_id' => "int(11) NULL DEFAULT '0' COMMENT '商品id'",
            'sku_id' => "int(11) NULL DEFAULT '0' COMMENT 'skuID'",
            'number' => "int(11) NULL DEFAULT '0' COMMENT '购买数量'",
            'refund_apply_money' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '退款申请金额'",
            'refund_type' => "tinyint(4) NULL DEFAULT '0' COMMENT '退款方式'",
            'refund_pay_type' => "int(10) NULL DEFAULT '0' COMMENT '付款方式'",
            'refund_reason' => "varchar(255) NULL DEFAULT '' COMMENT '退款原因'",
            'refund_explain' => "varchar(200) NULL DEFAULT '' COMMENT '退款说明'",
            'refund_evidence' => "json NULL COMMENT '退款凭证'",
            'refund_status' => "int(11) NULL DEFAULT '0' COMMENT '退款状态'",
            'refund_money' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '订单退款余额'",
            'refund_time' => "int(11) NULL DEFAULT '0' COMMENT '退款时间'",
            'member_express_company' => "varchar(100) NULL DEFAULT '' COMMENT '退款物流公司名称'",
            'member_express_no' => "varchar(100) NULL DEFAULT '' COMMENT '退款物流单号'",
            'member_express_mobile' => "varchar(20) NULL DEFAULT '' COMMENT '退款手机号码'",
            'member_express_time' => "int(10) NULL DEFAULT '0' COMMENT '退款物流时间'",
            'merchant_shipping_type' => "tinyint(4) NULL DEFAULT '1' COMMENT '发货方式1 需要物流 0无需物流'",
            'merchant_express_company_id' => "int(11) NULL DEFAULT '0' COMMENT '快递公司id'",
            'merchant_express_company' => "varchar(255) NULL DEFAULT '' COMMENT '物流公司名称'",
            'merchant_express_no' => "varchar(50) NULL DEFAULT '' COMMENT '运单编号'",
            'merchant_express_mobile' => "varchar(20) NULL DEFAULT '' COMMENT '商家手机号码'",
            'merchant_express_time' => "int(10) NULL DEFAULT '0' COMMENT '退款物流时间'",
            'memo' => "varchar(255) NULL DEFAULT '' COMMENT '备注'",
            'status' => "tinyint(4) NULL DEFAULT '1' COMMENT '状态[-1:删除;0:禁用;1启用]'",
            'created_at' => "int(10) NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) NULL DEFAULT '0' COMMENT '修改时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='扩展_微商城_订单_售后记录表'");

        /* 索引设置 */
        $this->createIndex('order_id','{{%addon_tiny_shop_order_after_sale}}','order_id',0);
        $this->createIndex('buyer_id','{{%addon_tiny_shop_order_after_sale}}','buyer_id',0);
        $this->createIndex('refund_status','{{%addon_tiny_shop_order_after_sale}}','refund_status',0);


        /* 表数据 */

        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_tiny_shop_order_after_sale}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

