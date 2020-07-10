<?php

use yii\db\Migration;

class m200529_160729_addon_shop_base_notify_subscription_config extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_base_notify_subscription_config}}', [
            'member_id' => "int(10) unsigned NOT NULL COMMENT '用户id'",
            'app_id' => "varchar(50) NULL DEFAULT '' COMMENT '应用id'",
            'action' => "json NULL COMMENT '订阅事件'",
            'merchant_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '商户id'",
            'PRIMARY KEY (`member_id`)'
        ], "ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='系统_消息配置表'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_base_notify_subscription_config}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

