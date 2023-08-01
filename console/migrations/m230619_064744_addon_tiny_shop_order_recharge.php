<?php

use yii\db\Migration;

class m230619_064744_addon_tiny_shop_order_recharge extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_tiny_shop_order_recharge}}', [
            'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID'",
            'merchant_id' => "int(11) unsigned NULL DEFAULT '0' COMMENT '商户ID'",
            'store_id' => "int(11) unsigned NULL DEFAULT '0' COMMENT '店铺ID'",
            'member_id' => "int(11) unsigned NULL DEFAULT '0' COMMENT '用户ID'",
            'order_sn' => "varchar(30) NOT NULL DEFAULT '' COMMENT '订单编号'",
            'out_trade_no' => "varchar(30) NULL DEFAULT '' COMMENT '外部交易号'",
            'price' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '价格'",
            'give_price' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '赠送金额'",
            'give_point' => "int(11) NULL DEFAULT '0' COMMENT '送积分数量（0表示不送）'",
            'give_growth' => "int(11) NULL DEFAULT '0' COMMENT '赠送成长值'",
            'give_coupon_type_ids' => "json NULL COMMENT '优惠券'",
            'pay_type' => "int(10) NULL DEFAULT '0' COMMENT '支付类型
'",
            'pay_status' => "tinyint(4) NULL DEFAULT '0' COMMENT '订单付款状态'",
            'pay_time' => "int(11) NULL DEFAULT '0' COMMENT '订单付款时间'",
            'status' => "int(11) NULL DEFAULT '1' COMMENT '状态[-1:删除;0:禁用;1启用]'",
            'created_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '修改时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='扩展_微商城_订单_会员卡'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_tiny_shop_order_recharge}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

