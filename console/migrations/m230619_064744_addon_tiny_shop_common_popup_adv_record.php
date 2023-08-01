<?php

use yii\db\Migration;

class m230619_064744_addon_tiny_shop_common_popup_adv_record extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_tiny_shop_common_popup_adv_record}}', [
            'id' => "int(11) NOT NULL AUTO_INCREMENT COMMENT '序号'",
            'member_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '用户id'",
            'merchant_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '商户id'",
            'popup_adv_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '弹出广告id'",
            'ip' => "varchar(50) NULL DEFAULT '' COMMENT 'ip地址'",
            'device_id' => "varchar(64) NULL DEFAULT '' COMMENT '设备ID'",
            'created_at' => "int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COMMENT='扩展_文章_幻灯片表'");
        
        /* 索引设置 */
        $this->createIndex('member_id','{{%addon_tiny_shop_common_popup_adv_record}}','member_id, popup_adv_id',0);
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_tiny_shop_common_popup_adv_record}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

