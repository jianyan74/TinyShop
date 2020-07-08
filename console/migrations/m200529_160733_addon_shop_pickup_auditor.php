<?php

use yii\db\Migration;

class m200529_160733_addon_shop_pickup_auditor extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_pickup_auditor}}', [
            'id' => "int(11) NOT NULL AUTO_INCREMENT COMMENT '审核人id'",
            'merchant_id' => "int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商户id'",
            'member_id' => "int(11) NOT NULL COMMENT '用户id'",
            'pickup_point_id' => "int(11) NOT NULL DEFAULT '0' COMMENT '自提点门店id'",
            'status' => "tinyint(4) NULL DEFAULT '1' COMMENT '状态'",
            'created_at' => "int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='扩展_微商城_自提门店审核人表'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_pickup_auditor}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

