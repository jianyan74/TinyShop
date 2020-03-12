<?php

use yii\db\Migration;

class m191009_090107_addon_shop_base_attribute extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_base_attribute}}', [
            'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '商品属性ID'",
            'merchant_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '商户id'",
            'title' => "varchar(50) NOT NULL DEFAULT '' COMMENT '模型名称'",
            'sort' => "int(11) NULL DEFAULT '999' COMMENT '排序'",
            'spec_ids' => "varchar(200) NOT NULL DEFAULT '' COMMENT '关联规格ids'",
            'status' => "tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态(-1:已删除,0:禁用,1:正常)'",
            'created_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '修改时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AVG_ROW_LENGTH=16384 ROW_FORMAT=DYNAMIC COMMENT='商品相关属性'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        $this->insert('{{%addon_shop_base_attribute}}',['id'=>'12','merchant_id'=>'1','title'=>'衣服','sort'=>'999','spec_ids'=>'12,11,10','status'=>'1','created_at'=>'1557467376','updated_at'=>'1557467580']);
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_base_attribute}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

