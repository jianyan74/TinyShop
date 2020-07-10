<?php

use yii\db\Migration;

class m200529_160732_addon_shop_order_account_records extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_order_account_records}}', [
            'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
            'merchant_id' => "int(11) NOT NULL DEFAULT '0' COMMENT '店铺ID'",
            'order_id' => "int(11) NOT NULL COMMENT '订单ID'",
            'order_sn' => "varchar(255) NOT NULL DEFAULT '' COMMENT '订单编号'",
            'order_product_id' => "int(11) NOT NULL COMMENT '订单项ID'",
            'product_pay_money' => "decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '订单项实际支付金额'",
            'rate' => "decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '商品平台佣金比率'",
            'merchant_money' => "decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '店铺获取金额'",
            'platform_money' => "decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '平台获取金额'",
            'is_refund' => "tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否产生退款'",
            'refund_money' => "decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '实际退款金额'",
            'merchant_refund_money' => "decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '店铺扣减余额'",
            'platform_refund_money' => "decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '平台扣减余额'",
            'is_issue' => "tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否已经结算'",
            'remark' => "varchar(255) NOT NULL DEFAULT '' COMMENT '备注'",
            'created_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '更新时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='店铺针对订单的金额分配'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_order_account_records}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

