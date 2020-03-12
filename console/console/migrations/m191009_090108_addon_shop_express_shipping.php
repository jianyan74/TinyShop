<?php

use yii\db\Migration;

class m191009_090108_addon_shop_express_shipping extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_express_shipping}}', [
            'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
            'merchant_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '商户id'",
            'company_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '物流公司id'",
            'title' => "varchar(50) NOT NULL DEFAULT '' COMMENT '公司名称'",
            'size_type' => "smallint(6) NOT NULL DEFAULT '1' COMMENT '尺寸类型[1:像素px;2:毫米mm]'",
            'width' => "smallint(6) NOT NULL DEFAULT '0' COMMENT '宽度'",
            'height' => "smallint(6) NOT NULL DEFAULT '0' COMMENT '长度'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_express_shipping}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

