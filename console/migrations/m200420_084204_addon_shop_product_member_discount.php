<?php

use yii\db\Migration;

class m200420_084204_addon_shop_product_member_discount extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_product_member_discount}}', [
            'id' => "int(11) NOT NULL AUTO_INCREMENT COMMENT '折扣id'",
            'member_level' => "int(11) NOT NULL DEFAULT '0' COMMENT '会员级别'",
            'product_id' => "int(11) NOT NULL DEFAULT '0' COMMENT '商品id'",
            'discount' => "int(2) NOT NULL DEFAULT '1' COMMENT '折扣'",
            'decimal_reservation_number' => "int(2) NOT NULL DEFAULT '2' COMMENT '价格保留方式 0 去掉角和分，1去掉分，2 保留角和分'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='商品会员折扣'");
        
        /* 索引设置 */
        $this->createIndex('product_id','{{%addon_shop_product_member_discount}}','product_id, member_level',0);
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_product_member_discount}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

