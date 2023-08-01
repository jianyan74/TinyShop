<?php

use yii\helpers\Json;
use common\helpers\Url;

?>

<div id="<?= $box_id ?>">
    <a class="blue" href="javascript:void(0)" @click="selectCouponType()">选择优惠券</a>
    <table class="table table-hover table-bordered" style="margin-top: 10px">
        <thead>
        <tr>
            <th>ID</th>
            <th>优惠券信息</th>
            <th>优惠内容</th>
            <th>参与商品</th>
            <th v-for="(column, index) in columns" :style="column.options.style">
                <span v-html="column.label"></span>
            </th>
            <th style="width: 50px">操作</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(couponType, index) in couponTypes">
            <td>{{couponType.id}}</td>
            <td>
                {{couponType.title}} <br>
                <small>库存：{{couponType.stock}}</small>
            </td>
            <td>{{couponType.discount}}</td>
            <td>{{couponType.range_type}}</td>
            <th v-for="(column, key) in columns">
                <!-- 文本框-->
                <input type="text" class="form-control" v-model="couponType[column.name]" @change="changeCouponType(index, key, column.name)" :name="name + '[' + couponType.id + '][' + column.name + ']'" v-if="column.type === 'textInput'">
            </th>
            <td>
                <input type="hidden" class="form-control" v-model="couponType.id" :name="name + '[' + couponType.id + '][id]'">
                <a href="javascript:void(0);" class="blue" @click="delCouponType(index)"> 删除</a>
            </td>
        </tr>
        </tbody>
    </table>
    <div v-if="couponTypes.length === 0">
        <input type="hidden" class="form-control" :name="name">
    </div>
</div>

<script>
    var <?= $box_id ?> = new Vue({
        el: '#<?= $box_id ?>',
        data: {
            min: <?= $min ?>,
            max: <?= $max ?>,
            name: "<?= $name ?>",
            selectUrl: "<?= Url::toRoute(['/tiny-shop/marketing/coupon-type/select', 'multiple' => $multiple]) ?>",
            columns: JSON.parse('<?= Json::encode($columns); ?>'),
            couponTypes: [],
        },
        methods: {
            // 优惠券
            addCouponType: function (couponType, isNewRecord = true) {
                // 查找重复
                var couponTypesLength = this.couponTypes.length;
                if (this.max > 0 && couponTypesLength >= this.max) {
                    return false;
                }

                for (let i = 0; i < couponTypesLength; i++) {
                    if (this.couponTypes[i].id === couponType.id) {
                        return false;
                    }
                }

                // 初始化值
                if (isNewRecord === true) {
                    for (let j = 0; j < this.columns.length; j++) {
                        var columnField = this.columns[j].name;
                        if (couponType[columnField] === '' || couponType[columnField] === undefined) {
                            couponType[columnField] = this.columns[j].value;
                        }
                    }
                }

                this.couponTypes.push(couponType);
            },
            // 删除优惠券
            delCouponType: function (index) {
                this.couponTypes.splice(index, 1);
            },
            // 优惠券变动
            changeCouponType: function (index, columnIndex, field) {
                // 验证输入的规则
                if (this.verify(columnIndex, this.couponTypes[index][field], this.couponTypes[index]) === false) {
                    this.couponTypes[index][field] = 1;
                }
            },
            verify: function (columnIndex, price, data) {
                // price = price.replace(/(^\s*)|(\s*$)/g, "");
                if (price === '' || price === undefined || price === 'undefined') {
                    return false;
                }

                if (!/^\d+(\.\d+)?$/.test(price)) {
                    rfMsg('请输入合法的数字');
                    return false;
                }

                var rule = this.columns[columnIndex].rule;
                // 验证字段对比
                if (rule.comparisonFieldMin !== '') {
                    var comparisonFieldMin = rule.comparisonFieldMin.split(",");
                    if (comparisonFieldMin.length > 1) {
                        for (let i = 0; i < comparisonFieldMin.length; i++) {
                            if (parseFloat(data[comparisonFieldMin[i]]) > price) {
                                rfMsg('请输入不低于 ' + data[comparisonFieldMin[i]] + ' 的数字');
                                return false;
                            }
                        }
                    } else {
                        if (parseFloat(data[rule.comparisonFieldMin]) > price) {
                            rfMsg('请输入不低于 ' + data[rule.comparisonFieldMin] + ' 的数字');
                            return false;
                        }
                    }
                }

                // 验证字段对比
                if (rule.comparisonFieldMax !== '') {
                    var comparisonFieldMax = rule.comparisonFieldMax.split(",");
                    if (comparisonFieldMax.length > 1) {
                        for (let i = 0; i < comparisonFieldMax.length; i++) {
                            if (parseFloat(data[comparisonFieldMax[i]]) < price) {
                                rfMsg('请输入不超过 ' + data[comparisonFieldMax[i]] + ' 的数字');
                                return false;
                            }
                        }
                    } else {
                        if (parseFloat(data[rule.comparisonFieldMax]) < price) {
                            rfMsg('请输入不超过 ' + data[rule.comparisonFieldMax] + ' 的数字');
                            return false;
                        }
                    }
                }

                // 验证最大值
                if (rule.min !== '' && parseFloat(rule.min) > price) {
                    rfMsg('请输入不低于 ' + rule.min + ' 的数字');
                    return false;
                }

                // 验证最小值
                if (rule.max !== '' && parseFloat(rule.max) < price) {
                    rfMsg('请输入不超过 ' + rule.max + ' 的数字');
                    return false;
                }

                return true;
            },
            selectCouponType: function () {
                layer.open({
                    type: 2,
                    title: '选择优惠券',
                    shade: 0.3,
                    offset: "10%",
                    shadeClose: true,
                    btn: ['保存', '关闭'],
                    yes: function (index, layero) {
                        var body = layer.getChildFrame('body', index);
                        var couponObj = body.find('.coupon-type-id');
                        // 获取选中的值
                        couponObj.each(function (i, item) {
                            if (item.checked) {
                                <?= $box_id ?>.addCouponType($(item).data('data'))
                            }
                        });

                        layer.closeAll();
                        // 触发关闭选择
                        $(document).trigger('select-coupon-close');
                    },
                    btn2: function () {
                        layer.closeAll();
                    },
                    area: ['80%', '80%'],
                    content: this.selectUrl
                });
            }
        },
        // 初始化
        mounted() {
            var couponTypes = JSON.parse('<?= Json::encode($couponTypes); ?>');
            for (let i = 0; i < couponTypes.length; i++) {
                this.addCouponType(couponTypes[i], false);
            }
        },
    })
</script>
