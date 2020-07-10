<?php

use yii\db\Migration;

class m200529_160732_addon_shop_member_footprint extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_member_footprint}}', [
            'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
            'merchant_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '商户id'",
            'product_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '产品id'",
            'member_id' => "int(11) NULL DEFAULT '0' COMMENT '用户id'",
            'cate_id' => "int(11) NULL DEFAULT '0' COMMENT '商品分类'",
            'num' => "int(10) unsigned NULL DEFAULT '0' COMMENT '浏览次数'",
            'status' => "tinyint(4) NULL DEFAULT '1' COMMENT '状态'",
            'created_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '更新时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COMMENT='扩展_微商城_足迹'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_member_footprint}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

