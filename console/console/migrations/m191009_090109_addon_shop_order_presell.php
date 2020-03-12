<?php

use yii\db\Migration;

class m191009_090109_addon_shop_order_presell extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_order_presell}}', [
            'presell_order_id' => "int(11) NOT NULL AUTO_INCREMENT COMMENT '订单id'",
            'out_trade_no' => "varchar(100) NOT NULL DEFAULT '0' COMMENT '外部交易号'",
            'payment_type' => "tinyint(4) NOT NULL DEFAULT '0' COMMENT '支付类型'",
            'order_status' => "tinyint(4) NOT NULL DEFAULT '0' COMMENT '订单状态 0创建 1尾款待支付 2开始结尾款 '",
            'pay_time' => "int(11) NOT NULL DEFAULT '0' COMMENT '订单付款时间'",
            'create_time' => "int(11) NOT NULL DEFAULT '0' COMMENT '订单创建时间'",
            'operator_type' => "int(1) NOT NULL DEFAULT '0' COMMENT '操作人类型  1店铺  2用户'",
            'operator_id' => "int(11) NOT NULL DEFAULT '0' COMMENT '操作人id'",
            'relate_id' => "int(11) NOT NULL DEFAULT '0' COMMENT '关联id'",
            'presell_time' => "int(11) NOT NULL DEFAULT '0' COMMENT '预售结束时间'",
            'presell_money' => "decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '预售金额'",
            'presell_pay' => "decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '预售支付金额'",
            'platform_money' => "decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '平台余额'",
            'point' => "decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '订单消耗积分'",
            'point_money' => "decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '订单消耗积分抵多少钱'",
            'presell_price' => "decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '预售金单价'",
            'presell_delivery_type' => "int(11) NOT NULL DEFAULT '0' COMMENT '预售发货形式 1指定时间 2支付后天数'",
            'presell_delivery_value' => "int(11) NOT NULL DEFAULT '0' COMMENT '预售发货时间 按形式 '",
            'presell_delivery_time' => "int(11) NOT NULL DEFAULT '0' COMMENT '预售发货具体时间（实则为结尾款时间）'",
            'is_full_payment' => "int(11) NOT NULL DEFAULT '0' COMMENT '是否全款预定'",
            'PRIMARY KEY (`presell_order_id`)'
        ], "ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='预售订单表'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_order_presell}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

