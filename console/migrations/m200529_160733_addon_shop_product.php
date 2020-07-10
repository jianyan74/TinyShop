<?php

use yii\db\Migration;

class m200529_160733_addon_shop_product extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_product}}', [
            'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
            'merchant_id' => "int(11) NOT NULL DEFAULT '0' COMMENT '商家编号'",
            'name' => "varchar(255) NOT NULL COMMENT '商品标题'",
            'picture' => "varchar(100) NULL DEFAULT '' COMMENT '商品主图'",
            'cate_id' => "int(11) unsigned NOT NULL COMMENT '商品分类编号'",
            'brand_id' => "int(11) unsigned NULL DEFAULT '0' COMMENT '品牌编号'",
            'type_id' => "tinyint(4) unsigned NULL DEFAULT '0' COMMENT '类型编号'",
            'sketch' => "varchar(200) NULL DEFAULT '' COMMENT '简述'",
            'intro' => "text NOT NULL COMMENT '商品描述'",
            'keywords' => "varchar(200) NULL DEFAULT '' COMMENT '商品关键字'",
            'tags' => "varchar(200) NULL DEFAULT '' COMMENT '标签'",
            'marque' => "varchar(100) NULL DEFAULT '' COMMENT '商品型号'",
            'barcode' => "varchar(100) NULL DEFAULT '' COMMENT '仓库条码'",
            'sales' => "int(11) NOT NULL DEFAULT '0' COMMENT '虚拟购买量'",
            'real_sales' => "int(10) NOT NULL DEFAULT '0' COMMENT '实际销量'",
            'total_sales' => "int(11) NULL DEFAULT '0' COMMENT '总销量'",
            'price' => "decimal(8,2) NOT NULL COMMENT '商品价格'",
            'market_price' => "decimal(8,2) NULL DEFAULT '0.00' COMMENT '市场价格'",
            'cost_price' => "decimal(19,2) NULL DEFAULT '0.00' COMMENT '成本价'",
            'wholesale_price' => "decimal(10,2) unsigned NULL DEFAULT '0.00' COMMENT '拼团价格'",
            'stock' => "int(11) NOT NULL DEFAULT '0' COMMENT '库存量'",
            'warning_stock' => "int(11) NOT NULL DEFAULT '0' COMMENT '库存警告'",
            'covers' => "text NOT NULL COMMENT '幻灯片'",
            'posters' => "json NULL COMMENT '宣传海报'",
            'state' => "tinyint(4) NOT NULL DEFAULT '0' COMMENT '审核状态 -1 审核失败 0 未审核 1 审核成功'",
            'is_package' => "enum('0','1') NULL DEFAULT '0' COMMENT '是否是套餐'",
            'is_attribute' => "enum('0','1') NULL DEFAULT '0' COMMENT '启用商品规格'",
            'sort' => "int(11) NOT NULL DEFAULT '999' COMMENT '排序'",
            'product_status' => "tinyint(4) NULL DEFAULT '1' COMMENT '商品状态 0下架，1正常，10违规（禁售）'",
            'shipping_type' => "tinyint(2) NULL DEFAULT '1' COMMENT '运费类型 1免邮2买家付邮费'",
            'shipping_fee' => "decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '运费'",
            'shipping_fee_id' => "int(11) NOT NULL DEFAULT '0' COMMENT '物流模板id'",
            'shipping_fee_type' => "int(11) NOT NULL DEFAULT '1' COMMENT '计价方式1.计件2.体积3.重量'",
            'product_weight' => "decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '商品重量'",
            'product_volume' => "decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '商品体积'",
            'marketing_type' => "varchar(50) NOT NULL DEFAULT '0' COMMENT '促销类型'",
            'marketing_id' => "int(11) NOT NULL DEFAULT '0' COMMENT '促销活动ID'",
            'marketing_price' => "decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '商品促销价格'",
            'point_exchange_type' => "tinyint(3) NOT NULL DEFAULT '1' COMMENT '积分兑换类型'",
            'point_exchange' => "int(11) NOT NULL DEFAULT '0' COMMENT '积分兑换'",
            'max_use_point' => "int(11) NOT NULL DEFAULT '0' COMMENT '积分抵现最大可用积分数 0为不可使用'",
            'integral_give_type' => "int(1) NOT NULL DEFAULT '0' COMMENT '积分赠送类型 0固定值 1按比率'",
            'give_point' => "int(11) NOT NULL DEFAULT '0' COMMENT '购买商品赠送积分'",
            'min_buy' => "int(11) NOT NULL DEFAULT '1' COMMENT '最少买几件'",
            'max_buy' => "int(11) NOT NULL DEFAULT '0' COMMENT '限购 0 不限购'",
            'view' => "int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品点击数量'",
            'star' => "int(10) unsigned NOT NULL DEFAULT '5' COMMENT '好评星级'",
            'collect_num' => "int(10) unsigned NOT NULL DEFAULT '0' COMMENT '收藏数量'",
            'comment_num' => "int(10) unsigned NOT NULL DEFAULT '0' COMMENT '评价数'",
            'transmit_num' => "int(11) NOT NULL DEFAULT '0' COMMENT '分享数'",
            'province_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '一级地区id'",
            'city_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '二级地区id'",
            'area_id' => "int(10) NULL DEFAULT '0' COMMENT '三级地区'",
            'address_name' => "varchar(200) NULL DEFAULT '' COMMENT '地址'",
            'is_stock_visible' => "int(1) NOT NULL DEFAULT '1' COMMENT '库存显示 0不显示1显示'",
            'is_hot' => "int(1) NOT NULL DEFAULT '0' COMMENT '是否热销商品'",
            'is_recommend' => "int(1) NOT NULL DEFAULT '0' COMMENT '是否推荐'",
            'is_new' => "int(1) NOT NULL DEFAULT '0' COMMENT '是否新品'",
            'is_bill' => "int(1) NOT NULL DEFAULT '0' COMMENT '是否开具增值税发票 1是，0否'",
            'base_attribute_id' => "int(11) NOT NULL DEFAULT '0' COMMENT '商品类型'",
            'base_attribute_format' => "text NULL COMMENT '商品规格'",
            'match_point' => "float(10,2) NULL DEFAULT '5' COMMENT '实物与描述相符（根据评价计算）'",
            'match_ratio' => "float(10,2) NULL DEFAULT '100' COMMENT '实物与描述相符（根据评价计算）百分比'",
            'sale_date' => "int(11) NULL DEFAULT '0' COMMENT '上下架时间'",
            'is_virtual' => "tinyint(1) NULL DEFAULT '0' COMMENT '是否虚拟商品'",
            'production_date' => "int(11) NULL DEFAULT '0' COMMENT '生产日期'",
            'shelf_life' => "int(11) NULL DEFAULT '0' COMMENT '保质期'",
            'is_open_presell' => "tinyint(4) NULL DEFAULT '0' COMMENT '是否支持预售'",
            'presell_time' => "int(11) NULL DEFAULT '0' COMMENT '预售发货时间'",
            'presell_day' => "int(11) NULL DEFAULT '0' COMMENT '预售发货天数'",
            'presell_delivery_type' => "int(11) NULL DEFAULT '1' COMMENT '预售发货方式1. 按照预售发货时间 2.按照预售发货天数'",
            'presell_price' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '预售金额'",
            'unit' => "varchar(20) NULL DEFAULT '' COMMENT '商品单位'",
            'video_url' => "varchar(100) NULL DEFAULT '' COMMENT '展示视频'",
            'supplier_id' => "int(11) NULL DEFAULT '0' COMMENT '供货商id'",
            'is_open_commission' => "tinyint(4) NULL DEFAULT '0' COMMENT '是否支持分销'",
            'status' => "tinyint(4) NULL DEFAULT '1' COMMENT '状态'",
            'created_at' => "int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='扩展_微商城_商品表'");
        
        /* 索引设置 */
        $this->createIndex('price','{{%addon_shop_product}}','price',0);
        $this->createIndex('cate_id','{{%addon_shop_product}}','cate_id',0);
        $this->createIndex('brand_id','{{%addon_shop_product}}','brand_id',0);
        $this->createIndex('view','{{%addon_shop_product}}','view',0);
        $this->createIndex('star','{{%addon_shop_product}}','star',0);
        $this->createIndex('comment_num','{{%addon_shop_product}}','comment_num',0);
        $this->createIndex('sort','{{%addon_shop_product}}','sort',0);
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_product}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

