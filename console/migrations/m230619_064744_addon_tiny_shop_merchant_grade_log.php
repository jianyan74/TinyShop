<?php

use yii\db\Migration;

class m230619_064744_addon_tiny_shop_merchant_grade_log extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_tiny_shop_merchant_grade_log}}', [
            'id' => "int(11) NOT NULL AUTO_INCREMENT",
            'merchant_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '商户id'",
            'member_id' => "int(11) NOT NULL COMMENT '用户id'",
            'map_id' => "int(11) unsigned NULL DEFAULT '0' COMMENT '关联id'",
            'match_point' => "float(10,2) NULL DEFAULT '5' COMMENT '实物与描述相符（根据评价计算）'",
            'service_point' => "float(10,2) NULL DEFAULT '5' COMMENT '服务态度（根据评价计算）'",
            'delivery_point' => "float(10,2) NULL DEFAULT '5' COMMENT '发货速度（根据评价计算）'",
            'status' => "tinyint(4) NULL DEFAULT '1' COMMENT '状态[-1:删除;0:禁用;1启用]'",
            'created_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '修改时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商户评分'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_tiny_shop_merchant_grade_log}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

