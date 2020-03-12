<?php

use yii\db\Migration;

class m191009_090110_addon_shop_product_spec_value extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_product_spec_value}}', [
            'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
            'merchant_id' => "int(10) NULL DEFAULT '0' COMMENT '商户id'",
            'product_id' => "int(11) NOT NULL COMMENT '商品编码'",
            'base_spec_id' => "int(11) unsigned NOT NULL DEFAULT '0' COMMENT '系统规格id'",
            'base_spec_value_id' => "int(11) unsigned NOT NULL DEFAULT '0' COMMENT '系统规格值id'",
            'title' => "varchar(125) NULL DEFAULT '' COMMENT '属性标题'",
            'data' => "varchar(125) NULL DEFAULT '' COMMENT '属性值例如颜色'",
            'sort' => "int(11) NOT NULL DEFAULT '999' COMMENT '排序'",
            'status' => "tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态(-1:已删除,0:禁用,1:正常)'",
            'created_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '修改时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='扩展_微商城_商品规格属性表'");
        
        /* 索引设置 */
        $this->createIndex('product_attribute_and_option_sku_id_option_id_attribute_id_index','{{%addon_shop_product_spec_value}}','base_spec_value_id, base_spec_id',0);
        
        
        /* 表数据 */
        $this->insert('{{%addon_shop_product_spec_value}}',['id'=>'1','merchant_id'=>'1','product_id'=>'12','base_spec_id'=>'12','base_spec_value_id'=>'51','title'=>'青春','data'=>'http://merchants.local/attachment/images/2019/05/10/image_155746806110056981.png','sort'=>'0','status'=>'1','created_at'=>'0','updated_at'=>'0']);
        $this->insert('{{%addon_shop_product_spec_value}}',['id'=>'2','merchant_id'=>'1','product_id'=>'12','base_spec_id'=>'12','base_spec_value_id'=>'50','title'=>'可爱','data'=>'http://merchants.local/attachment/images/2019/05/08/image_155729450052485251.jpg','sort'=>'1','status'=>'1','created_at'=>'0','updated_at'=>'0']);
        $this->insert('{{%addon_shop_product_spec_value}}',['id'=>'3','merchant_id'=>'1','product_id'=>'12','base_spec_id'=>'11','base_spec_value_id'=>'47','title'=>'大','data'=>'','sort'=>'2','status'=>'1','created_at'=>'0','updated_at'=>'0']);
        $this->insert('{{%addon_shop_product_spec_value}}',['id'=>'4','merchant_id'=>'1','product_id'=>'12','base_spec_id'=>'11','base_spec_value_id'=>'48','title'=>'中','data'=>'','sort'=>'3','status'=>'1','created_at'=>'0','updated_at'=>'0']);
        $this->insert('{{%addon_shop_product_spec_value}}',['id'=>'5','merchant_id'=>'1','product_id'=>'12','base_spec_id'=>'11','base_spec_value_id'=>'49','title'=>'小','data'=>'','sort'=>'4','status'=>'1','created_at'=>'0','updated_at'=>'0']);
        $this->insert('{{%addon_shop_product_spec_value}}',['id'=>'6','merchant_id'=>'1','product_id'=>'12','base_spec_id'=>'10','base_spec_value_id'=>'44','title'=>'红2','data'=>'#a61c00','sort'=>'5','status'=>'1','created_at'=>'0','updated_at'=>'0']);
        $this->insert('{{%addon_shop_product_spec_value}}',['id'=>'7','merchant_id'=>'1','product_id'=>'12','base_spec_id'=>'10','base_spec_value_id'=>'45','title'=>'黄','data'=>'#f9cb9c','sort'=>'6','status'=>'1','created_at'=>'0','updated_at'=>'0']);
        $this->insert('{{%addon_shop_product_spec_value}}',['id'=>'8','merchant_id'=>'1','product_id'=>'12','base_spec_id'=>'10','base_spec_value_id'=>'46','title'=>'蓝','data'=>'#4a86e8','sort'=>'7','status'=>'1','created_at'=>'0','updated_at'=>'0']);
        $this->insert('{{%addon_shop_product_spec_value}}',['id'=>'32','merchant_id'=>'1','product_id'=>'21','base_spec_id'=>'12','base_spec_value_id'=>'50','title'=>'可爱','data'=>'http://merchants.local/attachment/images/2019/05/08/image_155729450052485251.jpg','sort'=>'0','status'=>'1','created_at'=>'0','updated_at'=>'1557732908']);
        $this->insert('{{%addon_shop_product_spec_value}}',['id'=>'33','merchant_id'=>'1','product_id'=>'21','base_spec_id'=>'11','base_spec_value_id'=>'47','title'=>'大','data'=>'','sort'=>'2','status'=>'1','created_at'=>'0','updated_at'=>'1570604719']);
        $this->insert('{{%addon_shop_product_spec_value}}',['id'=>'34','merchant_id'=>'1','product_id'=>'21','base_spec_id'=>'10','base_spec_value_id'=>'44','title'=>'红','data'=>'#a61c00','sort'=>'5','status'=>'1','created_at'=>'0','updated_at'=>'1570604719']);
        $this->insert('{{%addon_shop_product_spec_value}}',['id'=>'35','merchant_id'=>'1','product_id'=>'21','base_spec_id'=>'11','base_spec_value_id'=>'48','title'=>'中','data'=>'','sort'=>'3','status'=>'1','created_at'=>'0','updated_at'=>'1570604719']);
        $this->insert('{{%addon_shop_product_spec_value}}',['id'=>'36','merchant_id'=>'1','product_id'=>'21','base_spec_id'=>'10','base_spec_value_id'=>'45','title'=>'黄','data'=>'#ff9900','sort'=>'6','status'=>'1','created_at'=>'0','updated_at'=>'1570604719']);
        $this->insert('{{%addon_shop_product_spec_value}}',['id'=>'37','merchant_id'=>'1','product_id'=>'21','base_spec_id'=>'11','base_spec_value_id'=>'49','title'=>'小','data'=>'','sort'=>'4','status'=>'1','created_at'=>'0','updated_at'=>'1570604719']);
        $this->insert('{{%addon_shop_product_spec_value}}',['id'=>'38','merchant_id'=>'1','product_id'=>'21','base_spec_id'=>'12','base_spec_value_id'=>'51','title'=>'青春','data'=>'','sort'=>'1','status'=>'1','created_at'=>'0','updated_at'=>'0']);
        $this->insert('{{%addon_shop_product_spec_value}}',['id'=>'39','merchant_id'=>'1','product_id'=>'21','base_spec_id'=>'10','base_spec_value_id'=>'46','title'=>'蓝','data'=>'#','sort'=>'7','status'=>'1','created_at'=>'0','updated_at'=>'0']);
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_product_spec_value}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

