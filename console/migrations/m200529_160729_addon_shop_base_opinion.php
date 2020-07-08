<?php

use yii\db\Migration;

class m200529_160729_addon_shop_base_opinion extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_base_opinion}}', [
            'id' => "int(11) NOT NULL AUTO_INCREMENT",
            'member_id' => "int(10) NOT NULL DEFAULT '0' COMMENT '用户id'",
            'merchant_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '商户id'",
            'content' => "text NOT NULL COMMENT '内容'",
            'covers' => "json NULL COMMENT '反馈图片'",
            'contact_way' => "varchar(100) NULL DEFAULT '' COMMENT '联系方式'",
            'reply' => "varchar(200) NULL DEFAULT '' COMMENT '回复'",
            'type' => "tinyint(4) NULL DEFAULT '1' COMMENT '反馈类型'",
            'from' => "varchar(200) NULL DEFAULT '' COMMENT '来源'",
            'sort' => "int(5) NULL DEFAULT '0' COMMENT '优先级（0-9）'",
            'status' => "tinyint(4) NULL DEFAULT '1' COMMENT '状态[-1:删除;0:禁用;1启用]'",
            'created_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '更新时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='扩展_微商城_意见反馈'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_base_opinion}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

