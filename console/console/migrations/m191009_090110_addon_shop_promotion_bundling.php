<?php

use yii\db\Migration;

class m191009_090110_addon_shop_promotion_bundling extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_promotion_bundling}}', [
            'bl_id' => "int(11) NOT NULL AUTO_INCREMENT COMMENT '组合ID'",
            'bl_name' => "varchar(50) NOT NULL COMMENT '组合名称'",
            'shop_id' => "int(11) NOT NULL COMMENT '店铺id'",
            'shop_name' => "varchar(100) NOT NULL COMMENT '店铺名称'",
            'bl_price' => "decimal(10,2) NOT NULL COMMENT '商品组合价格'",
            'shipping_fee_type' => "tinyint(1) NOT NULL COMMENT '运费承担方式 1卖家承担运费 2买家承担运费'",
            'shipping_fee' => "decimal(10,2) NOT NULL COMMENT '运费'",
            'status' => "tinyint(1) NOT NULL DEFAULT '1' COMMENT '组合状态 0-关闭/1-开启'",
            'PRIMARY KEY (`bl_id`)'
        ], "ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='组合销售活动表'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_promotion_bundling}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

