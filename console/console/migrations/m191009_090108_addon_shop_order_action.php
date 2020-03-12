<?php

use yii\db\Migration;

class m191009_090108_addon_shop_order_action extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_order_action}}', [
            'id' => "int(11) NOT NULL AUTO_INCREMENT COMMENT '动作id'",
            'order_id' => "int(11) NOT NULL COMMENT '订单id'",
            'action' => "varchar(200) NOT NULL DEFAULT '' COMMENT '动作内容'",
            'member_id' => "int(11) NOT NULL DEFAULT '0' COMMENT '操作人id'",
            'member_name' => "varchar(50) NOT NULL DEFAULT '' COMMENT '操作人'",
            'order_status' => "int(11) NOT NULL COMMENT '订单状态'",
            'order_status_text' => "varchar(200) NOT NULL DEFAULT '' COMMENT '订单状态名称'",
            'status' => "tinyint(4) NULL DEFAULT '1' COMMENT '状态[-1:删除;0:禁用;1启用]'",
            'created_at' => "int(10) NULL DEFAULT '0'",
            'updated_at' => "int(10) NULL DEFAULT '0'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AVG_ROW_LENGTH=1706 ROW_FORMAT=DYNAMIC COMMENT='订单操作表'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        $this->insert('{{%addon_shop_order_action}}',['id'=>'77','order_id'=>'203','action'=>'创建订单','member_id'=>'1','member_name'=>'admin','order_status'=>'0','order_status_text'=>'待付款','status'=>'1','created_at'=>'1570607895','updated_at'=>'1570607895']);
        $this->insert('{{%addon_shop_order_action}}',['id'=>'78','order_id'=>'204','action'=>'创建订单','member_id'=>'1','member_name'=>'admin','order_status'=>'0','order_status_text'=>'待付款','status'=>'1','created_at'=>'1570609330','updated_at'=>'1570609330']);
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_order_action}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

