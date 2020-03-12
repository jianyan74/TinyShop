<?php

use yii\db\Migration;

class m191009_090110_addon_shop_product_cate extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_product_cate}}', [
            'id' => "int(11) NOT NULL AUTO_INCREMENT COMMENT '主键'",
            'merchant_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '商户id'",
            'title' => "varchar(50) NOT NULL DEFAULT '' COMMENT '标题'",
            'cover' => "varchar(255) NULL COMMENT '封面图'",
            'sort' => "int(5) NULL DEFAULT '0' COMMENT '排序'",
            'level' => "tinyint(1) NULL DEFAULT '1' COMMENT '级别'",
            'pid' => "int(50) NULL DEFAULT '0' COMMENT '上级id'",
            'tree' => "text NULL COMMENT '树'",
            'index_block_status' => "tinyint(4) NOT NULL DEFAULT '0' COMMENT '首页块级状态 1=>显示'",
            'status' => "tinyint(4) NULL DEFAULT '1' COMMENT '状态[-1:删除;0:禁用;1启用]'",
            'created_at' => "int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='扩展_微商城_商品_分类表'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        $this->insert('{{%addon_shop_product_cate}}',['id'=>'6','merchant_id'=>'1','title'=>'衣服','cover'=>'http://merchants.local/attachment/images/2019/07/19/image_156352842899989850.jpg','sort'=>'0','level'=>'1','pid'=>'0','tree'=>'tr_0 ','index_block_status'=>'0','status'=>'1','created_at'=>'1557467897','updated_at'=>'1563530205']);
        $this->insert('{{%addon_shop_product_cate}}',['id'=>'7','merchant_id'=>'1','title'=>'毛衣','cover'=>NULL,'sort'=>'0','level'=>'2','pid'=>'6','tree'=>'tr_0 tr_6 ','index_block_status'=>'0','status'=>'1','created_at'=>'1560648228','updated_at'=>'1563529207']);
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_product_cate}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

