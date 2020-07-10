<?php
use yii\widgets\ActiveForm;
use common\helpers\Url;
use common\enums\StatusEnum;
use common\helpers\Html;
use addons\TinyShop\common\models\order\OrderProduct;

$count = count($product);
$show = true;

$form = ActiveForm::begin([
    'id' => 'priceAdjustment',
    'enableAjaxValidation' => true,
    'validationUrl' => Url::to(['price-adjustment','id' => $order['id']]),
]);
?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">关闭</span></button>
    <h4 class="modal-title">基本信息</h4>
</div>
<div class="modal-body">
    <table class="table table-bordered table-hover">
        <thead>
        <tr>
            <th>商品</th>
            <th>商品清单</th>
            <th>涨价或减价
                -表示减价</th>
            <th>运费</th>
        </tr>
        </thead>
        <tbody id='list'>
        <?php foreach($product as $item){ ?>
            <?php if ($item['order_status'] == 0) { ?>
                <tr id = <?= $item['id']; ?>>
                    <td>
                        <small><?= $item['product_name']; ?></small><br>
                        <small style="color: #999"><?= $item['sku_name']; ?></small>
                    </td>
                    <td>
                        <span class="pull-left"><?= $item['price']; ?>元</span>
                        <span class="pull-right"><?= $item['num']; ?>件</span>
                    </td>
                    <td>
                        <?= Html::textInput(Html::getInputName($model, 'order_product_ids') . '[' . $item['id'] . ']', $item['adjust_money'], [
                            'class' => 'form-control adjust_money',
                        ])?>
                    </td>
                    <?php if ($show == true) { ?>
                    <td rowspan="<?= $count ?>">
                        <?= $form->field($model, 'shipping_money')->textInput()->label(false); ?>
                        <?php $show = false ?>
                    </td>
                    <?php } ?>
                </tr>
            <?php } ?>
        <?php } ?>
        </tbody>
    </table>

    <div class="row">
        <div class="col-lg-12">
            商品总价：<?= $order->product_money ?>元; 商品优惠：<?= $order->point_money ?> 元; 运费：<?= $order->shipping_money ?> 元; 实收款：<?= $order->pay_money ?> 元;
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
    <span class="btn btn-primary" onclick="beforSubmit()">保存</span>
</div>
<?php ActiveForm::end(); ?>

<script>
    // 验证提交
    function beforSubmit() {
        var submit = true;
        $('.adjust_money').each(function () {
            if (isNaN($(this).val())) {
                rfMsg('调整价格只能为数字');
                submit = false;
            }
        });

        if (submit === true) {
            $('#priceAdjustment').submit();
        }

        return false;
    }
</script>
