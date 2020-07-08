<?php

use common\helpers\Url;
use addons\TinyShop\common\enums\OrderStatusEnum;

$orderStatusMap = OrderStatusEnum::getBackendMap();
$orderCountGroupByStatus = Yii::$app->tinyShopService->order->getOrderCountGroupByStatus();
$orderCountGroupByStatus = \common\helpers\ArrayHelper::map($orderCountGroupByStatus, 'order_status', 'count');
$orderCountGroupByStatus[-2] = Yii::$app->tinyShopService->orderProduct->getAfterSaleCountByBackend();

?>

<ul class="nav nav-tabs">
    <li <?php if ($order_status == ''){ ?>class="active"<?php } ?>>
        <a href="<?= Url::to(['index']) ?>">全部(<?= $total ?>)</a>
    </li>
    <?php foreach ($orderStatusMap as $key => $item) { ?>
        <li <?php if ($order_status != '' && $order_status == $key){ ?>class="active"<?php } ?>>
            <a href="<?= Url::to(['index', 'order_status' => $key]) ?>">
                <?= $item ?>
                <?php if (isset($orderCountGroupByStatus[$key]) && $orderCountGroupByStatus[$key] > 0 ){ ?>
                    (<?= $orderCountGroupByStatus[$key] ?? 0 ?>)
                <?php } ?>
            </a>
        </li>
    <?php } ?>
</ul>