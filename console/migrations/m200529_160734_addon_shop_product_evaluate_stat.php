<?php

use yii\db\Migration;

class m200529_160734_addon_shop_product_evaluate_stat extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_product_evaluate_stat}}', [
            'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
            'merchant_id' => "int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商户id'",
            'product_id' => "int(11) NULL COMMENT '商品ID'",
            'cover_num' => "int(11) unsigned NULL DEFAULT '0' COMMENT '有图数量'",
            'video_num' => "int(11) NULL DEFAULT '0' COMMENT '视频数量'",
            'again_num' => "int(11) unsigned NULL DEFAULT '0' COMMENT '追加数量'",
            'good_num' => "int(11) NULL DEFAULT '0' COMMENT '好评数量'",
            'ordinary_num' => "int(11) NULL DEFAULT '0' COMMENT '中评数量'",
            'negative_num' => "int(11) NULL DEFAULT '0' COMMENT '差评数量'",
            'total_num' => "int(11) NULL DEFAULT '0'",
            'tags' => "json NULL COMMENT '其他标签'",
            'status' => "tinyint(4) NULL DEFAULT '1' COMMENT '状态'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4");
        
        /* 索引设置 */
        $this->createIndex('merchant_id','{{%addon_shop_product_evaluate_stat}}','merchant_id, product_id',0);
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_product_evaluate_stat}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

