<?php

$this->title = '出库单：' . $model->order_sn;

use addons\TinyShop\common\enums\ShippingTypeEnum;

$num = 1;

?>
<style type="text/css" media="screen,print">
    body {
        background: #ffffff;
        color: #000;
    }

    h2 {
        text-align: center;
        padding-bottom: 20px;
        font-size: 24px;
    }

    h4 {
        font-weight: bold;
        font-size: 16px;
    }

    .content-header {
        display: none;
    }

    td {
        padding: 5px 10px;
        vertical-align: top;
        font-size: 12px;
    }

    th {
        padding: 5px 10px;
    }
</style>
<style media="print">
    @page {
        size: auto;  /* auto is the initial value */
        margin: 0mm; /* this affects the margin in the printer settings */
    }
</style>

<div style="float: right; padding-top: 10px" id="print-input">
    <input type=button name='button_export' title='打印' onclick=preview(1) value='打印' >
</div>
<div class="print-area">
    <table cellpadding="0" cellspacing="0" width="100%" style="direction: ltr; height: 100%;">
        <tr>
            <td style="width: 100%; height: 100%; padding: 24px 0;">
                <div style="background-color: #ffffff; margin: 0 auto; padding: 0 44px 0 46px; width: 800px; text-align: left;">
                    <h2 style="text-align:center;"><span style="vertical-align:middle;">出库单</span></h2>
                    <table cellpadding="0" cellspacing="0" width="100%" style="margin-bottom:30px;">
                        <tr>
                            <td width="50%" valign="top">
                                <table cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td width="30%" style="text-align:right;">
                                            客户:
                                        </td>
                                        <td width="70%">
                                            <?= $model->receiver_realname; ?>
                                        </td>
                                    </tr>
                                    <?php if ($model->shipping_type == ShippingTypeEnum::PICKUP) : ?>
                                        <tr>
                                            <td style="text-align:right;">配送类型:</td>
                                            <td>客户自提</td>
                                        </tr>
                                    <?php else : ?>
                                        <tr>
                                            <td style="text-align:right;">
                                                客户地址:
                                            </td>
                                            <td>
                                                <?= $model->receiver_name; ?>
                                                , <?= $model->receiver_details; ?> <?= $model->receiver_zip; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="text-align:right;">
                                                客户电话:
                                            </td>
                                            <td>
                                                <?= $model->receiver_mobile; ?>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </table>
                            </td>
                            <td width="50%" valign="top">
                                <table cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td style="text-align:right;">
                                            配送区域:
                                        </td>
                                        <td>
                                            <?= $model->receiver_name?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="text-align:right;">
                                            订单编号:
                                        </td>
                                        <td>
                                            ID#<?= $model->id; ?><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="text-align:right;">
                                            出库人:
                                        </td>
                                        <td>
                                            <span style="border-bottom:1px solid #000;padding:0 60px;">&nbsp;</span>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <table cellpadding="0" cellspacing="0" width="100%"
                           style="border:1px solid #000;border-bottom:0px;">
                        <td>订单号：<?= $model->order_sn; ?></td>
                        <td style="text-align: right">下单日期：<?= Yii::$app->formatter->asDatetime($model->pay_time); ?></td>
                    </table>
                    <?php if (!empty($product)) { ?>
                        <table cellpadding="0" cellspacing="0" width="100%"
                               style="border:1px solid #000;border-bottom:0px;border-left:0px;">
                            <tr>
                                <th width="8%"
                                    style="border-bottom:1px solid #000;border-right:1px solid #000;border-left:1px solid #000;">
                                    序号
                                </th>
                                <th style="border-bottom:1px solid #000;border-right:1px solid #000;text-align:center">
                                    商品名称
                                </th>
                                <th width="10%"
                                    style="border-bottom:1px solid #000;border-right:1px solid #000;text-align:center">
                                    规格
                                </th>
                                <th width="20%"
                                    style="border-bottom:1px solid #000;border-right:1px solid #000;text-align:center">
                                    类别
                                </th>
                                <th width="7%"
                                    style="border-bottom:1px solid #000;border-right:1px solid #000;text-align:center">
                                    数量
                                </th>
                                <th width="7%"
                                    style="border-bottom:1px solid #000;border-right:1px solid #000;text-align:center">
                                    小计
                                </th>
                                <th width="10%"
                                    style="border-bottom:1px solid #000;border-right:1px solid #000;text-align:center">
                                    商品编码
                                </th>
                                <th width="8%" style="border-bottom:1px solid #000;text-align:center">备注</th>
                            </tr>
                            <?php foreach ($product as $detail) { ?>
                                <tr>
                                    <td style="border-bottom:1px solid #000;border-right:1px solid #000;border-left:1px solid #000;">
                                        <?php
                                        echo $num;
                                        $num++;
                                        ?>
                                    </td>
                                    <td style="border-bottom:1px solid #000;border-right:1px solid #000;vertical-align:middle !important;">
                                        <?= $detail['name']; ?>
                                    </td>
                                    <td style="border-bottom:1px solid #000;border-right:1px solid #000;vertical-align:middle !important;text-align:center">
                                        <?= $detail['sku_name']; ?>
                                    </td>
                                    <td style="border-bottom:1px solid #000;border-right:1px solid #000;vertical-align:middle !important;text-align:center">
                                        <?= $detail['cate']['title'] ?? ''; ?>
                                    </td>
                                    <td style="border-bottom:1px solid #000;border-right:1px solid #000;vertical-align:middle !important;text-align:center">
                                        <?= $detail['num']; ?>
                                    </td>
                                    <td style="border-bottom:1px solid #000;border-right:1px solid #000;vertical-align:middle !important;text-align:center">
                                        <?= $detail['product_money']; ?>
                                    </td>
                                    <td style="border-bottom:1px solid #000;border-right:1px solid #000;vertical-align:middle !important;">
                                        <?= $detail['barcode']; ?>
                                    </td>
                                    <td style="border-bottom:1px solid #000;border-right:1px solid #000;vertical-align:middle !important;">

                                    </td>
                                </tr>
                            <?php } ?>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td style="border-right:1px solid #000;text-align:right;">合计</td>
                                <td style="border-right:1px solid #000;border-bottom:1px solid #000;text-align:center"><?= $total; ?></td>
                                <td style="border-right:1px solid #000;border-bottom:1px solid #000;text-align:center"><?= $productMoney; ?></td>
                                <td style="border-right:1px solid #000;border-bottom:1px solid #000;text-align:center"></td>
                                <td style="border-right:1px solid #000;border-bottom:1px solid #000;text-align:center"></td>
                            </tr>
                        </table>
                        <?php if (!empty($model->buyer_message)) : ?>
                            <p style="text-align:center;padding-top:20px;">
                                客户备注：<?= $model->buyer_message; ?>
                            </p>
                        <?php endif; ?>
                        <?php if (!empty($model->seller_memo)) : ?>
                            <p style="text-align:center;padding-top:20px;">
                                发货备注：<?= $model->seller_memo; ?>
                            </p>
                        <?php endif; ?>
                    <?php } ?>
                </div>
            </td>
        </tr>
    </table>
