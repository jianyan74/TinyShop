<?php

use common\helpers\Url;
use yii\helpers\Json;
use addons\TinyShop\common\enums\DiscountTypeEnum;

?>

<style>
    .input-group-text {
        font-size: 13px;
    }

    .rf-marketing-table thead tr th {
        word-break: keep-all;
        white-space: nowrap;
    }

    .rf-marketing-table tbody tr td {
        white-space: nowrap;
    }
</style>

<div id="<?= $box_id ?>" class="box-id">
    <a class="blue" href="javascript:void(0)" @click="selectProduct()">选择商品</a>
    <table class="table table-hover table-bordered rf-marketing-table" style="margin-top: 10px">
        <thead>
        <tr>
            <td width="20px"><input type="checkbox" @click="checkAll" v-model="checkedAll"></td>
            <th width="60px">商品图</th>
            <th width="300px">商品信息</th>
            <th>原价(元)</th>
            <th v-for="(column, index) in columns" :style="column.options.style">
                <span v-html="column.label"></span>
            </th>
            <th width="150px">操作</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(product, index) in products">
            <td><input type="checkbox" v-model="product.checked"></td>
            <td><img :src="product.picture" alt="" width="40px" height="40px"></td>
            <td>
                <span v-html="product.name_segmentation"></span> <br>
                <small>库存：{{product.stock}}</small>
            </td>
            <td>
                {{product.price}}
                <span v-if="product.price != product.tmp_max_money"> - {{product.tmp_max_money}}</span>
            </td>
            <td>
                <a href="javascript:void(0);" class="blue" @click="delProduct(index)">删除</a>
                <input type="hidden" class="form-control" v-model="product.id" :name="name + '[' + product.id + '][id]'">
            </td>
        </tr>
        </tbody>
    </table>
</div>

