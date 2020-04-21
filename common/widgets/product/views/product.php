<?php

use common\helpers\Url;
use common\helpers\Html;

?>

<div id="<?= $boxId ?>" data-min="<?= $min ?>" data-max="<?= $max ?>">
    <span class="btn btn-primary btn-sm openIframeByProduct" href="<?= Url::to(['/product/product/select', 'multiple' => $multiple, 'is_virtual' => $is_virtual]) ?>">选择商品</span>
    <table class="table table-hover table-bordered" style="margin-top: 10px">
        <thead>
        <tr>
            <th>商品名称</th>
            <th>价格</th>
            <th>库存</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($products as $product) { ?>
            <tr class="TinyShopProduct" data-id=<?= $product['id']; ?> data-name=<?= $product['name']; ?>
                data-price=<?= $product['price']; ?> data-stock=<?= $product['stock']; ?>>
                <td><?= $product['name']; ?></td>
                <td><?= $product['price']; ?></td>
                <td><?= $product['stock']; ?></td>
                <td>
                    <?= Html::hiddenInput($name . '[]', $product['id']) ?>
                    <a href="javascript:void(0);" class="TinyShopProductDelete"> <i class="icon ion-android-cancel"></i></a>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

<!--模板列表-->
<script type="text/html" id="TinyShopProduct">
    {{each data as value i}}
    <tr data-id="{{value.id}}">
        <td>{{value.name}}</td>
        <td>{{value.price}}</td>
        <td>{{value.stock}}</td>
        <td>
            <input type="hidden" name="<?= $name ?>[]" value="{{value.id}}">
            <a href="javascript:void(0);" class="TinyShopProductDelete"> <i class="icon ion-android-cancel"></i></a>
        </td>
    </tr>
    {{/each}}
</script>

<script>
    var allData = [];
    var boxId = "<?= $boxId ?>";
    allData[boxId] = [];

    // 默认加载
    $(document).ready(function () {
        var list = $('#' + boxId).find('.TinyShopProduct');

        list.each(function (i, item) {
            var tmpData = [];
            tmpData['id'] = $(item).data('id');
            tmpData['name'] = $(item).data('name');
            tmpData['price'] = $(item).data('price');
            tmpData['stock'] = $(item).data('stock');
            allData[boxId].push(tmpData);
        });

        // 触发加载完成
        $(document).trigger('select-product-ready');
    });

    // 删除属性
    $(document).on("click", ".TinyShopProductDelete", function () {
        var boxId = $(this).parent().parent().parent().parent().parent().attr('id');
        var id = $(this).parent().parent().data('id');

        $(this).parent().parent().remove();

        for (let j = 0; j < allData[boxId].length; j++) {
            if (parseInt(allData[boxId][j]['id']) === parseInt(id)) {
                allData[boxId].splice(j, 1);
            }
        }

        // 触发删除
        $(document).trigger('select-product-delete');

        console.log(allData[boxId]);
    });

    /* 打一个新窗口 */
    $(document).on("click", ".openIframeByProduct", function (e) {
        var href = $(this).attr('href');
        var boxId = $(this).parent().attr('id');

        openIframeByProduct(href, boxId);
        e.preventDefault();
        return false;
    });

    // 打一个新窗口
    function openIframeByProduct(url, boxId) {
        layer.open({
            type: 2,
            title: '选择商品',
            shade: 0.3,
            offset: "10%",
            shadeClose: true,
            btn: ['保存', '关闭'],
            yes: function (index, layero) {
                var body = layer.getChildFrame('body', index);
                var productObj = body.find('.product_id');
                var tmpSelect = [];
                // 获取选中的值
                productObj.each(function (i, item) {
                    if (item.checked) {
                        var tmpData = [];
                        tmpData['id'] = $(item).data('id');
                        tmpData['name'] = $(item).data('name');
                        tmpData['price'] = $(item).data('price');
                        tmpData['stock'] = $(item).data('stock');

                        var status = true;
                        for (let j = 0; j < allData[boxId].length; j++) {
                            if (parseInt(allData[boxId][j]['id']) === parseInt(tmpData['id'])) {
                                status = false;
                            }
                        }

                        if (status === true) {
                            allData[boxId].push(tmpData);
                            tmpSelect.push(tmpData);
                        }
                    }
                });

                let htmlData = [];
                htmlData['data'] = tmpSelect;
                let html = template('TinyShopProduct', htmlData);
                $('#' + boxId).find('tbody').append(html);

                // 删除上限的产品
                var max = parseInt($('#' + boxId).data('max'));
                console.log(max, allData[boxId].length);
                if (max > 0 && allData[boxId].length > max) {
                    var delNum = allData[boxId].length - max;
                    for (let n = 0; n < delNum; n++) {
                        $('.TinyShopProductDelete:first').click();
                    }
                }

                layer.closeAll();
                // 触发关闭选择
                $(document).trigger('select-product-close');
            },
            btn2: function () {
                layer.closeAll();
            },
            area: ['80%', '80%'],
            content: url
        });

        return false;
    }

    /**
     * 获取总价格
     *
     * @returns {number}
     */
    function getSumPrice() {
        var price = 0;
        for (let j = 0; j < allData[boxId].length; j++) {
            price += parseFloat(allData[boxId][j]['price']);
        }

        return price;
    }
</script>