<?php

use yii\db\Migration;

class m200529_160731_addon_shop_marketing_mini_program_live extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_marketing_mini_program_live}}', [
            'id' => "int(11) NOT NULL AUTO_INCREMENT COMMENT '组合商品id'",
            'merchant_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '商户id'",
            'name' => "varchar(200) NULL DEFAULT '' COMMENT '直播房间名'",
            'room_id' => "int(11) NULL DEFAULT '0' COMMENT '房间ID'",
            'cover' => "varchar(200) NULL DEFAULT '' COMMENT '直播封面'",
            'live_status' => "tinyint(4) NULL DEFAULT '0' COMMENT '直播状态'",
            'start_time' => "int(11) NULL DEFAULT '0' COMMENT '开始时间'",
            'end_time' => "int(11) NULL DEFAULT '0' COMMENT '结束时间'",
            'anchor_name' => "varchar(200) NULL DEFAULT '' COMMENT '主播名称'",
            'share_img' => "varchar(200) NULL DEFAULT '' COMMENT '分享卡片封面'",
            'is_recommend' => "tinyint(4) NULL DEFAULT '0' COMMENT '是否推荐'",
            'is_stick' => "tinyint(4) NULL DEFAULT '0' COMMENT '是否置顶'",
            'status' => "tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态[-1:删除;0:禁用;1启用]'",
            'created_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '修改时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='组合销售活动商品表'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_marketing_mini_program_live}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

