<?php

use yii\db\Migration;

class m191009_090108_addon_shop_marketing_coupon extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addon_shop_marketing_coupon}}', [
            'id' => "int(11) NOT NULL AUTO_INCREMENT COMMENT '优惠券id'",
            'coupon_type_id' => "int(11) unsigned NOT NULL DEFAULT '0' COMMENT '优惠券类型id'",
            'merchant_id' => "int(11) unsigned NULL DEFAULT '0' COMMENT '店铺Id'",
            'code' => "varchar(100) NULL DEFAULT '' COMMENT '优惠券编码'",
            'member_id' => "int(11) NULL DEFAULT '0' COMMENT '领用人'",
            'use_order_id' => "int(11) NULL DEFAULT '0' COMMENT '优惠券使用订单id'",
            'create_order_id' => "int(11) NULL DEFAULT '0' COMMENT '创建订单id(优惠券只有是完成订单发放的优惠券时才有值)'",
            'money' => "decimal(10,2) NULL DEFAULT '0.00' COMMENT '面额'",
            'state' => "tinyint(4) NULL DEFAULT '0' COMMENT '优惠券状态 0未领用 1已领用（未使用） 2已使用 3已过期'",
            'get_type' => "int(11) NULL DEFAULT '0' COMMENT '获取方式1订单2.首页领取'",
            'fetch_time' => "int(11) NULL DEFAULT '0' COMMENT '领取时间'",
            'use_time' => "int(11) NULL DEFAULT '0' COMMENT '使用时间'",
            'start_time' => "int(11) NULL DEFAULT '0' COMMENT '有效期开始时间'",
            'end_time' => "int(11) NULL DEFAULT '0' COMMENT '有效期结束时间'",
            'status' => "int(4) NULL DEFAULT '1' COMMENT '状态'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AVG_ROW_LENGTH=148 ROW_FORMAT=DYNAMIC COMMENT='优惠券表'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        $this->insert('{{%addon_shop_marketing_coupon}}',['id'=>'83','coupon_type_id'=>'3','merchant_id'=>'1','code'=>'155858699725572','member_id'=>'1','use_order_id'=>'0','create_order_id'=>'0','money'=>'0.00','state'=>'1','get_type'=>'0','fetch_time'=>'1560246218','use_time'=>'0','start_time'=>'1560246218','end_time'=>'1561283018','status'=>'1']);
        $this->insert('{{%addon_shop_marketing_coupon}}',['id'=>'84','coupon_type_id'=>'3','merchant_id'=>'1','code'=>'155858699774046','member_id'=>'1','use_order_id'=>'0','create_order_id'=>'0','money'=>'0.00','state'=>'1','get_type'=>'0','fetch_time'=>'1560246222','use_time'=>'0','start_time'=>'1560246222','end_time'=>'1561283022','status'=>'1']);
        $this->insert('{{%addon_shop_marketing_coupon}}',['id'=>'85','coupon_type_id'=>'3','merchant_id'=>'1','code'=>'155858699764630','member_id'=>'1','use_order_id'=>'111','create_order_id'=>'0','money'=>'2.00','state'=>'2','get_type'=>'0','fetch_time'=>'1560392842','use_time'=>'1560393721','start_time'=>'1560392842','end_time'=>'1561429642','status'=>'1']);
        $this->insert('{{%addon_shop_marketing_coupon}}',['id'=>'86','coupon_type_id'=>'3','merchant_id'=>'1','code'=>'155858699732280','member_id'=>'1','use_order_id'=>'112','create_order_id'=>'0','money'=>'2.00','state'=>'2','get_type'=>'0','fetch_time'=>'1560394156','use_time'=>'1560394332','start_time'=>'1560394156','end_time'=>'1561430956','status'=>'1']);
        $this->insert('{{%addon_shop_marketing_coupon}}',['id'=>'87','coupon_type_id'=>'3','merchant_id'=>'1','code'=>'155858699765438','member_id'=>'1','use_order_id'=>'0','create_order_id'=>'0','money'=>'2.00','state'=>'1','get_type'=>'0','fetch_time'=>'1563589642','use_time'=>'0','start_time'=>'1563589642','end_time'=>'1564626442','status'=>'1']);
        $this->insert('{{%addon_shop_marketing_coupon}}',['id'=>'88','coupon_type_id'=>'3','merchant_id'=>'1','code'=>'155858699747932','member_id'=>'1','use_order_id'=>'0','create_order_id'=>'0','money'=>'2.00','state'=>'1','get_type'=>'0','fetch_time'=>'1563589678','use_time'=>'0','start_time'=>'1563589678','end_time'=>'1564626478','status'=>'1']);
        $this->insert('{{%addon_shop_marketing_coupon}}',['id'=>'89','coupon_type_id'=>'3','merchant_id'=>'1','code'=>'155858699771447','member_id'=>'0','use_order_id'=>'0','create_order_id'=>'0','money'=>'0.00','state'=>'0','get_type'=>'0','fetch_time'=>'0','use_time'=>'0','start_time'=>'0','end_time'=>'0','status'=>'1']);
        $this->insert('{{%addon_shop_marketing_coupon}}',['id'=>'90','coupon_type_id'=>'3','merchant_id'=>'1','code'=>'155858699751790','member_id'=>'0','use_order_id'=>'0','create_order_id'=>'0','money'=>'0.00','state'=>'0','get_type'=>'0','fetch_time'=>'0','use_time'=>'0','start_time'=>'0','end_time'=>'0','status'=>'1']);
        $this->insert('{{%addon_shop_marketing_coupon}}',['id'=>'91','coupon_type_id'=>'3','merchant_id'=>'1','code'=>'155858699731335','member_id'=>'0','use_order_id'=>'0','create_order_id'=>'0','money'=>'0.00','state'=>'0','get_type'=>'0','fetch_time'=>'0','use_time'=>'0','start_time'=>'0','end_time'=>'0','status'=>'1']);
        $this->insert('{{%addon_shop_marketing_coupon}}',['id'=>'92','coupon_type_id'=>'3','merchant_id'=>'1','code'=>'155858699784147','member_id'=>'0','use_order_id'=>'0','create_order_id'=>'0','money'=>'0.00','state'=>'0','get_type'=>'0','fetch_time'=>'0','use_time'=>'0','start_time'=>'0','end_time'=>'0','status'=>'1']);
        $this->insert('{{%addon_shop_marketing_coupon}}',['id'=>'93','coupon_type_id'=>'4','merchant_id'=>'1','code'=>'155858700778556','member_id'=>'0','use_order_id'=>'0','create_order_id'=>'0','money'=>'0.00','state'=>'0','get_type'=>'0','fetch_time'=>'0','use_time'=>'0','start_time'=>'0','end_time'=>'0','status'=>'1']);
        $this->insert('{{%addon_shop_marketing_coupon}}',['id'=>'94','coupon_type_id'=>'4','merchant_id'=>'1','code'=>'155858700728093','member_id'=>'0','use_order_id'=>'0','create_order_id'=>'0','money'=>'0.00','state'=>'0','get_type'=>'0','fetch_time'=>'0','use_time'=>'0','start_time'=>'0','end_time'=>'0','status'=>'1']);
        $this->insert('{{%addon_shop_marketing_coupon}}',['id'=>'95','coupon_type_id'=>'4','merchant_id'=>'1','code'=>'155858700788193','member_id'=>'0','use_order_id'=>'0','create_order_id'=>'0','money'=>'0.00','state'=>'0','get_type'=>'0','fetch_time'=>'0','use_time'=>'0','start_time'=>'0','end_time'=>'0','status'=>'1']);
        $this->insert('{{%addon_shop_marketing_coupon}}',['id'=>'96','coupon_type_id'=>'4','merchant_id'=>'1','code'=>'155858700786263','member_id'=>'0','use_order_id'=>'0','create_order_id'=>'0','money'=>'0.00','state'=>'0','get_type'=>'0','fetch_time'=>'0','use_time'=>'0','start_time'=>'0','end_time'=>'0','status'=>'1']);
        $this->insert('{{%addon_shop_marketing_coupon}}',['id'=>'97','coupon_type_id'=>'4','merchant_id'=>'1','code'=>'155858700770323','member_id'=>'0','use_order_id'=>'0','create_order_id'=>'0','money'=>'0.00','state'=>'0','get_type'=>'0','fetch_time'=>'0','use_time'=>'0','start_time'=>'0','end_time'=>'0','status'=>'1']);
        $this->insert('{{%addon_shop_marketing_coupon}}',['id'=>'98','coupon_type_id'=>'4','merchant_id'=>'1','code'=>'155858701677161','member_id'=>'0','use_order_id'=>'0','create_order_id'=>'0','money'=>'0.00','state'=>'0','get_type'=>'0','fetch_time'=>'0','use_time'=>'0','start_time'=>'0','end_time'=>'0','status'=>'1']);
        $this->insert('{{%addon_shop_marketing_coupon}}',['id'=>'99','coupon_type_id'=>'4','merchant_id'=>'1','code'=>'155858701658043','member_id'=>'0','use_order_id'=>'0','create_order_id'=>'0','money'=>'0.00','state'=>'0','get_type'=>'0','fetch_time'=>'0','use_time'=>'0','start_time'=>'0','end_time'=>'0','status'=>'1']);
        $this->insert('{{%addon_shop_marketing_coupon}}',['id'=>'100','coupon_type_id'=>'5','merchant_id'=>'1','code'=>'156358975674691','member_id'=>'1','use_order_id'=>'187','create_order_id'=>'0','money'=>'1000.00','state'=>'2','get_type'=>'0','fetch_time'=>'1563589770','use_time'=>'1563591939','start_time'=>'1563589770','end_time'=>'1563676170','status'=>'1']);
        $this->insert('{{%addon_shop_marketing_coupon}}',['id'=>'101','coupon_type_id'=>'5','merchant_id'=>'1','code'=>'156358975625627','member_id'=>'1','use_order_id'=>'188','create_order_id'=>'0','money'=>'1000.00','state'=>'2','get_type'=>'0','fetch_time'=>'1563589790','use_time'=>'1563592144','start_time'=>'1563589790','end_time'=>'1563676190','status'=>'1']);
        $this->insert('{{%addon_shop_marketing_coupon}}',['id'=>'102','coupon_type_id'=>'5','merchant_id'=>'1','code'=>'156359031425882','member_id'=>'0','use_order_id'=>'0','create_order_id'=>'0','money'=>'0.00','state'=>'0','get_type'=>'0','fetch_time'=>'0','use_time'=>'0','start_time'=>'0','end_time'=>'0','status'=>'1']);
        $this->insert('{{%addon_shop_marketing_coupon}}',['id'=>'103','coupon_type_id'=>'5','merchant_id'=>'1','code'=>'156359031490000','member_id'=>'0','use_order_id'=>'0','create_order_id'=>'0','money'=>'0.00','state'=>'0','get_type'=>'0','fetch_time'=>'0','use_time'=>'0','start_time'=>'0','end_time'=>'0','status'=>'1']);
        $this->insert('{{%addon_shop_marketing_coupon}}',['id'=>'104','coupon_type_id'=>'5','merchant_id'=>'1','code'=>'156359031491380','member_id'=>'0','use_order_id'=>'0','create_order_id'=>'0','money'=>'0.00','state'=>'0','get_type'=>'0','fetch_time'=>'0','use_time'=>'0','start_time'=>'0','end_time'=>'0','status'=>'1']);
        $this->insert('{{%addon_shop_marketing_coupon}}',['id'=>'105','coupon_type_id'=>'5','merchant_id'=>'1','code'=>'156359031454772','member_id'=>'0','use_order_id'=>'0','create_order_id'=>'0','money'=>'0.00','state'=>'0','get_type'=>'0','fetch_time'=>'0','use_time'=>'0','start_time'=>'0','end_time'=>'0','status'=>'1']);
        $this->insert('{{%addon_shop_marketing_coupon}}',['id'=>'106','coupon_type_id'=>'5','merchant_id'=>'1','code'=>'156359031427270','member_id'=>'0','use_order_id'=>'0','create_order_id'=>'0','money'=>'0.00','state'=>'0','get_type'=>'0','fetch_time'=>'0','use_time'=>'0','start_time'=>'0','end_time'=>'0','status'=>'1']);
        $this->insert('{{%addon_shop_marketing_coupon}}',['id'=>'107','coupon_type_id'=>'5','merchant_id'=>'1','code'=>'156359031419712','member_id'=>'0','use_order_id'=>'0','create_order_id'=>'0','money'=>'0.00','state'=>'0','get_type'=>'0','fetch_time'=>'0','use_time'=>'0','start_time'=>'0','end_time'=>'0','status'=>'1']);
        $this->insert('{{%addon_shop_marketing_coupon}}',['id'=>'108','coupon_type_id'=>'5','merchant_id'=>'1','code'=>'156359031438623','member_id'=>'0','use_order_id'=>'0','create_order_id'=>'0','money'=>'0.00','state'=>'0','get_type'=>'0','fetch_time'=>'0','use_time'=>'0','start_time'=>'0','end_time'=>'0','status'=>'1']);
        $this->insert('{{%addon_shop_marketing_coupon}}',['id'=>'109','coupon_type_id'=>'5','merchant_id'=>'1','code'=>'156359031431128','member_id'=>'0','use_order_id'=>'0','create_order_id'=>'0','money'=>'0.00','state'=>'0','get_type'=>'0','fetch_time'=>'0','use_time'=>'0','start_time'=>'0','end_time'=>'0','status'=>'1']);
        $this->insert('{{%addon_shop_marketing_coupon}}',['id'=>'110','coupon_type_id'=>'5','merchant_id'=>'1','code'=>'156359031423993','member_id'=>'0','use_order_id'=>'0','create_order_id'=>'0','money'=>'0.00','state'=>'0','get_type'=>'0','fetch_time'=>'0','use_time'=>'0','start_time'=>'0','end_time'=>'0','status'=>'1']);
        $this->insert('{{%addon_shop_marketing_coupon}}',['id'=>'111','coupon_type_id'=>'5','merchant_id'=>'1','code'=>'156359031473758','member_id'=>'0','use_order_id'=>'0','create_order_id'=>'0','money'=>'0.00','state'=>'0','get_type'=>'0','fetch_time'=>'0','use_time'=>'0','start_time'=>'0','end_time'=>'0','status'=>'1']);
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addon_shop_marketing_coupon}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

