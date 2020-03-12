<?php

use yii\db\Migration;

class m200311_140537_addon_shop_product_ladder_preferential extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_product_ladder_preferential}}', [
            'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键'",
            'product_id' => "int(11) NOT NULL DEFAULT '0' COMMENT '商品关联id'",
            'type' => "tinyint(4) NULL DEFAULT '1' COMMENT '优惠类型'",
            'quantity' => "int(11) NOT NULL DEFAULT '0' COMMENT '数量'",
            'price' => "decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '优惠价格'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='扩展_微商城_商品阶梯优惠'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_product_ladder_preferential}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

