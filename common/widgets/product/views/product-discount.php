<?php

use common\helpers\Url;
use common\helpers\Html;

?>

<div id="<?= $boxId ?>">
    <span class="btn btn-primary btn-sm openIframeByProduct"
          href="<?= Url::to(['/product/product/select', 'multiple' => true]) ?>">选择商品</span>
    <table class="table table-hover table-bordered" style="margin-top: 10px">
        <thead>
        <tr>
            <th>商品名称</th>
            <th>价格</th>
            <th>库存</th>
            <th>折扣(1-100)%</th>
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
                <td><input type="text" class="form-control" value="<?= $product['discount'] ?>" name="<?= $name ?>[<?= $product['id'] ?>][discount]">
                </td>
                <td>
                    <?= Html::hiddenInput($name . "[" . $product['id'] . "]" . '[id]', $product['id']) ?>
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
        <td><input type="text" class="form-control" value="99" name="<?= $name ?>[{{value.id}}][discount]"></td>
        <td>
            <input type="hidden" name="<?= $name ?>[{{value.id}}][id]" value="{{value.id}}">
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

                layer.closeAll();
            },
            btn2: function () {
                layer.closeAll();
            },
            area: ['80%', '80%'],
            content: url
        });

        return false;
    }
</script>