<script>
    var <?= $box_id ?> = new Vue({
        el: '#<?= $box_id ?>',
        data: {
            min: <?= $min ?>,
            max: <?= $max ?>,
            name: "<?= $name ?>",
            settingSku: <?= $setting_sku ?>,
            selectUrl: "<?= !empty($url) ? $url : Url::toRoute(['/tiny-shop/product/product/select', 'multiple' => true, 'is_virtual' => $is_virtual]) ?>",
            discountType: JSON.parse('<?= Json::encode(DiscountTypeEnum::getAllMap()); ?>'),
            columns: JSON.parse('<?= Json::encode($columns); ?>'),
            calculation: JSON.parse('<?= Json::encode($calculation); ?>'),
            difference: JSON.parse('<?= Json::encode($difference); ?>'),
            products: [],
            productIndex: 0, // 选择编辑的商品位置
            product: [], // 选择编辑的商品消息
            productErrors: [], // 报错信息
            checkedAll: false, // 商品全部选中
            checkedSkuAll: false, // SKU 全部选中
        },
        methods: {
            // 添加商品
            addProduct: function (product, isNewRecord = true) {
                // 查找重复
                var productLength = this.products.length;
                if (this.max > 0 && productLength >= this.max) {
                    // rfMsg('已达到添加商品上限');
                    return false;
                }

                for (let i = 0; i < productLength; i++) {
                    if (this.products[i].id === product.id) {
                        return false;
                    }
                }

                product.checked = false;
                product.skuBound = [];

                // 临时计算值
                product.tmp_money = 0;
                product.tmp_min_money = '';
                product.tmp_max_money = '';
                product.tmp_total_money = 0;
                product.tmp_total_number = 1;
                product.tmp_discount_type = 0;
                product.tmp_discount_type_explain = '-';

                // 字段分行
                var nameSegmentation = '';
                for (let j = 0; j < product.name_segmentation.length; j++) {
                    nameSegmentation += product.name_segmentation[j];
                    if (j > 0 && ((j % 20) === 0)) {
                        nameSegmentation += "<br>";
                    }
                }
                product.name_segmentation = nameSegmentation;

                // 初始化值
                if (isNewRecord === true) {
                    for (let j = 0; j < this.columns.length; j++) {
                        var columnField = this.columns[j].name;
                        // 映射默认值
                        var valueFieldMap = this.columns[j].valueFieldMap;
                        if (valueFieldMap !== '') {
                            this.columns[j].value = product[valueFieldMap];
                        }

                        if (product[columnField] === '' || product[columnField] === undefined) {
                            product[columnField] = this.columns[j].value;
                        }
                    }
                }

                // 设置 sku
                if (product.sku) {
                    product.tmp_total_number = 0;

                    for (let i = 0; i < product.sku.length; i++) {
                        product.sku[i].checked = false;

                        // 初始化值
                        for (let j = 0; j < this.columns.length; j++) {
                            columnField = this.columns[j].name;
                            // 映射默认值
                            valueFieldMap = this.columns[j].valueFieldMap;
                            if (valueFieldMap !== '') {
                                product.sku[i][columnField] = product.sku[i][valueFieldMap];
                            }

                            if (!product.sku[i][columnField]) {
                                product.sku[i][columnField] = product[columnField];
                            }
                        }

                        if (!product.sku[i].number) {
                            product.sku[i].number = 0;
                        }
                        // 临时计算值
                        product.sku[i].tmp_money = 0;
                        product.sku[i].tmp_total_number = product.sku[i].number;
                        product.sku[i].tmp_total_money = parseFloat(product.sku[i][this.calculation.numberField]) * parseFloat(product.sku[i][this.calculation.moneyField]);
                        product.sku[i].tmp_discount_type = 0;
                        product.sku[i].tmp_discount_type_explain = product.tmp_discount_type_explain;
                        if (product.tmp_min_money === '') {
                            product.tmp_min_money = product.sku[i].price;
                        }
                        if (product.tmp_max_money === '') {
                            product.tmp_max_money = product.sku[i].price;
                        }
                        if (product.tmp_min_money > product.sku[i].price) {
                            product.tmp_min_money = product.sku[i].price
                        }
                        if (product.tmp_max_money < product.sku[i].price) {
                            product.tmp_max_money = product.sku[i].price
                        }
                        product.tmp_total_money += parseFloat(product.sku[i].tmp_total_money);
                        product.tmp_total_number += parseInt(product.sku[i].number);

                        // 对比字段
                        product.sku[i][this.difference.relevancy] = product.sku[i][this.difference.from] - product.sku[i][this.difference.to];
                    }
                }

                // 重置值
                for (let j = 0; j < this.columns.length; j++) {
                    columnField = this.columns[j].name;
                    if (
                        this.columns[j].options.sku === true &&
                        this.columns[j].type !== 'text' &&
                        product[columnField] > 0
                    ) {
                        product[columnField] = '';
                    }
                }

                // 对比字段
                product[this.difference.relevancy] = product[this.difference.from] - product[this.difference.to];

                product.skuBound = this.bound(product);
                this.products.push(product);

                // console.log(this.products)
            },
            // 删除商品
            delProduct: function (index) {
                this.products.splice(index, 1);

                // 触发关闭选择
                $(document).trigger('select-product-delete');
            },
            // 计算最大值/最小值
            bound: function (product) {
                // 初始化最小值和最大值
                var columns = this.columns;
                var skuBound = [];
                var skuLength = product.sku.length;
                for (let j = 0; j < columns.length; j++) {
                    // 启用显示
                    if (columns[j].options.sku === true) {
                        columnField = columns[j].name;
                        skuBound[columnField] = {
                            'min': '',
                            'max': '',
                            'explain': ''
                        };

                        // 循环 sku
                        for (let i = 0; i < skuLength; i++) {
                            if (skuBound[columnField].min === '') {
                                skuBound[columnField].min = product.sku[i][columnField];
                                skuBound[columnField].max = product.sku[i][columnField];
                            }

                            // 最小值
                            if (parseFloat(product.sku[i][columnField]) < parseFloat(skuBound[columnField].min)) {
                                skuBound[columnField].min = product.sku[i][columnField];
                            }

                            // 最大值
                            if (parseFloat(product.sku[i][columnField]) > parseFloat(skuBound[columnField].max)) {
                                skuBound[columnField].max = product.sku[i][columnField];
                            }
                        }

                        // 写入数据
                        skuBound[columnField].explain = skuBound[columnField].min + ' - ' + skuBound[columnField].max;
                        if (skuBound[columnField].min === skuBound[columnField].max) {
                            skuBound[columnField].explain = skuBound[columnField].min;
                        }
                    }
                }

                return skuBound;
            },
            getPrice: function () {
                var price = 0;
                var productLength = this.products.length;
                for (let i = 0; i < productLength; i++) {
                    price += parseFloat(this.products[i].tmp_total_money);
                }

                return price.toFixed(2);
            },
            getNumber: function () {
                var number = 0;
                var productLength = this.products.length;
                for (let i = 0; i < productLength; i++) {
                    number += parseFloat(this.products[i].tmp_total_number);
                }

                return number.toFixed(2);
            },
            // 选中商品
            checkAll: function () {
                var productLength = this.products.length;
                for (let i = 0; i < productLength; i++) {
                    this.products[i].checked = this.checkedAll === false;
                }
            },
            // 选中规格
            checkSkuAll: function (index) {
                var skuLength = this.product.sku.length;
                for (let i = 0; i < skuLength; i++) {
                    this.product.sku[i].checked = this.checkedSkuAll === false;
                }
            },
            selectProduct: function () {
                layer.open({
                    type: 2,
                    title: '商品选择',
                    shade: 0.3,
                    offset: "10%",
                    shadeClose: true,
                    btn: ['保存', '关闭'],
                    yes: function (index, layero) {
                        var body = layer.getChildFrame('body', index);
                        var productObj = body.find('.product_id');
                        // 获取选中的值
                        productObj.each(function (i, item) {
                            if (item.checked) {
                                <?= $box_id ?>.addProduct($(item).data('value'))
                            }
                        });

                        layer.closeAll();

                        // 触发关闭选择
                        $(document).trigger('select-product-close');
                    },
                    btn2: function () {
                        layer.closeAll();
                    },
                    area: ['80%', '80%'],
                    content: this.selectUrl
                });
            },
        },
        // 初始化
        mounted() {
            var products = JSON.parse('<?= Json::encode($products); ?>');
            for (let i = 0; i < products.length; i++) {
                this.addProduct(products[i], false);
            }

            setTimeout(function () {
                // 触发
                $(document).trigger('select-product-ready');
            }, 300);
        },
    });
</script>
