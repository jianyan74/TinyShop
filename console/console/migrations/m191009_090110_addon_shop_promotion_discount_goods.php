<?php

use yii\db\Migration;

class m191009_090110_addon_shop_promotion_discount_goods extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_promotion_discount_goods}}', [
            'discount_goods_id' => "int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键'",
            'discount_id' => "int(11) NOT NULL COMMENT '对应活动'",
            'goods_id' => "int(11) NOT NULL COMMENT '商品ID'",
            'status' => "tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态'",
            'discount' => "decimal(10,2) NOT NULL COMMENT '活动折扣或者减现信息'",
            'goods_name' => "varchar(255) NOT NULL DEFAULT '' COMMENT '商品名称'",
            'goods_picture' => "int(11) NOT NULL COMMENT '商品图片'",
            'start_time' => "int(11) NULL DEFAULT '0' COMMENT '开始时间'",
            'end_time' => "int(11) NULL DEFAULT '0' COMMENT '结束时间'",
            'decimal_reservation_number' => "int(2) NOT NULL DEFAULT '-1' COMMENT '价格保留方式 0去掉角和分 1去掉分'",
            'PRIMARY KEY (`discount_goods_id`)'
        ], "ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AVG_ROW_LENGTH=712 ROW_FORMAT=DYNAMIC COMMENT='限时折扣商品列表'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_promotion_discount_goods}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

