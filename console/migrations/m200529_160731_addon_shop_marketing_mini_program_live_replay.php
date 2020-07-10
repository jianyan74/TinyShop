<?php

use yii\db\Migration;

class m200529_160731_addon_shop_marketing_mini_program_live_replay extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_marketing_mini_program_live_replay}}', [
            'id' => "int(11) NOT NULL AUTO_INCREMENT COMMENT 'id'",
            'merchant_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '商户id'",
            'live_id' => "int(11) NULL DEFAULT '0' COMMENT '关联id'",
            'media_url' => "varchar(100) NULL DEFAULT '' COMMENT '回放视频'",
            'expire_time' => "int(10) NULL DEFAULT '0' COMMENT '回放视频 url 过期时间'",
            'create_time' => "int(10) NULL DEFAULT '0' COMMENT '回放视频创建时间'",
            'status' => "tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态[-1:删除;0:禁用;1启用]'",
            'created_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '修改时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='组合销售活动商品表'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_marketing_mini_program_live_replay}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

