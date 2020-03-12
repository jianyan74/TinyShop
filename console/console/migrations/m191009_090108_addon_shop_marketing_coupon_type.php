<?php

use yii\db\Migration;

class m191009_090108_addon_shop_marketing_coupon_type extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_marketing_coupon_type}}', [
            'id' => "int(11) NOT NULL AUTO_INCREMENT COMMENT '优惠券类型Id'",
            'merchant_id' => "int(11) NOT NULL DEFAULT '0' COMMENT '店铺ID'",
            'title' => "varchar(50) NOT NULL DEFAULT '' COMMENT '优惠券名称'",
            'money' => "decimal(10,2) NOT NULL COMMENT '发放面额'",
            'count' => "int(11) NOT NULL COMMENT '发放数量'",
            'get_count' => "int(11) unsigned NULL DEFAULT '0' COMMENT '领取数量'",
            'max_fetch' => "int(11) NOT NULL DEFAULT '0' COMMENT '每人最大领取个数 0无限制'",
            'at_least' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '满多少元使用 0代表无限制'",
            'need_user_level' => "tinyint(4) NULL DEFAULT '0' COMMENT '领取人会员等级'",
            'range_type' => "tinyint(4) NULL DEFAULT '1' COMMENT '使用范围0部分产品使用 1全场产品使用'",
            'is_show' => "int(11) NULL DEFAULT '0' COMMENT '是否允许首页显示0不显示1显示'",
            'start_time' => "int(11) NULL DEFAULT '0' COMMENT '有效日期开始时间'",
            'end_time' => "int(11) NULL DEFAULT '0' COMMENT '有效日期结束时间'",
            'term_of_validity_type' => "int(1) NULL DEFAULT '0' COMMENT '有效期类型 0固定时间 1领取之日起'",
            'fixed_term' => "int(3) NULL DEFAULT '1' COMMENT '领取之日起N天内有效'",
            'status' => "tinyint(4) NULL DEFAULT '1' COMMENT '状态[-1:删除;0:禁用;1启用]'",
            'created_at' => "int(10) NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) NULL DEFAULT '0' COMMENT '修改时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AVG_ROW_LENGTH=1365 ROW_FORMAT=DYNAMIC COMMENT='优惠券类型表'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        $this->insert('{{%addon_shop_marketing_coupon_type}}',['id'=>'3','merchant_id'=>'1','title'=>'满100-2','money'=>'2.00','count'=>'72','get_count'=>'2','max_fetch'=>'0','at_least'=>'100.00','need_user_level'=>'0','range_type'=>'1','is_show'=>'1','start_time'=>'1557666769','end_time'=>'1557666769','term_of_validity_type'=>'1','fixed_term'=>'12','status'=>'1','created_at'=>'1557666781','updated_at'=>'1563589678']);
        $this->insert('{{%addon_shop_marketing_coupon_type}}',['id'=>'4','merchant_id'=>'1','title'=>'满100-10','money'=>'10.00','count'=>'7','get_count'=>'0','max_fetch'=>'1','at_least'=>'100.00','need_user_level'=>'0','range_type'=>'1','is_show'=>'1','start_time'=>'1558587000','end_time'=>'1565836200','term_of_validity_type'=>'0','fixed_term'=>'1','status'=>'1','created_at'=>'1558587007','updated_at'=>'1560309683']);
        $this->insert('{{%addon_shop_marketing_coupon_type}}',['id'=>'5','merchant_id'=>'1','title'=>'测算','money'=>'1000.00','count'=>'12','get_count'=>'2','max_fetch'=>'2','at_least'=>'0.00','need_user_level'=>'0','range_type'=>'1','is_show'=>'1','start_time'=>'1563589741','end_time'=>'1563589741','term_of_validity_type'=>'1','fixed_term'=>'1','status'=>'1','created_at'=>'1563589756','updated_at'=>'1563590314']);
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_marketing_coupon_type}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

