<?php

use yii\db\Migration;

class m191009_090108_addon_shop_marketing_point_config extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_marketing_point_config}}', [
            'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键'",
            'merchant_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '商户id'",
            'is_open' => "tinyint(4) NULL DEFAULT '0' COMMENT '是否启动'",
            'convert_rate' => "decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '1积分对应金额'",
            'desc' => "text NULL COMMENT '积分说明'",
            'status' => "tinyint(4) NULL DEFAULT '1' COMMENT '状态[-1:删除;0:禁用;1启用]'",
            'created_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '添加时间'",
            'updated_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '修改时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AVG_ROW_LENGTH=16384 ROW_FORMAT=DYNAMIC COMMENT='积分设置表'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        $this->insert('{{%addon_shop_marketing_point_config}}',['id'=>'2','merchant_id'=>'1','is_open'=>'1','convert_rate'=>'0.01','desc'=>'123','status'=>'1','created_at'=>'1557664739','updated_at'=>'1557979303']);
        $this->insert('{{%addon_shop_marketing_point_config}}',['id'=>'3','merchant_id'=>'1','is_open'=>'0','convert_rate'=>'0.00','desc'=>'','status'=>'1','created_at'=>'1557664775','updated_at'=>'1557664775']);
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_marketing_point_config}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

