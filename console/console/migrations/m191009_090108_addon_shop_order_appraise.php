<?php

use yii\db\Migration;

class m191009_090108_addon_shop_order_appraise extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_order_appraise}}', [
            'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
            'merchant_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '商户id'",
            'member_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '用户'",
            'product_id' => "int(255) unsigned NULL DEFAULT '0' COMMENT '商品id'",
            'order_id' => "int(11) NOT NULL COMMENT '订单编码'",
            'info' => "text NULL COMMENT '评论内容'",
            'level' => "tinyint(4) NOT NULL COMMENT '级别 -1差评 0中评 1好评'",
            'desc_star' => "tinyint(4) NOT NULL COMMENT '描述相符 1-5'",
            'logistics_star' => "tinyint(4) NOT NULL COMMENT '物流服务 1-5'",
            'attitude_star' => "tinyint(4) NOT NULL COMMENT '服务态度 1-5'",
            'status' => "tinyint(4) NULL DEFAULT '1' COMMENT '状态'",
            'created_at' => "int(11) NULL DEFAULT '0'",
            'updated_at' => "int(11) NULL DEFAULT '0'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='扩展_微商城_订单评论表'");
        
        /* 索引设置 */
        $this->createIndex('order_appraise_order_id_index','{{%addon_shop_order_appraise}}','order_id',0);
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_order_appraise}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

