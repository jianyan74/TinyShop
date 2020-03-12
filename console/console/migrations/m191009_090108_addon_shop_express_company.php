<?php

use yii\db\Migration;

class m191009_090108_addon_shop_express_company extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_express_company}}', [
            'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
            'merchant_id' => "int(10) unsigned NULL DEFAULT '0' COMMENT '商户id'",
            'title' => "varchar(50) NOT NULL DEFAULT '' COMMENT '物流公司名称'",
            'express_no' => "varchar(20) NOT NULL DEFAULT '' COMMENT '物流编号'",
            'cover' => "varchar(100) NULL DEFAULT '' COMMENT '封面'",
            'mobile' => "varchar(20) NULL DEFAULT '' COMMENT '手机号码'",
            'sort' => "int(5) NULL DEFAULT '999' COMMENT '排序'",
            'is_default' => "tinyint(4) unsigned NULL DEFAULT '0' COMMENT '默认'",
            'status' => "tinyint(4) NULL DEFAULT '1' COMMENT '状态[-1:删除;0:禁用;1启用]'",
            'created_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => "int(10) unsigned NULL DEFAULT '0' COMMENT '更新时间'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        $this->insert('{{%addon_shop_express_company}}',['id'=>'1','merchant_id'=>'1','title'=>'天天快递','express_no'=>'223','cover'=>'http://merchants.local/attachment/images/2019/05/08/image_155729450052485251.jpg','mobile'=>'12345','sort'=>'999','is_default'=>'1','status'=>'1','created_at'=>'1557394736','updated_at'=>'1558059668']);
        $this->insert('{{%addon_shop_express_company}}',['id'=>'2','merchant_id'=>'1','title'=>'顺丰快递','express_no'=>'234','cover'=>'','mobile'=>'','sort'=>'999','is_default'=>'0','status'=>'1','created_at'=>'1558059678','updated_at'=>'1558059678']);
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_express_company}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

