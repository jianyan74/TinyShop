<?php

use yii\db\Migration;

class m230619_064744_addon_tiny_shop_marketing_coupon_type extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_tiny_shop_marketing_coupon_type}}', [
            'id' => "int(11) NOT NULL AUTO_INCREMENT COMMENT '优惠券类型Id'",
            'merchant_id' => "int(11) NOT NULL DEFAULT '0' COMMENT '店铺ID'",
            'title' => "varchar(50) NULL DEFAULT '' COMMENT '优惠券名称'",
            'discount' => "decimal(10,2) NULL DEFAULT '9.90' COMMENT '活动金额'",
            'discount_type' => "tinyint(4) NULL DEFAULT '1' COMMENT '活动金额类型'",
            'count' => "int(11) NULL DEFAULT '0' COMMENT '发放数量'",
            'get_count' => "int(11) unsigned NULL DEFAULT '0' COMMENT '领取数量'",
            'max_fetch' => "int(11) NULL DEFAULT '0' COMMENT '每人最大领取个数 0无限制'",
            'max_day_fetch' => "int(11) NULL DEFAULT '0' COMMENT '每人每日最大领取个数 0无限制'",
            'at_least' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '满多少元使用 0代表无限制'",
            'need_user_level' => "int(11) NULL DEFAULT '0' COMMENT '领取人会员等级'",
            'range_type' => "tinyint(4) NULL DEFAULT '1' COMMENT '使用范围'",
            'get_start_time' => "int(11) NULL DEFAULT '0' COMMENT '领取有效日期开始时间'",
            'get_end_time' => "int(11) NULL DEFAULT '0' COMMENT '领取有效日期结束时间'",
            'start_time' => "int(11) NULL DEFAULT '0' COMMENT '有效日期开始时间'",
            'end_time' => "int(11) NULL DEFAULT '0' COMMENT '有效日期结束时间'",
            'min_price' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '优惠券最小金额'",
            'max_price' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '优惠券最大金额'",
            'term_of_validity_type' => "tinyint(4) NULL DEFAULT '0' COMMENT '有效期类型'",
            'fixed_term' => "tinyint(4) NULL DEFAULT '1' COMMENT '领取之日起N天内有效'",
            'single_type' => "tinyint(4) NULL DEFAULT '0' COMMENT '单品卷'",
            'is_list_visible' => "int(11) NULL DEFAULT '1' COMMENT '领劵列表可见'",
            'is_new_people' => "tinyint(4) NULL DEFAULT '0' COMMENT '新人优惠券(未下支付单)'",
            'remark' => "varchar(10) NULL DEFAULT '' COMMENT '备注'",
            'status' => "tinyint(4) NULL DEFAULT '1' COMMENT '状态[-1:删除;0:禁用;1启用]'",
            'created_at' => "int(10) NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) NULL DEFAULT '0' COMMENT '修改时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='扩展_微商城_优惠券类型表'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_tiny_shop_marketing_coupon_type}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

