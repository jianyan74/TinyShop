<?php

use yii\db\Migration;

class m191009_090109_addon_shop_order_refund extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_order_refund}}', [
            'id' => "int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id'",
            'order_goods_id' => "int(11) NOT NULL COMMENT '订单商品表id'",
            'refund_status' => "varchar(255) NOT NULL COMMENT '操作状态 流程状态(refund_status) 状态名称(refund_status_name)  操作时间1 买家申请  发起了退款申请,等待卖家处理2 等待买家退货  卖家已同意退款申请,等待买家退货3 等待卖家确认收货  买家已退货,等待卖家确认收货4 等待卖家确认退款  卖家同意退款'",
            'action' => "varchar(255) NOT NULL COMMENT '退款操作内容描述'",
            'action_way' => "tinyint(4) NOT NULL DEFAULT '0' COMMENT '操作方 1 买家 2 卖家'",
            'action_userid' => "int(10) NOT NULL DEFAULT '0' COMMENT '操作人id'",
            'action_username' => "varchar(255) NOT NULL DEFAULT '' COMMENT '操作人姓名'",
            'action_time' => "int(11) NULL DEFAULT '0' COMMENT '操作时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AVG_ROW_LENGTH=108 ROW_FORMAT=DYNAMIC COMMENT='订单商品退货退款操作表'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_order_refund}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

