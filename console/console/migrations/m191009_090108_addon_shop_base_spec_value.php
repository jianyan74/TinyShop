<?php

use yii\db\Migration;

class m191009_090108_addon_shop_base_spec_value extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_base_spec_value}}', [
            'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
            'merchant_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '商户id'",
            'spec_id' => "int(11) NOT NULL COMMENT '属性编码'",
            'title' => "varchar(125) NOT NULL COMMENT '选项名称'",
            'data' => "varchar(100) NULL DEFAULT '' COMMENT '默认数据'",
            'sort' => "int(11) NOT NULL DEFAULT '999' COMMENT '排序'",
            'status' => "tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态(-1:已删除,0:禁用,1:正常)'",
            'created_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '修改时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='扩展_微商城_系统属性表'");
        
        /* 索引设置 */
        $this->createIndex('product_attribute_option_name_attr_id_index','{{%addon_shop_base_spec_value}}','title, spec_id',0);
        
        
        /* 表数据 */
        $this->insert('{{%addon_shop_base_spec_value}}',['id'=>'44','merchant_id'=>'1','spec_id'=>'10','title'=>'红','data'=>'','sort'=>'999','status'=>'1','created_at'=>'1557467412','updated_at'=>'1557732636']);
        $this->insert('{{%addon_shop_base_spec_value}}',['id'=>'45','merchant_id'=>'1','spec_id'=>'10','title'=>'黄','data'=>'','sort'=>'999','status'=>'1','created_at'=>'1557467412','updated_at'=>'1557467412']);
        $this->insert('{{%addon_shop_base_spec_value}}',['id'=>'46','merchant_id'=>'1','spec_id'=>'10','title'=>'蓝','data'=>'','sort'=>'999','status'=>'1','created_at'=>'1557467412','updated_at'=>'1557467412']);
        $this->insert('{{%addon_shop_base_spec_value}}',['id'=>'47','merchant_id'=>'1','spec_id'=>'11','title'=>'大','data'=>'','sort'=>'999','status'=>'1','created_at'=>'1557467430','updated_at'=>'1557467430']);
        $this->insert('{{%addon_shop_base_spec_value}}',['id'=>'48','merchant_id'=>'1','spec_id'=>'11','title'=>'中','data'=>'','sort'=>'999','status'=>'1','created_at'=>'1557467430','updated_at'=>'1557467430']);
        $this->insert('{{%addon_shop_base_spec_value}}',['id'=>'49','merchant_id'=>'1','spec_id'=>'11','title'=>'小','data'=>'','sort'=>'999','status'=>'1','created_at'=>'1557467430','updated_at'=>'1557467430']);
        $this->insert('{{%addon_shop_base_spec_value}}',['id'=>'50','merchant_id'=>'1','spec_id'=>'12','title'=>'可爱','data'=>'','sort'=>'999','status'=>'1','created_at'=>'1557467491','updated_at'=>'1557467491']);
        $this->insert('{{%addon_shop_base_spec_value}}',['id'=>'51','merchant_id'=>'1','spec_id'=>'12','title'=>'青春','data'=>'','sort'=>'999','status'=>'1','created_at'=>'1557467491','updated_at'=>'1557467491']);
        $this->insert('{{%addon_shop_base_spec_value}}',['id'=>'52','merchant_id'=>'1','spec_id'=>'12','title'=>'活泼','data'=>'','sort'=>'999','status'=>'1','created_at'=>'1557467491','updated_at'=>'1557467491']);
        $this->insert('{{%addon_shop_base_spec_value}}',['id'=>'53','merchant_id'=>'1','spec_id'=>'12','title'=>'浪漫','data'=>'','sort'=>'999','status'=>'1','created_at'=>'1557467491','updated_at'=>'1557467491']);
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_base_spec_value}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

