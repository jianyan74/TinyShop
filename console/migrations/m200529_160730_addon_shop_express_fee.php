<?php

use yii\db\Migration;

class m200529_160730_addon_shop_express_fee extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_express_fee}}', [
            'id' => "int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '运费模板ID'",
            'merchant_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '商户id'",
            'company_id' => "int(11) unsigned NOT NULL DEFAULT '0' COMMENT '物流公司ID'",
            'title' => "varchar(30) NOT NULL DEFAULT '' COMMENT '运费模板名称'",
            'is_default' => "tinyint(2) unsigned NOT NULL DEFAULT '1' COMMENT '是否是默认模板'",
            'province_ids' => "text NULL COMMENT '省ID组'",
            'city_ids' => "text NULL COMMENT '市ID组'",
            'area_ids' => "text NULL COMMENT '区县ID组'",
            'weight_is_use' => "tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否启用重量运费'",
            'weight_snum' => "decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '首重'",
            'weight_sprice' => "decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '首重运费'",
            'weight_xnum' => "decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '续重'",
            'weight_xprice' => "decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '续重运费'",
            'volume_is_use' => "tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否启用体积计算运费'",
            'volume_snum' => "decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '首体积量'",
            'volume_sprice' => "decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '首体积运费'",
            'volume_xnum' => "decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '续体积量'",
            'volume_xprice' => "decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '续体积运费'",
            'bynum_is_use' => "tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否启用计件方式运费'",
            'bynum_snum' => "int(11) NOT NULL DEFAULT '0' COMMENT '首件'",
            'bynum_sprice' => "decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '首件运费'",
            'bynum_xnum' => "int(11) NOT NULL DEFAULT '0' COMMENT '续件'",
            'bynum_xprice' => "decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '续件运费'",
            'status' => "tinyint(4) NULL DEFAULT '1' COMMENT '状态[-1:删除;0:禁用;1启用]'",
            'created_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '更新时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COMMENT='扩展_微商城_运费模板'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_express_fee}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

