<?php

use common\helpers\Url;
use yii\helpers\Json;

?>

<div id="attribute">
    <?= $form->field($model, 'attribute_id')->dropDownList($attribute, [
        'prompt' => '请选择',
        '@change' => "initParams()",
        'v-model' => "attributeId"
    ])->hint('商品可以添加自定义参数，也可以通过参数模板批量设置参数');
    ?>

    <div class="form-group">
        <div class="row" v-if="attributeList.length > 0">
            <div class="col-sm-12">
                <table class="table table-bordered">
                    <colgroup>
                        <col width="30%">
                        <col width="40%">
                        <col width="20%">
                        <col width="10%">
                    </colgroup>
                    <thead>
                    <tr>
                        <th>参数名</th>
                        <th>参数值</th>
                        <th>排序</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(list, index) in attributeList">
                        <td>
                            <div v-if="list.type && list.type > 0">
                                {{list.title}}
                            </div>
                            <div v-else>
                                <input type="text" class="form-control" v-model="list.title">
                            </div>
                        </td>
                        <td>
                            <div v-if="list.type && list.type == 1">
                                <input type="text" class="form-control" v-model="list.data">
                            </div>
                            <div v-else-if="list.type && list.type == 2">
                                <label v-for="(item, key) in list.value">
                                    <input type="radio" name="value" v-model="list.data" :value="item"> <span style="padding-right: 5px">{{item}}</span>
                                </label>
                            </div>
                            <div v-else-if="list.type && list.type == 3">
                                <label v-for="(item, key) in list.value" style="padding-right: 5px">
                                    <input type="checkbox" name="value" v-model="list.data" :value="item"> <span style="padding-right: 5px">{{item}}</span>
                                </label>
                            </div>
                            <div v-else>
                                <input type="text" class="form-control" v-model="list.data">
                            </div>
                        </td>
                        <td><input type="number" class="form-control" v-model="list.sort"></td>
                        <td>
                            <a href="#" class="blue" @click="delParams(index)">删除</a>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div class="hint-block"></div>
                <div class="help-block"></div>
            </div>
        </div>
        <a href="#" class="blue" @click="addParams()">添加参数</a>
    </div>
</div>

<script>
    var attribute = new Vue({
        el: '#attribute',
        data: {
            attributeId: '<?= $model->attribute_id; ?>',
            attributeList: JSON.parse('<?= Json::encode($attributeValue); ?>'),
        },
        methods: {
            addParams: function (index) {
                this.attributeList.push({
                    'data': '',
                    'sort': 999,
                });

                console.log(this.attributeList)
            },
            delParams: function (index) {
                this.attributeList.splice(index, 1);
            },
            initParams: function () {
                $.ajax({
                    type: "get",
                    url: "<?= Url::to(['/common/attribute-value/list'])?>",
                    dataType: "json",
                    data: {attribute_id: this.attributeId},
                    success: function (data) {
                        if (parseInt(data.code) !== 200) {
                            rfWarning(data.message);
                        } else {
                            // 清空数组
                            attribute.attributeList.splice(0, attribute.attributeList.length);

                            for (let i = 0; i < data.data.length; i++) {
                                attribute.attributeList.push(data.data[i]);
                            }
                        }
                    }
                });
            },
        },
        // 初始化
        mounted() {

        },
    });
</script>
