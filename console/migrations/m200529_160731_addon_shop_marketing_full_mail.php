<?php

use yii\db\Migration;

class m200529_160731_addon_shop_marketing_full_mail extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_marketing_full_mail}}', [
            'id' => "int(11) NOT NULL AUTO_INCREMENT",
            'merchant_id' => "int(11) NOT NULL DEFAULT '0' COMMENT '店铺id'",
            'is_open' => "tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否开启 0未开启 1已开启'",
            'full_mail_money' => "decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '包邮所需订单金额'",
            'no_mail_province_ids' => "text NULL COMMENT '不包邮省id组'",
            'no_mail_city_ids' => "text NULL COMMENT '不包邮市id组'",
            'status' => "tinyint(4) NULL DEFAULT '1' COMMENT '状态[-1:删除;0:禁用;1启用]'",
            'created_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '添加时间'",
            'updated_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '修改时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='扩展_微商城_满额包邮'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_marketing_full_mail}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

