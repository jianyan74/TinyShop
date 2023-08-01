<?php

use yii\db\Migration;

class m230619_064744_addon_tiny_shop_marketing_cate extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_tiny_shop_marketing_cate}}', [
            'id' => "int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键'",
            'merchant_id' => "int(11) NULL DEFAULT '0' COMMENT '店铺ID'",
            'cate_id' => "int(11) unsigned NULL DEFAULT '0' COMMENT '分类ID'",
            'marketing_id' => "int(11) unsigned NULL DEFAULT '0' COMMENT '对应活动'",
            'marketing_type' => "varchar(60) NULL DEFAULT '' COMMENT '活动类型'",
            'prediction_time' => "int(11) unsigned NULL DEFAULT '0' COMMENT '预告时间'",
            'start_time' => "int(11) unsigned NULL DEFAULT '0' COMMENT '开始时间'",
            'end_time' => "int(11) unsigned NULL DEFAULT '0' COMMENT '结束时间'",
            'status' => "tinyint(4) NULL DEFAULT '0' COMMENT '状态'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='营销_关联分类'");
        
        /* 索引设置 */
        $this->createIndex('product_id','{{%addon_tiny_shop_marketing_cate}}','cate_id',0);
        $this->createIndex('start_time','{{%addon_tiny_shop_marketing_cate}}','start_time, end_time',0);
        $this->createIndex('merchant_id','{{%addon_tiny_shop_marketing_cate}}','merchant_id',0);
        $this->createIndex('marketing_type','{{%addon_tiny_shop_marketing_cate}}','marketing_id, marketing_type, status',0);
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_tiny_shop_marketing_cate}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

