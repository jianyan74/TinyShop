<?php

use common\helpers\Html;
use addons\TinyShop\common\enums\OrderStatusEnum;
use addons\TinyShop\common\enums\ShippingTypeEnum;

$br = empty($class) ? '<br>' : '';
?>

<?php if (empty($class)) { ?>
    <?= Html::linkButton(['detail', 'id' => $model->id], '订单详情', [
        'class' => 'cyan',
    ]) . $br ?>
<?php } ?>

    <!--未支付-->
<?php if (in_array($model->order_status, [OrderStatusEnum::NOT_PAY])) { ?>
    <?= Html::linkButton(['pay', 'id' => $model->id], '线下支付', [
        'class' => !empty($class) ? $class : 'orange',
        'onclick' => "rfTwiceAffirm(this, '确定线下支付吗？', '请谨慎操作');return false;",
    ]) . $br ?>

    <?= Html::a('交易关闭', 'javascript:void (0);', [
        'class' => (!empty($class) ? $class : 'orange') . ' orderClose',
    ]) . $br ?>

    <?= Html::linkButton(['product/price-adjustment', 'id' => $model->id], '修改价格', [
        'class' => !empty($class) ? $class : 'green',
        'data-toggle' => 'modal',
        'data-target' => '#ajaxModalLg',
    ]) . $br ?>
<?php } ?>

    <!--已支付-->
<?php if ($model->order_status == OrderStatusEnum::PAY) { ?>
    <!--自提-->
    <?php if ($model->shipping_type == ShippingTypeEnum::PICKUP) { ?>
        <?= Html::linkButton(['pickup', 'id' => $model->id], '提货', [
            'class' => !empty($class) ? $class : 'orange',
            'data-toggle' => 'modal',
            'data-target' => '#ajaxModal',
        ]) . $br ?>
    <?php } else { ?>
        <?= Html::linkButton(['product-express/create', 'id' => $model->id], '发货', [
            'class' => !empty($class) ? $class : 'green',
            'data-toggle' => 'modal',
            'data-target' => '#ajaxModalLg',
        ]) . $br ?>

        <?= Html::linkButton(['address', 'id' => $model->id], '修改地址', [
            'class' => !empty($class) ? $class : 'purple',
            'data-toggle' => 'modal',
            'data-target' => '#ajaxModal',
        ]) . $br ?>
    <?php } ?>
<?php } ?>

    <!--已发货-->
<?php if ($model->order_status == OrderStatusEnum::SHIPMENTS) { ?>
    <?= Html::a('确认收货', 'javascript:void (0);', [
        'class' => (!empty($class) ? $class : 'orange') . ' orderDelivery',
    ]) . $br ?>
<?php } ?>


    <!--订单关闭-->
<?php if ($model->order_status == OrderStatusEnum::REPEAL) { ?>
    <?= Html::delete(['destroy', 'id' => $model->id,], '删除订单', [
        'class' => !empty($class) ? $class : 'red',
    ]) . $br ?>
<?php } ?>

<?= Html::linkButton(['seller-memo', 'id' => $model->id], '备注', [
    'class' => !empty($class) ? $class : '',
    'data-toggle' => 'modal',
    'data-target' => '#ajaxModal',
]) ?>