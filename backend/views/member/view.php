<?php

use common\helpers\Url;
use common\enums\GenderEnum;
use common\helpers\ImageHelper;
use common\helpers\DebrisHelper;

?>

<style>
    .table {
        border: none;
        border-collapse: separate;
        border-spacing: 5px;
    }
    .table > thead > tr > th,
    .table > tbody > tr > th,
    .table > tfoot > tr > th,
    .table > thead > tr > td,
    .table > tbody > tr > td,
    .table > tfoot > tr > td {
        border-top: 0 solid #e4eaec;
        line-height: 1.42857;
        padding: 8px;
        vertical-align: middle;
    }

    .table tr td {
        padding: 4px 8px;
        height: 28px;
        line-height: 12px;
        border: none;
        text-align: left;
        padding-left: 10px;
        color: #000;
        font-size: 12px;
        border-radius: 4px;
        background: #f1f1f1;
    }
</style>

<div class="row p-m">
    <div class="col-sm-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="<?= Url::to(['view', 'member_id' => $member_id]) ?>">用户信息</a></li>
                <li><a href="<?= Url::to(['footprint', 'member_id' => $member_id]) ?>">足迹</a></li>
            </ul>
            <div class="tab-content">
                <div class="active tab-pane">
                    <div class="box-body">
                        <table class="table">
                            <tbody>
                            <tr>
                                <td rowspan="6" style="background: #ffffff;text-align: right;width: 200px">
                                    <img src="<?= ImageHelper::defaultHeaderPortrait($member->head_portrait)?>" style="width: 200px">
                                </td>
                            </tr>
                            <tr>
                                <td>姓名：<?= $member->realname ?></td>
                                <td>昵称：<?= $member->nickname ?></td>
                                <td>剩余余额：<?= $member->account->user_money ?></td>
                                <td>剩余积分：<?= $member->account->user_integral ?></td>
                            </tr>
                            <tr>
                                <td>会员号：<?= $member->id ?></td>
                                <td>会员级别：<?= $member->memberLevel->name ?? '' ?></td>
                                <td>
                                    生日：<?= $member->birthday ?>
                                </td>
                                <td>
                                    性别：<?= GenderEnum::getValue($member->gender) ?>
                                </td>
                            </tr>
                            <tr>
                                <td>手机号码：<?= $member['mobile'] ?></td>
                                <td>推荐人：<?= $member->parent->nickname ?? '无' ?></td>
                                <td>推广码：<?= $member['promo_code'] ?></td>
                                <td>访问次数：<?= $member['visit_count'] ?></td>
                            </tr>
                            <tr>
                                <td colspan="2">最后一次登录时间：<?= Yii::$app->formatter->asDatetime($member['last_time']) ?></td>
                                <td colspan="2">最后一次登录IP：<?= $member['last_ip'] ?></td>
                            </tr>
                            <tr>
                                <td colspan="4">最后一次登录地点：<?= DebrisHelper::analysisIp($member['last_ip']) ?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- /.tab-content -->
        </div>
        <!-- /.nav-tabs-custom -->
    </div>
</div>
