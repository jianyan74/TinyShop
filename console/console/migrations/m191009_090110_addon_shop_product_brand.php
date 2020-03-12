<?php

use yii\db\Migration;

class m191009_090110_addon_shop_product_brand extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_product_brand}}', [
            'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
            'merchant_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '商户id'",
            'cate_id' => "int(11) NULL DEFAULT '0' COMMENT '商品类别编号'",
            'title' => "varchar(25) NOT NULL DEFAULT '' COMMENT '品牌名称'",
            'cover' => "varchar(125) NOT NULL DEFAULT '' COMMENT '图片url'",
            'sort' => "int(11) NOT NULL DEFAULT '999' COMMENT '排列次序'",
            'status' => "tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态'",
            'created_at' => "int(10) NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) NULL DEFAULT '0' COMMENT '修改时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='扩展_微商城_商品_品牌表'");
        
        /* 索引设置 */
        $this->createIndex('product_brand_name_unique','{{%addon_shop_product_brand}}','title',1);
        
        
        /* 表数据 */
        $this->insert('{{%addon_shop_product_brand}}',['id'=>'10','merchant_id'=>'1','cate_id'=>'0','title'=>'美特斯邦威','cover'=>'','sort'=>'1','status'=>'1','created_at'=>'1557467656','updated_at'=>'1564451181']);
        $this->insert('{{%addon_shop_product_brand}}',['id'=>'11','merchant_id'=>'1','cate_id'=>'0','title'=>'优衣库','cover'=>'','sort'=>'999','status'=>'1','created_at'=>'1557467665','updated_at'=>'1557467665']);
        $this->insert('{{%addon_shop_product_brand}}',['id'=>'12','merchant_id'=>'1','cate_id'=>'0','title'=>'太平鸟','cover'=>'','sort'=>'999','status'=>'1','created_at'=>'1557467673','updated_at'=>'1557467673']);
        $this->insert('{{%addon_shop_product_brand}}',['id'=>'13','merchant_id'=>'1','cate_id'=>'0','title'=>'罗蒙','cover'=>'','sort'=>'999','status'=>'1','created_at'=>'1557467680','updated_at'=>'1557467680']);
        $this->insert('{{%addon_shop_product_brand}}',['id'=>'14','merchant_id'=>'1','cate_id'=>'0','title'=>'GXG','cover'=>'','sort'=>'999','status'=>'1','created_at'=>'1557467688','updated_at'=>'1557467688']);
        $this->insert('{{%addon_shop_product_brand}}',['id'=>'15','merchant_id'=>'1','cate_id'=>'0','title'=>'CK','cover'=>'','sort'=>'999','status'=>'1','created_at'=>'1557467695','updated_at'=>'1557467695']);
        $this->insert('{{%addon_shop_product_brand}}',['id'=>'16','merchant_id'=>'1','cate_id'=>'0','title'=>'森马','cover'=>'','sort'=>'999','status'=>'1','created_at'=>'1557467709','updated_at'=>'1557467709']);
        $this->insert('{{%addon_shop_product_brand}}',['id'=>'17','merchant_id'=>'1','cate_id'=>'0','title'=>'鸿星尔克','cover'=>'','sort'=>'999','status'=>'1','created_at'=>'1557467717','updated_at'=>'1557467717']);
        $this->insert('{{%addon_shop_product_brand}}',['id'=>'18','merchant_id'=>'1','cate_id'=>'0','title'=>'VII','cover'=>'','sort'=>'999','status'=>'1','created_at'=>'1557467723','updated_at'=>'1557467723']);
        $this->insert('{{%addon_shop_product_brand}}',['id'=>'19','merchant_id'=>'1','cate_id'=>'0','title'=>'雅戈尔','cover'=>'','sort'=>'999','status'=>'1','created_at'=>'1557467734','updated_at'=>'1557467734']);
        $this->insert('{{%addon_shop_product_brand}}',['id'=>'20','merchant_id'=>'1','cate_id'=>'0','title'=>'海澜之家','cover'=>'','sort'=>'999','status'=>'1','created_at'=>'1557467763','updated_at'=>'1557467763']);
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_product_brand}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

