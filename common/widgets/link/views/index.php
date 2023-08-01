<?php

use common\helpers\AddonHelper;
use common\helpers\Url;
use common\enums\StatusEnum;
use addons\TinyShop\common\enums\MarketingEnum;

$inAddon = Yii::$app->params['inAddon'];
Yii::$app->params['inAddon'] = false;
// 大转盘
$bigWheelUrl = Url::toApi(['/big-wheel/v1/activity/list']);
$circleUrl  = Url::toApi(['/tiny-circle/v1/circle/list']);
$articleUrl  = Url::toApi(['/tiny-blog/v1/article/list']);
Yii::$app->params['inAddon'] = $inAddon;

?>

<link href=<?= AddonHelper::filePath() ?>plugins/drap/css/chunk-vendors.03e1740d.css rel=stylesheet>
<link href=<?= AddonHelper::filePath() ?>plugins/drap/css/app.3f486b3a.css rel=stylesheet>

<style>
    .input-group-text {
        font-size: 14px;
    }
</style>

<div class="input-group">
    <input type="text" value="<?= $title; ?>" class="form-control" id="rfLinkTitle" readonly>
    <input type="hidden" name="<?= $name ?>" value='<?= $value ?>' class="form-control" id="rfLink" readonly>
    <div class="input-group-append">
        <span class="input-group-text" id="rfSelectLinkClear">清空</span>
        <span class="input-group-text" id="rfSelectLink">选择链接</span>
    </div>
</div>

<script>
    var rfConfig = {
        'dev': false, // 是否显示参数
        'type': '',
        'isNewRecord': <?= StatusEnum::DISABLED ?>,
        'filePath': '<?= AddonHelper::filePath()?>plugins/drap/', // 资源前缀
        'viewUrl': '', // 获取地址
        'customPageUrl': '<?= Url::to(['custom-page/list', 'merchant_id' => $merchant_id])?>', // 微页面
        'productUrl': '<?= Url::toApi(['v1/product/product/list', 'merchant_id' => $merchant_id])?>', // 商品列表
        // 拼团
        'wholesaleUrl': '<?= Url::toApi(['v1/marketing/wholesale/list', 'merchant_id' => $merchant_id])?>',
        'wholesaleProductUrl': '<?= Url::toApi(['v1/marketing/product/list', 'marketing_type' => MarketingEnum::WHOLESALE, 'merchant_id' => $merchant_id])?>',
        // 秒杀
        'secKillUrl': '<?= Url::toApi(['v1/marketing/sec-kill/list', 'merchant_id' => $merchant_id])?>',
        'secKillProductUrl': '<?= Url::toApi(['v1/marketing/product/list', 'marketing_type' => MarketingEnum::SEC_KILL, 'merchant_id' => $merchant_id])?>',
        // 团购
        'groupBuyUrl': '<?= Url::toApi(['v1/marketing/group-buy/list', 'merchant_id' => $merchant_id])?>',
        'groupBuyProductUrl': '<?= Url::toApi(['v1/marketing/product/list', 'marketing_type' => MarketingEnum::GROUP_BUY, 'merchant_id' => $merchant_id])?>',
        // 砍价
        'bargainUrl': '<?= Url::toApi(['v1/marketing/bargain/list', 'merchant_id' => $merchant_id])?>',
        'bargainProductUrl': '<?= Url::toApi(['v1/marketing/product/list', 'marketing_type' => MarketingEnum::BARGAIN, 'merchant_id' => $merchant_id])?>',
        // 限时折扣
        'discountUrl': '<?= Url::toApi(['v1/marketing/discount/list', 'merchant_id' => $merchant_id])?>',
        'discountProductUrl': '<?= Url::toApi(['v1/marketing/product/list', 'marketing_type' => MarketingEnum::DISCOUNT, 'merchant_id' => $merchant_id])?>',
        // 预售
        'preSellUrl': '<?= Url::toApi(['v1/marketing/pre-sell/list', 'merchant_id' => $merchant_id])?>', // 预售
        'preSellProductUrl': '<?= Url::toApi(['v1/marketing/product/list', 'marketing_type' => MarketingEnum::PRE_SELL, 'merchant_id' => $merchant_id])?>', // 预售
        // 组合套餐
        'combinationUrl': '<?= Url::toApi(['v1/marketing/combination/list', 'merchant_id' => $merchant_id])?>',
        // 其他
        'couponUrl': '<?= Url::toApi(['v1/marketing/coupon-type/list', 'merchant_id' => $merchant_id]); ?>', // 优惠券
        'merchantUrl': '<?= Url::toApi(['v1/merchant/merchant/list', 'merchant_id' => $merchant_id])?>', // 商家列表
        'cateUrl': '<?= Url::toApi(['v1/product/cate/index'])?>', // 分类列表
        'bigWheelUrl': '<?= $bigWheelUrl ?>', // 大转盘
        'circleUrl': '<?= $circleUrl ?>', // 社区文章
        'articleUrl': '<?= $articleUrl ?>' // 博客文章
    }
</script>

<script src=<?= AddonHelper::filePath() ?>plugins/drap/js/chunk-vendors.0b90c59b.js></script>
<script src=<?= AddonHelper::filePath() ?>plugins/drap/js/app.7199d3b1.js></script>

<script>
    $(document).on("click", "#rfSelectLink", function () {
        window.vm.handleClickModalVisible()
    })

    $(document).on("click", "#rfSelectLinkClear", function () {
        $('#rfLinkTitle').val('');
        $('#rfLink').val(JSON.stringify([]))
    })

    $(document).on("click", ".el-button--primary", function () {
        var linkData = window.vm.linkData;
        if (linkData.title) {
            $('#rfLinkTitle').val(linkData.title)
        } else if (linkData.link_url) {
            $('#rfLinkTitle').val(linkData.title)
        }

        $('#rfLink').val(JSON.stringify(linkData))

        console.log(linkData)
    })
</script>

