<?php

use yii\db\Migration;

class m200529_160732_addon_shop_order_action extends Migration
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
            'member_name' => "varchar(50) NULL DEFAULT '' COMMENT '操作人'",
            'order_status' => "int(11) NOT NULL COMMENT '订单状态'",
            'order_status_text' => "varchar(200) NOT NULL DEFAULT '' COMMENT '订单状态名称'",
            'status' => "tinyint(4) NULL DEFAULT '1' COMMENT '状态[-1:删除;0:禁用;1启用]'",
            'created_at' => "int(10) NULL DEFAULT '0'",
            'updated_at' => "int(10) NULL DEFAULT '0'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='扩展_微商城_订单操作表'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        
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

