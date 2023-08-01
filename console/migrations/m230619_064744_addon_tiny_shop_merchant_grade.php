<?php

use yii\db\Migration;

class m230619_064744_addon_tiny_shop_merchant_grade extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_tiny_shop_merchant_grade}}', [
            'id' => "int(11) NOT NULL AUTO_INCREMENT",
            'merchant_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '商户id'",
            'comment_num' => "int(10) unsigned NOT NULL DEFAULT '0' COMMENT '评价数'",
            'match_credit' => "float NULL DEFAULT '5' COMMENT '描述相符度总分数'",
            'match_point' => "float(10,2) NULL DEFAULT '5' COMMENT '实物与描述相符（根据评价计算）'",
            'match_ratio' => "float(10,2) NULL DEFAULT '100' COMMENT '实物与描述相符（根据评价计算）百分比'",
            'service_credit' => "float NULL DEFAULT '5' COMMENT '服务态度总分数'",
            'service_ratio' => "float(10,2) NULL DEFAULT '100' COMMENT '服务态度（根据评价计算）百分比'",
            'service_point' => "float(10,2) NULL DEFAULT '5' COMMENT '服务态度（根据评价计算）'",
            'delivery_credit' => "float NULL DEFAULT '5' COMMENT '发货速度总分数'",
            'delivery_point' => "float(10,2) NULL DEFAULT '5' COMMENT '发货速度（根据评价计算）'",
            'delivery_ratio' => "float(10,2) NULL DEFAULT '100' COMMENT '发货速度（根据评价计算）百分比'",
            'synthesize_point' => "float(10,2) NULL DEFAULT '5' COMMENT '综合评分'",
            'status' => "tinyint(4) NULL DEFAULT '1' COMMENT '状态[-1:删除;0:禁用;1启用]'",
            'created_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '修改时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商户评分'");
        
        /* 索引设置 */
        $this->createIndex('merchant_id','{{%addon_tiny_shop_merchant_grade}}','merchant_id',0);
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_tiny_shop_merchant_grade}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