</div>

<script>
    function preview() {
        if (getExplorer() === "IE") {
            pageSetUpNull();
        }

        $("#print-input").css("display", "none");
        $(".rf-main-footer").css("display", "none");
        window.print();
        $("#print-input").css("display", "block");
        $(".rf-main-footer").css("display", "block");
    }

    function pageSetUpNull() {
        var hkey_root, hkey_path, hkey_key;
        hkey_root = "HKEY_CURRENT_USER";
        hkey_path = "\\Software\\Microsoft\\Internet Explorer\\PageSetup\\";
        try {
            var RegWsh = new ActiveXObject("WScript.Shell");
            hkey_key = "header";
            RegWsh.RegWrite(hkey_root + hkey_path + hkey_key, "");
            hkey_key = "footer";
            RegWsh.RegWrite(hkey_root + hkey_path + hkey_key, "");
        } catch (e) {
        }
    }

    function getExplorer() {
        var explorer = window.navigator.userAgent;
        //ie
        if (explorer.indexOf("MSIE") >= 0) {
            return "IE";
        }
        //firefox
        else if (explorer.indexOf("Firefox") >= 0) {
            return "Firefox";
        }
        //Chrome
        else if (explorer.indexOf("Chrome") >= 0) {
            return "Chrome";
        }
        //Opera
        else if (explorer.indexOf("Opera") >= 0) {
            return "Opera";
        }
        //Safari
        else if (explorer.indexOf("Safari") >= 0) {
            return "Safari";
        }
    }
</script>
