<?php

use common\helpers\Url;
use addons\TinyShop\common\enums\OrderStatusEnum;

$orderStatusMap = OrderStatusEnum::getMap();
?>

<ul class="nav nav-tabs">
    <li <?php if ($order_status === ''){ ?>class="active"<?php } ?>>
        <a href="<?= Url::to(['index']) ?>">全部(<?= $total ?>)</a>
    </li>
    <?php foreach ($orderStatusMap as $key => $item) { ?>
        <li <?php if ($order_status !== '' && $order_status == $key){ ?>class="active"<?php } ?>>
            <a href="<?= Url::to(['index', 'order_status' => $key]) ?>"> <?= $item ?></a>
        </li>
    <?php } ?>
</ul>