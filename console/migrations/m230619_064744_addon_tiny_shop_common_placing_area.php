<?php

use yii\db\Migration;

class m230619_064744_addon_tiny_shop_common_placing_area extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_tiny_shop_common_placing_area}}', [
            'id' => "int(11) NOT NULL AUTO_INCREMENT",
            'merchant_id' => "int(11) NOT NULL DEFAULT '0' COMMENT '店铺id'",
            'no_placing_province_ids' => "text NULL COMMENT '不支持下单省id组'",
            'no_placing_city_ids' => "text NULL COMMENT '不支持下单市id组'",
            'no_placing_area_ids' => "text NULL COMMENT '不支持下单区id组'",
            'status' => "tinyint(4) NULL DEFAULT '0' COMMENT '状态[-1:删除;0:禁用;1启用]'",
            'created_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '添加时间'",
            'updated_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '修改时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='扩展_微商城_不支持下单区域'");
        
        /* 索引设置 */
        $this->createIndex('merchant_id','{{%addon_tiny_shop_common_placing_area}}','merchant_id',0);
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_tiny_shop_common_placing_area}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

