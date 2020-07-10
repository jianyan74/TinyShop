<?php

use yii\db\Migration;

class m200608_090445_addon_shop_product_cate_map extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_product_cate_map}}', [
            'cate_id' => "int(10) NULL DEFAULT '0' COMMENT '分类id'",
            'product_id' => "int(10) NULL DEFAULT '0' COMMENT '产品id'",
        ], "ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='扩展_微商城产品分类关联表'");
        
        /* 索引设置 */
        $this->createIndex('tag_id','{{%addon_shop_product_cate_map}}','cate_id',0);
        $this->createIndex('article_id','{{%addon_shop_product_cate_map}}','product_id',0);
        
        
        /* 表数据 */
        $this->insert('{{%addon_shop_product_cate_map}}',['cate_id'=>'161','product_id'=>'89']);
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_product_cate_map}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

