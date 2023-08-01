<?php

use yii\db\Migration;

class m230619_064744_addon_tiny_shop_marketing_product_sku extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_tiny_shop_marketing_product_sku}}', [
            'id' => "int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键'",
            'merchant_id' => "int(11) NULL DEFAULT '0' COMMENT '店铺ID'",
            'product_id' => "int(11) unsigned NULL DEFAULT '0' COMMENT '商品ID'",
            'sku_id' => "int(11) unsigned NULL DEFAULT '0' COMMENT 'SKU'",
            'marketing_product_id' => "int(10) NULL DEFAULT '0' COMMENT '关联ID'",
            'marketing_id' => "int(11) unsigned NULL DEFAULT '0' COMMENT '对应活动'",
            'marketing_type' => "varchar(60) NULL DEFAULT '' COMMENT '活动类型'",
            'marketing_data' => "json NULL COMMENT '活动数据'",
            'discount' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '活动金额'",
            'discount_type' => "tinyint(4) NULL DEFAULT '1' COMMENT '活动金额类型'",
            'marketing_sales' => "int(11) NULL DEFAULT '0' COMMENT '销量'",
            'marketing_stock' => "int(11) NULL DEFAULT '0' COMMENT '锁定可用库存'",
            'prediction_time' => "int(11) unsigned NULL DEFAULT '0' COMMENT '预告时间'",
            'start_time' => "int(11) unsigned NULL DEFAULT '0' COMMENT '开始时间'",
            'end_time' => "int(11) unsigned NULL DEFAULT '0' COMMENT '结束时间'",
            'number' => "int(11) unsigned NULL DEFAULT '1' COMMENT '参与数量'",
            'min_buy' => "int(11) unsigned NULL DEFAULT '1' COMMENT '最少购买'",
            'max_buy' => "int(11) unsigned NULL DEFAULT '0' COMMENT '每人限购 0无限制'",
            'decimal_reservation_number' => "tinyint(4) NULL DEFAULT '-1' COMMENT '价格保留方式 0去掉角和分 1去掉分'",
            'marketing_price' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '营销金额(临时)'",
            'is_min_price' => "tinyint(4) NULL DEFAULT '0' COMMENT '是否最低价'",
            'not_calculate' => "tinyint(4) NULL DEFAULT '0' COMMENT '参与最低价计算 1参加 0不参加'",
            'status' => "tinyint(4) NULL DEFAULT '0' COMMENT '状态'",
            'is_template' => "tinyint(4) NULL DEFAULT '0' COMMENT '活动模板'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='营销_关联商品'");
        
        /* 索引设置 */
        $this->createIndex('product_id','{{%addon_tiny_shop_marketing_product_sku}}','product_id',0);
        $this->createIndex('marketing_id','{{%addon_tiny_shop_marketing_product_sku}}','marketing_id',0);
        $this->createIndex('marketing_type','{{%addon_tiny_shop_marketing_product_sku}}','marketing_type, status',0);
        $this->createIndex('start_time','{{%addon_tiny_shop_marketing_product_sku}}','start_time, end_time',0);
        $this->createIndex('merchant_id','{{%addon_tiny_shop_marketing_product_sku}}','merchant_id',0);
        $this->createIndex('sku_id','{{%addon_tiny_shop_marketing_product_sku}}','sku_id',0);
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_tiny_shop_marketing_product_sku}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

