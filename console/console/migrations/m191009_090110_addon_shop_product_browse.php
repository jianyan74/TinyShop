<?php

use yii\db\Migration;

class m191009_090110_addon_shop_product_browse extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_product_browse}}', [
            'id' => "int(11) NOT NULL AUTO_INCREMENT",
            'product_id' => "int(11) NOT NULL DEFAULT '0' COMMENT '商品id'",
            'member_id' => "int(11) NOT NULL DEFAULT '0' COMMENT '用户id'",
            'cate_id' => "int(11) NOT NULL DEFAULT '0' COMMENT '分类id'",
            'status' => "tinyint(4) NULL DEFAULT '1' COMMENT '状态[-1:删除;0:禁用;1启用]'",
            'created_at' => "int(10) NULL DEFAULT '0'",
            'updated_at' => "int(10) NULL DEFAULT '0'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='商品足迹表'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_product_browse}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

