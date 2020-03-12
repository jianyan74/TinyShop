<?php

use yii\db\Migration;

class m191009_090110_addon_shop_product_spec extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_product_spec}}', [
            'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
            'merchant_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '商户id'",
            'product_id' => "int(11) NOT NULL COMMENT '商品编码'",
            'base_spec_id' => "int(11) NOT NULL DEFAULT '0' COMMENT '系统规格id'",
            'title' => "varchar(125) NOT NULL DEFAULT '' COMMENT '规格名称'",
            'sort' => "int(11) NOT NULL DEFAULT '999' COMMENT '排序'",
            'show_type' => "tinyint(4) NULL DEFAULT '1' COMMENT '展示方式 1 文字 2 颜色 3 图片'",
            'status' => "tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态(-1:已删除,0:禁用,1:正常)'",
            'created_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '修改时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='扩展_微商城_商品_自定义属性表'");
        
        /* 索引设置 */
        $this->createIndex('product_supplier_attribute_name_product_id_index','{{%addon_shop_product_spec}}','title, product_id',0);
        
        
        /* 表数据 */
        $this->insert('{{%addon_shop_product_spec}}',['id'=>'1','merchant_id'=>'1','product_id'=>'12','base_spec_id'=>'12','title'=>'风格','sort'=>'0','show_type'=>'3','status'=>'1','created_at'=>'0','updated_at'=>'1557715854']);
        $this->insert('{{%addon_shop_product_spec}}',['id'=>'2','merchant_id'=>'1','product_id'=>'12','base_spec_id'=>'11','title'=>'尺寸','sort'=>'1','show_type'=>'1','status'=>'1','created_at'=>'0','updated_at'=>'1557715854']);
        $this->insert('{{%addon_shop_product_spec}}',['id'=>'3','merchant_id'=>'1','product_id'=>'12','base_spec_id'=>'10','title'=>'颜色','sort'=>'2','show_type'=>'2','status'=>'1','created_at'=>'0','updated_at'=>'1557715854']);
        $this->insert('{{%addon_shop_product_spec}}',['id'=>'10','merchant_id'=>'1','product_id'=>'21','base_spec_id'=>'12','title'=>'风格','sort'=>'0','show_type'=>'3','status'=>'1','created_at'=>'0','updated_at'=>'1570604763']);
        $this->insert('{{%addon_shop_product_spec}}',['id'=>'11','merchant_id'=>'1','product_id'=>'21','base_spec_id'=>'11','title'=>'尺寸','sort'=>'1','show_type'=>'1','status'=>'1','created_at'=>'0','updated_at'=>'1570604763']);
        $this->insert('{{%addon_shop_product_spec}}',['id'=>'12','merchant_id'=>'1','product_id'=>'21','base_spec_id'=>'10','title'=>'颜色','sort'=>'2','show_type'=>'2','status'=>'1','created_at'=>'0','updated_at'=>'1570604763']);
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_product_spec}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

