<?php

use yii\db\Migration;

class m200529_160732_addon_shop_order_delivery extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_order_delivery}}', [
            'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
            'express_no' => "varchar(255) NOT NULL DEFAULT '' COMMENT '订单编号'",
            'order_id' => "int(11) NOT NULL DEFAULT '0' COMMENT '订单id'",
            'order_delivery_user_id' => "int(11) NOT NULL DEFAULT '0' COMMENT '配送人员id'",
            'order_delivery_user_name' => "varchar(255) NOT NULL DEFAULT '' COMMENT '配送人员姓名'",
            'order_delivery_user_mobile' => "varchar(255) NOT NULL DEFAULT '' COMMENT '配送人员电话'",
            'status' => "int(11) NOT NULL DEFAULT '0' COMMENT '状态'",
            'remark' => "text NOT NULL COMMENT '备注'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='o2o订单配送'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_order_delivery}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

