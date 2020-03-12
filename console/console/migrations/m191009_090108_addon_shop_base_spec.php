<?php

use yii\db\Migration;

class m191009_090108_addon_shop_base_spec extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_base_spec}}', [
            'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
            'merchant_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '商户id'",
            'title' => "varchar(25) NOT NULL COMMENT '规格名称'",
            'sort' => "int(11) NOT NULL DEFAULT '999' COMMENT '排列次序'",
            'show_type' => "tinyint(255) NULL DEFAULT '1' COMMENT '展示方式[1:文字;2:颜色;3:图片]'",
            'explain' => "varchar(100) NULL DEFAULT '' COMMENT '规格说明'",
            'status' => "tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态(-1:已删除,0:禁用,1:正常)'",
            'created_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '修改时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='扩展_微商城_系统规格表'");
        
        /* 索引设置 */
        $this->createIndex('product_attribute_category_id_name_index','{{%addon_shop_base_spec}}','title',0);
        
        
        /* 表数据 */
        $this->insert('{{%addon_shop_base_spec}}',['id'=>'10','merchant_id'=>'1','title'=>'颜色','sort'=>'999','show_type'=>'2','explain'=>'','status'=>'1','created_at'=>'1557467412','updated_at'=>'1557732636']);
        $this->insert('{{%addon_shop_base_spec}}',['id'=>'11','merchant_id'=>'1','title'=>'尺寸','sort'=>'999','show_type'=>'1','explain'=>'','status'=>'1','created_at'=>'1557467430','updated_at'=>'1557467430']);
        $this->insert('{{%addon_shop_base_spec}}',['id'=>'12','merchant_id'=>'1','title'=>'风格','sort'=>'999','show_type'=>'3','explain'=>'','status'=>'1','created_at'=>'1557467491','updated_at'=>'1557467491']);
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_base_spec}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

