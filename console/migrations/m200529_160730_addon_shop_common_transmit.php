<?php

use yii\db\Migration;

class m200529_160730_addon_shop_common_transmit extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_common_transmit}}', [
            'id' => "bigint(20) NOT NULL AUTO_INCREMENT",
            'merchant_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '商户id'",
            'member_id' => "int(10) NOT NULL DEFAULT '0' COMMENT '用户id'",
            'topic_id' => "int(10) NOT NULL DEFAULT '0' COMMENT '主题id'",
            'topic_type' => "varchar(50) NULL DEFAULT '1' COMMENT '主题类型'",
            'status' => "tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态[-1:删除;0:禁用;1启用]'",
            'created_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '修改时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COMMENT='扩展_微商城_转发'");
        
        /* 索引设置 */
        $this->createIndex('member_id','{{%addon_shop_common_transmit}}','member_id, topic_id, topic_type',0);
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_common_transmit}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

