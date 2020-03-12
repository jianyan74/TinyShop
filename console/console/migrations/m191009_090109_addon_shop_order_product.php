<?php

use yii\db\Migration;

class m191009_090109_addon_shop_order_product extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_order_product}}', [
            'id' => "int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '订单项ID'",
            'order_id' => "int(11) NULL COMMENT '订单ID'",
            'member_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '用户id'",
            'merchant_id' => "int(11) NULL DEFAULT '1' COMMENT '店铺ID'",
            'product_id' => "int(11) NULL COMMENT '商品ID'",
            'product_name' => "varchar(100) NULL DEFAULT '' COMMENT '商品名称'",
            'sku_id' => "int(11) NULL DEFAULT '0' COMMENT 'skuID'",
            'sku_name' => "varchar(50) NULL DEFAULT '' COMMENT 'sku名称'",
            'price' => "decimal(19,2) NULL DEFAULT '0.00' COMMENT '商品价格'",
            'cost_price' => "decimal(19,2) NULL DEFAULT '0.00' COMMENT '商品成本价'",
            'num' => "int(10) unsigned NULL DEFAULT '0' COMMENT '购买数量'",
            'adjust_money' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '调整金额'",
            'product_money' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '商品总价'",
            'product_picture' => "int(11) NULL DEFAULT '0' COMMENT '商品图片'",
            'buyer_id' => "int(11) NULL DEFAULT '0' COMMENT '购买人ID'",
            'point_exchange_type' => "int(11) NULL DEFAULT '0' COMMENT '积分兑换类型0.非积分兑换1.积分兑换'",
            'product_virtual_group' => "varchar(200) NULL DEFAULT '1' COMMENT '商品类型'",
            'promotion_id' => "int(11) NULL DEFAULT '0' COMMENT '促销ID'",
            'promotion_type_id' => "int(11) NULL DEFAULT '0' COMMENT '促销类型'",
            'order_type' => "int(11) NULL DEFAULT '1' COMMENT '订单类型'",
            'order_status' => "int(11) NULL DEFAULT '0' COMMENT '订单状态'",
            'give_point' => "int(11) NULL DEFAULT '0' COMMENT '积分数量'",
            'shipping_status' => "int(11) NULL DEFAULT '0' COMMENT '物流状态'",
            'refund_type' => "int(11) NULL DEFAULT '1' COMMENT '退款方式'",
            'refund_require_money' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '退款金额'",
            'refund_reason' => "varchar(200) NULL DEFAULT '' COMMENT '退款原因'",
            'refund_shipping_code' => "varchar(200) NULL DEFAULT '' COMMENT '退款物流单号'",
            'refund_shipping_company' => "varchar(200) NULL DEFAULT '0' COMMENT '退款物流公司名称'",
            'refund_real_money' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '实际退款金额'",
            'refund_status' => "int(1) NULL DEFAULT '0' COMMENT '退款状态'",
            'memo' => "varchar(200) NULL DEFAULT '' COMMENT '备注'",
            'is_evaluate' => "smallint(6) NULL DEFAULT '0' COMMENT '是否评价 0为未评价 1为已评价 2为已追评'",
            'refund_time' => "int(11) NULL DEFAULT '0' COMMENT '退款时间'",
            'refund_balance_money' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '订单退款余额'",
            'tmp_express_company' => "varchar(200) NULL DEFAULT '' COMMENT '批量打印时添加的临时物流公司'",
            'tmp_express_company_id' => "int(11) NULL DEFAULT '0' COMMENT '批量打印时添加的临时物流公司id'",
            'tmp_express_no' => "varchar(50) NULL DEFAULT '' COMMENT '批量打印时添加的临时订单编号'",
            'gift_flag' => "int(11) NULL DEFAULT '0' COMMENT '赠品标识，0:不是赠品，大于0：赠品id'",
            'status' => "tinyint(4) NULL DEFAULT '1' COMMENT '状态[-1:删除;0:禁用;1启用]'",
            'created_at' => "int(10) NULL DEFAULT '0'",
            'updated_at' => "int(10) NULL DEFAULT '0'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AVG_ROW_LENGTH=289 ROW_FORMAT=DYNAMIC COMMENT='订单商品表'");
        
        /* 索引设置 */
        $this->createIndex('UK_ns_order_goods_buyer_id','{{%addon_shop_order_product}}','buyer_id',0);
        $this->createIndex('UK_ns_order_goods_goods_id','{{%addon_shop_order_product}}','product_id',0);
        $this->createIndex('UK_ns_order_goods_order_id','{{%addon_shop_order_product}}','order_id',0);
        $this->createIndex('UK_ns_order_goods_promotion_id','{{%addon_shop_order_product}}','promotion_id',0);
        $this->createIndex('UK_ns_order_goods_sku_id','{{%addon_shop_order_product}}','sku_id',0);
        
        
        /* 表数据 */
        $this->insert('{{%addon_shop_order_product}}',['id'=>'390','order_id'=>'203','member_id'=>'1','merchant_id'=>'1','product_id'=>'21','product_name'=>'夏季T恤白色上衣','sku_id'=>'77','sku_name'=>'可爱 大 红','price'=>'26.00','cost_price'=>'20.00','num'=>'7','adjust_money'=>'0.00','product_money'=>'182.00','product_picture'=>NULL,'buyer_id'=>'1','point_exchange_type'=>'2','product_type'=>'1','promotion_id'=>'0','promotion_type_id'=>'0','order_type'=>'1','order_status'=>'1','give_point'=>'21','shipping_status'=>'0','refund_type'=>'1','refund_require_money'=>'0.00','refund_reason'=>'不想要了','refund_shipping_code'=>'','refund_shipping_company'=>'0','refund_real_money'=>'0.00','refund_status'=>'5','memo'=>'','is_evaluate'=>'0','refund_time'=>'0','refund_balance_money'=>'0.00','tmp_express_company'=>'','tmp_express_company_id'=>'0','tmp_express_no'=>'','gift_flag'=>'0','status'=>'1','created_at'=>'1570607895','updated_at'=>'1570608617']);
        $this->insert('{{%addon_shop_order_product}}',['id'=>'391','order_id'=>'203','member_id'=>'1','merchant_id'=>'1','product_id'=>'21','product_name'=>'夏季T恤白色上衣','sku_id'=>'80','sku_name'=>'可爱 大 黄','price'=>'26.00','cost_price'=>'20.00','num'=>'1','adjust_money'=>'0.00','product_money'=>'26.00','product_picture'=>NULL,'buyer_id'=>'1','point_exchange_type'=>'2','product_type'=>'1','promotion_id'=>'0','promotion_type_id'=>'0','order_type'=>'1','order_status'=>'1','give_point'=>'21','shipping_status'=>'0','refund_type'=>'1','refund_require_money'=>'0.00','refund_reason'=>'不想要了','refund_shipping_code'=>'','refund_shipping_company'=>'0','refund_real_money'=>'0.00','refund_status'=>'5','memo'=>'哈哈哈','is_evaluate'=>'0','refund_time'=>'1570610678','refund_balance_money'=>'0.00','tmp_express_company'=>'','tmp_express_company_id'=>'0','tmp_express_no'=>'','gift_flag'=>'0','status'=>'1','created_at'=>'1570607895','updated_at'=>'1570610678']);
        $this->insert('{{%addon_shop_order_product}}',['id'=>'392','order_id'=>'203','member_id'=>'1','merchant_id'=>'1','product_id'=>'21','product_name'=>'夏季T恤白色上衣','sku_id'=>'83','sku_name'=>'可爱 小 红','price'=>'26.00','cost_price'=>'0.00','num'=>'1','adjust_money'=>'0.00','product_money'=>'26.00','product_picture'=>NULL,'buyer_id'=>'1','point_exchange_type'=>'2','product_type'=>'1','promotion_id'=>'0','promotion_type_id'=>'0','order_type'=>'1','order_status'=>'1','give_point'=>'21','shipping_status'=>'0','refund_type'=>'1','refund_require_money'=>'26.00','refund_reason'=>'不想要了','refund_shipping_code'=>'','refund_shipping_company'=>'0','refund_real_money'=>'0.00','refund_status'=>'5','memo'=>'','is_evaluate'=>'0','refund_time'=>'1570611120','refund_balance_money'=>'0.00','tmp_express_company'=>'','tmp_express_company_id'=>'0','tmp_express_no'=>'','gift_flag'=>'0','status'=>'1','created_at'=>'1570607895','updated_at'=>'1570611120']);
        $this->insert('{{%addon_shop_order_product}}',['id'=>'393','order_id'=>'204','member_id'=>'1','merchant_id'=>'1','product_id'=>'21','product_name'=>'夏季T恤白色上衣','sku_id'=>'77','sku_name'=>'可爱 大 红','price'=>'26.00','cost_price'=>'20.00','num'=>'7','adjust_money'=>'10.00','product_money'=>'192.00','product_picture'=>NULL,'buyer_id'=>'1','point_exchange_type'=>'2','product_type'=>'1','promotion_id'=>'0','promotion_type_id'=>'0','order_type'=>'1','order_status'=>'1','give_point'=>'21','shipping_status'=>'0','refund_type'=>'1','refund_require_money'=>'0.00','refund_reason'=>'','refund_shipping_code'=>'','refund_shipping_company'=>'0','refund_real_money'=>'0.00','refund_status'=>'0','memo'=>'','is_evaluate'=>'0','refund_time'=>'0','refund_balance_money'=>'0.00','tmp_express_company'=>'','tmp_express_company_id'=>'0','tmp_express_no'=>'','gift_flag'=>'0','status'=>'1','created_at'=>'1570609330','updated_at'=>'1570609340']);
        $this->insert('{{%addon_shop_order_product}}',['id'=>'394','order_id'=>'204','member_id'=>'1','merchant_id'=>'1','product_id'=>'21','product_name'=>'夏季T恤白色上衣','sku_id'=>'80','sku_name'=>'可爱 大 黄','price'=>'26.00','cost_price'=>'20.00','num'=>'1','adjust_money'=>'0.00','product_money'=>'26.00','product_picture'=>NULL,'buyer_id'=>'1','point_exchange_type'=>'2','product_type'=>'1','promotion_id'=>'0','promotion_type_id'=>'0','order_type'=>'1','order_status'=>'1','give_point'=>'21','shipping_status'=>'0','refund_type'=>'1','refund_require_money'=>'26.00','refund_reason'=>'不想要了','refund_shipping_code'=>'','refund_shipping_company'=>'0','refund_real_money'=>'0.00','refund_status'=>'5','memo'=>'','is_evaluate'=>'0','refund_time'=>'1570610791','refund_balance_money'=>'0.00','tmp_express_company'=>'','tmp_express_company_id'=>'0','tmp_express_no'=>'','gift_flag'=>'0','status'=>'1','created_at'=>'1570609330','updated_at'=>'1570610791']);
        $this->insert('{{%addon_shop_order_product}}',['id'=>'395','order_id'=>'204','member_id'=>'1','merchant_id'=>'1','product_id'=>'21','product_name'=>'夏季T恤白色上衣','sku_id'=>'83','sku_name'=>'可爱 小 红','price'=>'26.00','cost_price'=>'0.00','num'=>'1','adjust_money'=>'0.00','product_money'=>'26.00','product_picture'=>NULL,'buyer_id'=>'1','point_exchange_type'=>'2','product_type'=>'1','promotion_id'=>'0','promotion_type_id'=>'0','order_type'=>'1','order_status'=>'1','give_point'=>'21','shipping_status'=>'0','refund_type'=>'1','refund_require_money'=>'0.00','refund_reason'=>'','refund_shipping_code'=>'','refund_shipping_company'=>'0','refund_real_money'=>'0.00','refund_status'=>'0','memo'=>'','is_evaluate'=>'0','refund_time'=>'0','refund_balance_money'=>'0.00','tmp_express_company'=>'','tmp_express_company_id'=>'0','tmp_express_no'=>'','gift_flag'=>'0','status'=>'1','created_at'=>'1570609330','updated_at'=>'1570609340']);
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_order_product}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

