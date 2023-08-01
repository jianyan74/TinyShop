<?php

use common\helpers\Url;
use common\helpers\ArrayHelper;
use addons\TinyShop\common\enums\OrderStatusEnum;

$orderStatusMap = OrderStatusEnum::getBackendMap();
$orderCountGroupByStatus = Yii::$app->tinyShopService->order->getOrderCountGroupByStatus();
$orderCountGroupByStatus = ArrayHelper::map($orderCountGroupByStatus, 'order_status', 'count');
$orderCountGroupByStatus[OrderStatusEnum::REFUND_ING] = Yii::$app->tinyShopService->order->findAfterSaleCount();

?>

<ul class="nav nav-tabs">
    <li <?php if ($orderStatus == ''){ ?>class="active"<?php } ?>>
        <a href="<?= Url::to(['index']) ?>">全部 (<?= $total ?>)</a>
    </li>
    <?php foreach ($orderStatusMap as $key => $item) { ?>
        <li <?php if ($orderStatus != '' && $orderStatus == $key){ ?>class="active"<?php } ?>>
            <a href="<?= Url::to(['index', 'order_status' => $key]) ?>">
                <?= $item ?>
                <?php if (isset($orderCountGroupByStatus[$key]) && $orderCountGroupByStatus[$key] > 0 ){ ?>
                    (<?= $orderCountGroupByStatus[$key] ?? 0 ?>)
                <?php } ?>
            </a>
        </li>
    <?php } ?>
</ul>
