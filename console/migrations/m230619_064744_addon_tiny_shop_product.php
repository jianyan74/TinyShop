<?php

use yii\db\Migration;

class m230619_064744_addon_tiny_shop_product extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_tiny_shop_product}}', [
            'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
            'merchant_id' => "int(11) NULL DEFAULT '0' COMMENT '商家编号'",
            'store_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '店铺ID'",
            'name' => "varchar(255) NULL DEFAULT '' COMMENT '商品标题'",
            'picture' => "varchar(255) NULL DEFAULT '' COMMENT '商品主图'",
            'cate_id' => "int(11) unsigned NULL DEFAULT '0' COMMENT '商品分类编号'",
            'brand_id' => "int(11) unsigned NULL DEFAULT '0' COMMENT '品牌编号'",
            'type' => "tinyint(4) unsigned NULL DEFAULT '0' COMMENT '商品类型'",
            'sketch' => "varchar(200) NULL DEFAULT '' COMMENT '简述'",
            'intro' => "text NULL COMMENT '商品描述'",
            'keywords' => "varchar(200) NULL DEFAULT '' COMMENT '商品关键字'",
            'tags' => "json NULL COMMENT '标签'",
            'sku_no' => "varchar(100) NULL DEFAULT '' COMMENT '商品编码'",
            'barcode' => "varchar(100) NULL DEFAULT '' COMMENT '商品条码'",
            'sales' => "int(11) NULL DEFAULT '0' COMMENT '虚拟购买量'",
            'real_sales' => "int(10) NULL DEFAULT '0' COMMENT '实际销量'",
            'total_sales' => "int(11) NULL DEFAULT '0' COMMENT '总销量'",
            'price' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '商品价格'",
            'market_price' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '市场价格'",
            'cost_price' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '成本价'",
            'stock' => "int(11) NULL DEFAULT '0' COMMENT '库存量'",
            'stock_warning_num' => "int(11) NULL DEFAULT '0' COMMENT '库存警告数量'",
            'stock_deduction_type' => "tinyint(4) NULL DEFAULT '1' COMMENT '库存扣减类型'",
            'covers' => "json NULL COMMENT '幻灯片'",
            'video_url' => "varchar(200) NULL DEFAULT '' COMMENT '展示视频'",
            'sort' => "int(11) NULL DEFAULT '999' COMMENT '排序'",
            'delivery_type' => "json NULL COMMENT '配送方式'",
            'shipping_type' => "tinyint(2) NULL DEFAULT '1' COMMENT '运费类型 1免邮2买家付邮费3固定运费'",
            'shipping_fee' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '运费'",
            'shipping_fee_id' => "int(11) NULL DEFAULT '0' COMMENT '物流模板id'",
            'shipping_fee_type' => "int(11) NULL DEFAULT '1' COMMENT '计价方式1.计件2.体积3.重量'",
            'weight' => "decimal(8,2) NULL DEFAULT '0.00' COMMENT '商品重量'",
            'volume' => "decimal(8,2) NULL DEFAULT '0.00' COMMENT '商品体积'",
            'marketing_id' => "int(11) NULL DEFAULT '0' COMMENT '促销活动ID'",
            'marketing_type' => "varchar(50) NULL DEFAULT '' COMMENT '促销类型'",
            'marketing_price' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '商品促销价格'",
            'point_exchange_type' => "tinyint(3) NULL DEFAULT '1' COMMENT '积分兑换类型'",
            'point_exchange' => "int(11) NULL DEFAULT '0' COMMENT '积分兑换'",
            'point_give_type' => "int(1) NULL DEFAULT '0' COMMENT '积分赠送类型 0固定值 1按比率'",
            'give_point' => "int(11) NULL DEFAULT '0' COMMENT '购买商品赠送积分'",
            'max_use_point' => "int(11) NULL DEFAULT '0' COMMENT '积分抵现最大可用积分数 0为不可使用'",
            'min_buy' => "int(11) NULL DEFAULT '1' COMMENT '最少买几件'",
            'max_buy' => "int(11) NULL DEFAULT '0' COMMENT '限购 0 不限购'",
            'order_max_buy' => "int(11) NULL DEFAULT '0' COMMENT '单笔订单限购 0 不限购'",
            'view' => "int(10) unsigned NULL DEFAULT '0' COMMENT '商品点击数量'",
            'star' => "int(10) unsigned NULL DEFAULT '5' COMMENT '好评星级'",
            'collect_num' => "int(10) unsigned NULL DEFAULT '0' COMMENT '收藏数量'",
            'comment_num' => "int(10) unsigned NULL DEFAULT '0' COMMENT '评价数'",
            'transmit_num' => "int(11) NULL DEFAULT '0' COMMENT '分享数'",
            'province_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '所在省'",
            'city_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '所在市'",
            'area_id' => "int(10) NULL DEFAULT '0' COMMENT '所在区'",
            'address_name' => "varchar(200) NULL DEFAULT '' COMMENT '地址'",
            'attribute_id' => "int(11) unsigned NULL DEFAULT '0' COMMENT '商品参数模板'",
            'is_spec' => "tinyint(4) NULL DEFAULT '0' COMMENT '启用商品规格'",
            'is_stock_visible' => "tinyint(4) NULL DEFAULT '1' COMMENT '库存显示 0不显示1显示'",
            'is_sales_visible' => "tinyint(4) NULL DEFAULT '1' COMMENT '销量显示 0不显示1显示'",
            'is_hot' => "tinyint(4) NULL DEFAULT '0' COMMENT '是否热销商品'",
            'is_recommend' => "tinyint(4) NULL DEFAULT '0' COMMENT '是否推荐'",
            'is_new' => "tinyint(4) NULL DEFAULT '0' COMMENT '是否新品'",
            'is_bill' => "tinyint(4) NULL DEFAULT '0' COMMENT '是否开具增值税发票 1是，0否'",
            'spec_format' => "json NULL COMMENT '商品规格'",
            'match_point' => "float(10,2) NULL DEFAULT '5' COMMENT '实物与描述相符（根据评价计算）'",
            'match_ratio' => "float(10,2) NULL DEFAULT '100' COMMENT '实物与描述相符（根据评价计算）百分比'",
            'production_date' => "int(11) NULL DEFAULT '0' COMMENT '生产日期'",
            'shelf_life' => "int(11) NULL DEFAULT '0' COMMENT '保质期'",
            'growth_give_type' => "int(1) NULL DEFAULT '0' COMMENT '成长值赠送类型 0固定值 1按比率'",
            'give_growth' => "int(11) NULL DEFAULT '0' COMMENT '购买商品赠送成长值'",
            'unit' => "varchar(50) NULL DEFAULT '' COMMENT '商品单位'",
            'supplier_id' => "int(11) NULL DEFAULT '0' COMMENT '供货商id'",
            'spec_template_id' => "int(11) NULL DEFAULT '0' COMMENT '规格模板ID'",
            'is_commission' => "tinyint(4) NULL DEFAULT '0' COMMENT '是否支持分销'",
            'is_member_discount' => "tinyint(4) NULL DEFAULT '0' COMMENT '是否支持会员折扣'",
            'member_discount_config' => "tinyint(4) NULL DEFAULT '1' COMMENT '折扣设置 1:系统;2:自定义'",
            'member_discount_type' => "tinyint(4) NULL DEFAULT '2' COMMENT '折扣类型'",
            'active_blacklist' => "tinyint(4) NULL DEFAULT '0' COMMENT '活动黑名单'",
            'is_list_visible' => "int(11) NULL DEFAULT '1' COMMENT '列表可见'",
            'start_time' => "int(11) NULL DEFAULT '0' COMMENT '上架时间'",
            'end_time' => "int(11) NULL DEFAULT '0' COMMENT '下架时间'",
            'extend' => "json NULL COMMENT '扩展内容'",
            'refusal_cause' => "varchar(200) NULL DEFAULT '' COMMENT '拒绝原因'",
            'audit_status' => "tinyint(4) NOT NULL DEFAULT '1' COMMENT '审核状态[0:申请;1通过;-1失败]'",
            'audit_time' => "int(10) unsigned NULL DEFAULT '0' COMMENT '审核时间'",
            'status' => "tinyint(4) NULL DEFAULT '1' COMMENT '状态'",
            'created_at' => "int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='扩展_微商城_商品表'");
        
        /* 索引设置 */
        $this->createIndex('price','{{%addon_tiny_shop_product}}','price',0);
        $this->createIndex('brand_id','{{%addon_tiny_shop_product}}','brand_id',0);
        $this->createIndex('view','{{%addon_tiny_shop_product}}','view',0);
        $this->createIndex('star','{{%addon_tiny_shop_product}}','star',0);
        $this->createIndex('comment_num','{{%addon_tiny_shop_product}}','comment_num',0);
        $this->createIndex('sort','{{%addon_tiny_shop_product}}','sort',0);
        $this->createIndex('cate_id','{{%addon_tiny_shop_product}}','cate_id',0);
        $this->createIndex('audit_status','{{%addon_tiny_shop_product}}','audit_status, status',0);
        $this->createIndex('match_point','{{%addon_tiny_shop_product}}','match_point',0);
        $this->createIndex('match_ratio','{{%addon_tiny_shop_product}}','match_ratio',0);
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_tiny_shop_product}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

