<?php

use yii\db\Migration;

class m230619_064744_addon_tiny_shop_member extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_tiny_shop_member}}', [
            'id' => "int(11) NOT NULL AUTO_INCREMENT COMMENT '主键'",
            'merchant_id' => "int(11) unsigned NULL DEFAULT '0' COMMENT '商户id'",
            'member_id' => "int(11) unsigned NULL DEFAULT '0' COMMENT '用户ID'",
            'is_pay_password' => "tinyint(4) NULL DEFAULT '0' COMMENT '支付密码[0:禁用;1启用]'",
            'pay_password_hash' => "varchar(150) NOT NULL DEFAULT '' COMMENT '支付密码'",
            'status' => "tinyint(4) NULL DEFAULT '0' COMMENT '状态[-1:删除;0:禁用;1启用]'",
            'created_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '修改时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='会员卡表'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_tiny_shop_member}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

