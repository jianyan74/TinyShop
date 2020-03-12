<?php

use yii\db\Migration;

class m191009_090110_addon_shop_promotion_discount extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_promotion_discount}}', [
            'discount_id' => "int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键'",
            'shop_id' => "int(11) NOT NULL DEFAULT '1' COMMENT '店铺ID'",
            'shop_name' => "varchar(50) NOT NULL DEFAULT '' COMMENT '店铺名称'",
            'discount_name' => "varchar(255) NOT NULL DEFAULT '' COMMENT '活动名称'",
            'status' => "tinyint(1) NOT NULL DEFAULT '0' COMMENT '活动状态(0-未发布/1-正常/3-关闭/4-结束)'",
            'remark' => "text NOT NULL COMMENT '备注'",
            'start_time' => "int(11) NULL DEFAULT '0' COMMENT '开始时间'",
            'end_time' => "int(11) NULL DEFAULT '0' COMMENT '结束时间'",
            'create_time' => "int(11) NULL DEFAULT '0' COMMENT '创建时间'",
            'modify_time' => "int(11) NULL DEFAULT '0' COMMENT '修改时间'",
            'decimal_reservation_number' => "int(2) NOT NULL DEFAULT '-1' COMMENT '价格保留方式 0去掉角和分 1去掉分'",
            'PRIMARY KEY (`discount_id`)'
        ], "ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AVG_ROW_LENGTH=4096 ROW_FORMAT=DYNAMIC COMMENT='限时折扣'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_promotion_discount}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

