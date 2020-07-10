<?php

use yii\db\Migration;

class m200529_160733_addon_shop_order_product_express extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_order_product_express}}', [
            'id' => "int(11) unsigned NOT NULL AUTO_INCREMENT",
            'order_id' => "int(11) NOT NULL COMMENT '订单id'",
            'express_name' => "varchar(50) NULL DEFAULT '' COMMENT '包裹名称  （包裹- 1 包裹 - 2）'",
            'order_product_ids' => "json NULL COMMENT '产品id'",
            'shipping_type' => "tinyint(4) NULL DEFAULT '1' COMMENT '发货方式1 需要物流 0无需物流'",
            'express_company_id' => "int(11) NULL COMMENT '快递公司id'",
            'express_company' => "varchar(255) NULL DEFAULT '' COMMENT '物流公司名称'",
            'express_no' => "varchar(50) NULL COMMENT '运单编号'",
            'buyer_id' => "int(11) NULL DEFAULT '0' COMMENT '买家id'",
            'buyer_name' => "varchar(100) NULL DEFAULT '' COMMENT '买家信息'",
            'operator_id' => "int(11) NULL COMMENT '发货人用户id'",
            'operator_username' => "varchar(50) NULL COMMENT '发货人用户名'",
            'memo' => "varchar(255) NULL DEFAULT '' COMMENT '备注'",
            'status' => "tinyint(4) NULL DEFAULT '1' COMMENT '状态[-1:删除;0:禁用;1启用]'",
            'created_at' => "int(10) NULL DEFAULT '0'",
            'updated_at' => "int(10) NULL DEFAULT '0'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='扩展_微商城_商品订单物流信息表（多次发货）'");
        
        /* 索引设置 */
        $this->createIndex('UK_ns_order_goods_express_order_id','{{%addon_shop_order_product_express}}','order_id',0);
        $this->createIndex('UK_ns_order_goods_express_uid','{{%addon_shop_order_product_express}}','operator_id',0);
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_order_product_express}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

