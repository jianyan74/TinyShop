<?php

use yii\db\Migration;

class m191009_090108_addon_shop_marketing_full_mail extends Migration
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
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AVG_ROW_LENGTH=16384 ROW_FORMAT=DYNAMIC COMMENT='满额包邮'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        $this->insert('{{%addon_shop_marketing_full_mail}}',['id'=>'2','merchant_id'=>'1','is_open'=>'0','full_mail_money'=>'100.00','no_mail_province_ids'=>'310000,320000,540000,650000,810000,820000,830000','no_mail_city_ids'=>'310100,320100,320200,320300,320400,320500,320600,320700,320800,320900,321000,321100,321200,321300,540100,540200,540300,540400,540500,542500,650100,650200,650400,650500,652300,652700,652800,652900,653000,653100,653200,654000,654200,654300,659000,810100,810200,810300,820100,820200,830100,830200,830300,830400,830500,830600,830700,830800,830900','status'=>'1','created_at'=>'1557736240','updated_at'=>'1568886624']);
        
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

