<?php

use yii\db\Migration;

class m191009_090109_addon_shop_product extends Migration
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
            'price' => "decimal(8,2) NOT NULL COMMENT '商品价格'",
            'market_price' => "decimal(8,2) NULL DEFAULT '0.00' COMMENT '市场价格'",
            'cost_price' => "decimal(19,2) NULL DEFAULT '0.00' COMMENT '成本价'",
            'stock' => "int(11) NOT NULL DEFAULT '0' COMMENT '库存量'",
            'warning_stock' => "int(11) NOT NULL DEFAULT '0' COMMENT '库存警告'",
            'covers' => "text NOT NULL COMMENT '幻灯片'",
            'posters' => "text NULL COMMENT '宣传海报'",
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
            'promotion_type' => "tinyint(3) NOT NULL DEFAULT '0' COMMENT '促销类型 0无促销，1团购，2限时折扣'",
            'promote_id' => "int(11) NOT NULL DEFAULT '0' COMMENT '促销活动ID'",
            'promotion_price' => "decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '商品促销价格'",
            'point_exchange_type' => "tinyint(3) NOT NULL DEFAULT '1' COMMENT '积分兑换类型'",
            'point_exchange' => "int(11) NOT NULL DEFAULT '0' COMMENT '积分兑换'",
            'max_use_point' => "int(11) NOT NULL DEFAULT '0' COMMENT '积分抵现最大可用积分数 0为不可使用'",
            'integral_give_type' => "int(1) NOT NULL DEFAULT '0' COMMENT '积分赠送类型 0固定值 1按比率'",
            'give_point' => "int(11) NOT NULL DEFAULT '0' COMMENT '购买商品赠送积分'",
            'min_buy' => "int(11) NOT NULL DEFAULT '1' COMMENT '最少买几件'",
            'max_buy' => "int(11) NOT NULL DEFAULT '0' COMMENT '限购 0 不限购'",
            'view' => "int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品点击数量'",
            'star' => "tinyint(3) unsigned NOT NULL DEFAULT '5' COMMENT '好评星级'",
            'collect_num' => "int(10) unsigned NOT NULL DEFAULT '0' COMMENT '收藏数量'",
            'comment_num' => "int(10) unsigned NOT NULL DEFAULT '0' COMMENT '评价数'",
            'transmit_num' => "int(11) NOT NULL DEFAULT '0' COMMENT '分享数'",
            'province_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '一级地区id'",
            'city_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '二级地区id'",
            'area_id' => "int(10) NULL DEFAULT '0' COMMENT '三级地区'",
            'is_stock_visible' => "int(1) NOT NULL DEFAULT '1' COMMENT '库存显示 0不显示1显示'",
            'is_hot' => "int(1) NOT NULL DEFAULT '0' COMMENT '是否热销商品'",
            'is_recommend' => "int(1) NOT NULL DEFAULT '0' COMMENT '是否推荐'",
            'is_new' => "int(1) NOT NULL DEFAULT '0' COMMENT '是否新品'",
            'is_bill' => "int(1) NOT NULL DEFAULT '0' COMMENT '是否开具增值税发票 1是，0否'",
            'base_attribute_id' => "int(11) NOT NULL DEFAULT '0' COMMENT '商品类型'",
            'base_attribute_format' => "text NULL COMMENT '商品规格'",
            'match_point' => "float(10,2) NULL COMMENT '实物与描述相符（根据评价计算）'",
            'match_ratio' => "float(10,2) NULL COMMENT '实物与描述相符（根据评价计算）百分比'",
            'sale_date' => "int(11) NULL DEFAULT '0' COMMENT '上下架时间'",
            'virtual_goods_type_id' => "int(11) NULL DEFAULT '0' COMMENT '虚拟商品类型id'",
            'production_date' => "int(11) NULL DEFAULT '0' COMMENT '生产日期'",
            'shelf_life' => "varchar(50) NULL DEFAULT '' COMMENT '保质期'",
            'is_open_presell' => "tinyint(4) NULL DEFAULT '0' COMMENT '是否支持预售'",
            'presell_time' => "int(11) NULL DEFAULT '0' COMMENT '预售发货时间'",
            'presell_day' => "int(11) NULL DEFAULT '0' COMMENT '预售发货天数'",
            'presell_delivery_type' => "int(11) NULL DEFAULT '1' COMMENT '预售发货方式1. 按照预售发货时间 2.按照预售发货天数'",
            'presell_price' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '预售金额'",
            'unit' => "varchar(20) NULL DEFAULT '' COMMENT '商品单位'",
            'status' => "tinyint(4) NULL DEFAULT '1' COMMENT '状态'",
            'created_at' => "int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='扩展_微商城_商品表'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        $this->insert('{{%addon_shop_product}}',['id'=>'15','merchant_id'=>'1','name'=>'测试','cate_id'=>'6','brand_id'=>NULL,'type_id'=>'0','sketch'=>'','intro'=>'<p>123<br/></p>','keywords'=>'','tags'=>'','marque'=>'','barcode'=>'','sales'=>'0','real_sales'=>'0','price'=>'50.00','market_price'=>'0.00','cost_price'=>'0.00','stock'=>'100','warning_stock'=>'5','covers'=>'a:1:{i:0;s:80:"http://merchants.local/attachment/images/2019/07/19/image_156352842899989850.jpg";}','posters'=>NULL,'state'=>'0','is_package'=>'0','is_attribute'=>'0','sort'=>'999','product_status'=>'1','shipping_type'=>'1','shipping_fee'=>'0.00','shipping_fee_id'=>'0','shipping_fee_type'=>'1','product_weight'=>'0.00','product_volume'=>'0.00','promotion_type'=>'0','promote_id'=>'0','promotion_price'=>'0.00','point_exchange_type'=>'1','point_exchange'=>'0','max_use_point'=>'20','integral_give_type'=>'0','give_point'=>'0','min_buy'=>'1','max_buy'=>'0','view'=>'0','star'=>'5','collect_num'=>'0','comment_num'=>'0','transmit_num'=>'0','province_id'=>NULL,'city_id'=>NULL,'area_id'=>'0','is_stock_visible'=>'1','is_hot'=>'0','is_recommend'=>'0','is_new'=>'0','is_bill'=>'0','base_attribute_id'=>'0','base_attribute_format'=>'[]','match_point'=>NULL,'match_ratio'=>NULL,'sale_date'=>'0','virtual_goods_type_id'=>'0','production_date'=>'0','shelf_life'=>'','is_open_presell'=>'0','presell_time'=>'0','presell_day'=>'0','presell_delivery_type'=>'1','presell_price'=>'0.00','unit'=>'','status'=>'1','created_at'=>'1557724732','updated_at'=>'1563528446']);
        $this->insert('{{%addon_shop_product}}',['id'=>'21','merchant_id'=>'1','name'=>'夏季T恤白色上衣','cate_id'=>'6','brand_id'=>NULL,'type_id'=>'0','sketch'=>'1112345','intro'=>'<p>123<br/></p>','keywords'=>'','tags'=>'','marque'=>'11233','barcode'=>'','sales'=>'200','real_sales'=>'397','price'=>'0.00','market_price'=>'0.00','cost_price'=>'0.00','stock'=>'112312','warning_stock'=>'1000','covers'=>'a:1:{i:0;s:80:"http://merchants.local/attachment/images/2019/07/19/image_156352842899989850.jpg";}','posters'=>NULL,'state'=>'0','is_package'=>'0','is_attribute'=>'1','sort'=>'999','product_status'=>'1','shipping_type'=>'2','shipping_fee'=>'0.00','shipping_fee_id'=>'1','shipping_fee_type'=>'1','product_weight'=>'30.00','product_volume'=>'50.00','promotion_type'=>'0','promote_id'=>'0','promotion_price'=>'0.00','point_exchange_type'=>'2','point_exchange'=>'10','max_use_point'=>'0','integral_give_type'=>'1','give_point'=>'21','min_buy'=>'1','max_buy'=>'0','view'=>'0','star'=>'5','collect_num'=>'0','comment_num'=>'0','transmit_num'=>'0','province_id'=>NULL,'city_id'=>NULL,'area_id'=>'0','is_stock_visible'=>'1','is_hot'=>'0','is_recommend'=>'1','is_new'=>'0','is_bill'=>'0','base_attribute_id'=>'12','base_attribute_format'=>'[{\"id\":\"10\",\"base_spec_id\":\"12\",\"title\":\"风格\",\"show_type\":\"3\",\"value\":[{\"base_spec_id\":\"12\",\"base_spec_value_id\":\"51\",\"title\":\"青春\",\"sort\":\"0\",\"data\":\"http://merchants.local/attachment/images/2019/05/10/image_155746806110056981.png\"},{\"base_spec_id\":\"12\",\"base_spec_value_id\":\"50\",\"title\":\"可爱\",\"sort\":\"0\",\"data\":\"http://merchants.local/attachment/images/2019/05/08/image_155729450052485251.jpg\"},{\"base_spec_id\":\"12\",\"base_spec_value_id\":\"50\",\"title\":\"可爱\",\"sort\":\"1\",\"data\":\"http://merchants.local/attachment/images/2019/05/08/image_155729450052485251.jpg\"},{\"base_spec_id\":\"12\",\"base_spec_value_id\":\"51\",\"title\":\"青春\",\"sort\":\"1\",\"data\":\"\"}]},{\"id\":\"11\",\"base_spec_id\":\"11\",\"title\":\"尺寸\",\"show_type\":\"1\",\"value\":[{\"base_spec_id\":\"11\",\"base_spec_value_id\":\"47\",\"title\":\"大\",\"sort\":\"2\",\"data\":\"\"},{\"base_spec_id\":\"11\",\"base_spec_value_id\":\"47\",\"title\":\"大\",\"sort\":\"2\",\"data\":\"\"},{\"base_spec_id\":\"11\",\"base_spec_value_id\":\"48\",\"title\":\"中\",\"sort\":\"3\",\"data\":\"\"},{\"base_spec_id\":\"11\",\"base_spec_value_id\":\"48\",\"title\":\"中\",\"sort\":\"3\",\"data\":\"\"},{\"base_spec_id\":\"11\",\"base_spec_value_id\":\"49\",\"title\":\"小\",\"sort\":\"4\",\"data\":\"\"},{\"base_spec_id\":\"11\",\"base_spec_value_id\":\"49\",\"title\":\"小\",\"sort\":\"4\",\"data\":\"\"}]},{\"id\":\"12\",\"base_spec_id\":\"10\",\"title\":\"颜色\",\"show_type\":\"2\",\"value\":[{\"base_spec_id\":\"10\",\"base_spec_value_id\":\"44\",\"title\":\"红2\",\"sort\":\"5\",\"data\":\"#a61c00\"},{\"base_spec_id\":\"10\",\"base_spec_value_id\":\"44\",\"title\":\"红\",\"sort\":\"5\",\"data\":\"#a61c00\"},{\"base_spec_id\":\"10\",\"base_spec_value_id\":\"45\",\"title\":\"黄\",\"sort\":\"6\",\"data\":\"#f9cb9c\"},{\"base_spec_id\":\"10\",\"base_spec_value_id\":\"45\",\"title\":\"黄\",\"sort\":\"6\",\"data\":\"#ff9900\"},{\"base_spec_id\":\"10\",\"base_spec_value_id\":\"46\",\"title\":\"蓝\",\"sort\":\"7\",\"data\":\"#4a86e8\"},{\"base_spec_id\":\"10\",\"base_spec_value_id\":\"46\",\"title\":\"蓝\",\"sort\":\"7\",\"data\":\"#\"}]}]','match_point'=>NULL,'match_ratio'=>NULL,'sale_date'=>'0','virtual_goods_type_id'=>'0','production_date'=>'0','shelf_life'=>'','is_open_presell'=>'0','presell_time'=>'0','presell_day'=>'0','presell_delivery_type'=>'1','presell_price'=>'0.00','unit'=>'','status'=>'1','created_at'=>'1557726238','updated_at'=>'1570609674']);
        $this->insert('{{%addon_shop_product}}',['id'=>'22','merchant_id'=>'1','name'=>'123','cate_id'=>'6','brand_id'=>NULL,'type_id'=>'0','sketch'=>'','intro'=>'<p>12<br/></p>','keywords'=>'','tags'=>'','marque'=>'','barcode'=>'','sales'=>'0','real_sales'=>'0','price'=>'5.00','market_price'=>'10.00','cost_price'=>'100.00','stock'=>'50','warning_stock'=>'1','covers'=>'a:1:{i:0;s:80:"http://merchants.local/attachment/images/2019/05/08/image_155729450052485251.jpg";}','posters'=>NULL,'state'=>'0','is_package'=>'0','is_attribute'=>'0','sort'=>'999','product_status'=>'1','shipping_type'=>'1','shipping_fee'=>'0.00','shipping_fee_id'=>'0','shipping_fee_type'=>'1','product_weight'=>'0.00','product_volume'=>'0.00','promotion_type'=>'0','promote_id'=>'0','promotion_price'=>'0.00','point_exchange_type'=>'1','point_exchange'=>'0','max_use_point'=>'0','integral_give_type'=>'0','give_point'=>'0','min_buy'=>'1','max_buy'=>'0','view'=>'0','star'=>'5','collect_num'=>'0','comment_num'=>'0','transmit_num'=>'0','province_id'=>NULL,'city_id'=>NULL,'area_id'=>'0','is_stock_visible'=>'1','is_hot'=>'0','is_recommend'=>'0','is_new'=>'0','is_bill'=>'0','base_attribute_id'=>'0','base_attribute_format'=>'[]','match_point'=>NULL,'match_ratio'=>NULL,'sale_date'=>'0','virtual_goods_type_id'=>'0','production_date'=>'0','shelf_life'=>'','is_open_presell'=>'0','presell_time'=>'0','presell_day'=>'0','presell_delivery_type'=>'1','presell_price'=>'0.00','unit'=>'','status'=>'0','created_at'=>'1557727594','updated_at'=>'1557728720']);
        $this->insert('{{%addon_shop_product}}',['id'=>'23','merchant_id'=>'1','name'=>'哈哈哈','cate_id'=>'6','brand_id'=>NULL,'type_id'=>'0','sketch'=>'','intro'=>'<p>1234<br/></p>','keywords'=>'','tags'=>'','marque'=>'','barcode'=>'','sales'=>'0','real_sales'=>'0','price'=>'0.00','market_price'=>'0.00','cost_price'=>'0.00','stock'=>'0','warning_stock'=>'0','covers'=>'a:1:{i:0;s:80:"http://merchants.yllook.com/attachment/videos/2019/09/05/测试视频_poster.jpg";}','posters'=>NULL,'state'=>'0','is_package'=>'0','is_attribute'=>'1','sort'=>'999','product_status'=>'1','shipping_type'=>'1','shipping_fee'=>'0.00','shipping_fee_id'=>'0','shipping_fee_type'=>'1','product_weight'=>'0.00','product_volume'=>'0.00','promotion_type'=>'0','promote_id'=>'0','promotion_price'=>'0.00','point_exchange_type'=>'1','point_exchange'=>'0','max_use_point'=>'0','integral_give_type'=>'0','give_point'=>'0','min_buy'=>'1','max_buy'=>'0','view'=>'0','star'=>'5','collect_num'=>'0','comment_num'=>'0','transmit_num'=>'0','province_id'=>NULL,'city_id'=>NULL,'area_id'=>'0','is_stock_visible'=>'1','is_hot'=>'0','is_recommend'=>'0','is_new'=>'0','is_bill'=>'0','base_attribute_id'=>'0','base_attribute_format'=>'[]','match_point'=>NULL,'match_ratio'=>NULL,'sale_date'=>'0','virtual_goods_type_id'=>'0','production_date'=>'0','shelf_life'=>'','is_open_presell'=>'0','presell_time'=>'0','presell_day'=>'0','presell_delivery_type'=>'1','presell_price'=>'0.00','unit'=>'','status'=>'1','created_at'=>'1570588524','updated_at'=>'1570588524']);
        
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

