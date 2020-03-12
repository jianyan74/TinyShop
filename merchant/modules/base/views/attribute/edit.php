<?php

use yii\widgets\ActiveForm;
use common\helpers\Html;

$this->title = $model->isNewRecord ? '创建' : '编辑';
$this->params['breadcrumbs'][] = ['label' => '规格管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$updateName = $model->formName() . '[valueData][update]';
$createName = $model->formName() . '[valueData][create]';
?>

<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">基本信息</h3>
            </div>
            <?php $form = ActiveForm::begin([]); ?>
            <div class="box-body">
                <div class="col-lg-12">
                    <?= $form->field($model, 'title')->textInput(); ?>
                    <?= $form->field($model, 'sort')->textInput(); ?>
                    <?= $form->field($model, 'spec_ids')->checkboxList($specs)->label('关联规格'); ?>
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>排序</th>
                            <th>所属类型</th>
                            <th>属性名称</th>
                            <th>属性值</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($model->value as $key => $option) { ?>
                            <tr id="<?= $option['id']; ?>">
                                <td class="col-lg-1">
                                    <?= Html::textInput($updateName . '[sort][]', $option['sort'], [
                                        'class' => 'form-control sort',
                                    ]) ?>
                                </td>
                                <td class="col-lg-1">
                                    <?= Html::dropDownList($updateName . '[type][]', $option['type'], $valueType,
                                        ['class' => 'form-control type']) ?>
                                </td>
                                <td class="col-lg-2">
                                    <?= Html::textInput($updateName . '[title][]', $option['title'], [
                                        'class' => 'form-control title',
                                    ]) ?>
                                </td>
                                <td class="col-lg-2 value"><?= $option['value']; ?></td>
                                <td class="col-lg-2">
                                    <?= Html::hiddenInput($updateName . '[id][]', $option['id']) ?>
                                    <?= Html::hiddenInput($updateName . '[value][]', $option['value'], [
                                        'class' => 'hideValue',
                                    ]) ?>
                                    <a href="javascript:void(0);" class="editValue" data-toggle="modal" data-target="#ajaxModalLgForAttribute">
                                        <?php if ($option['type'] > 1) {
                                            echo '编辑属性值';
                                        } ?>
                                    </a>
                                    <a href="javascript:void(0);" class="delete update">删除</a>
                                </td>
                            </tr>
                        <?php } ?>
                        <tr id="position">
                            <td colspan="5"><a href="javascript:void(0);" id="add"><i class="icon ion-android-add-circle"></i> 添加属性</a></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="box-footer text-center">
                <span class="btn btn-primary" onclick="beforSubmit()">保存</span>
                <span class="btn btn-white" onclick="history.go(-1)">返回</span>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>


<!--模拟框加载 -->
<div class="modal fade" id="ajaxModalLgForAttribute" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">关闭</span></button>
                <h4 class="modal-title">编辑属性值</h4>
            </div>
            <div class="modal-body">
                <?= Html::textarea('value', '', [
                    'class' => 'form-control',
                    'id' => 'tmpValue',
                    'style' => 'height:200px',
                ]); ?>
                <div class="help-block">一行为一个属性值，多个属性值用换行输入</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
                <button class="btn btn-primary js-confirm" data-dismiss="modal">保存</button>
            </div>
        </div>
    </div>
</div>

<!--添加模板-->
<script id="addHtml" type="text/html">
    <tr>
        <td class="col-lg-1">
            <?= Html::textInput($createName . '[sort][]', 999, [
                'class' => 'form-control sort',
            ]) ?>
        </td>
        <td class="col-lg-1">
            <?= Html::dropDownList($createName . '[type][]', 1, $valueType, ['class' => 'form-control type']) ?>
        </td>
        <td>
            <?= Html::textInput($createName . '[title][]', '', [
                'class' => 'form-control title',
            ]) ?>
        </td>
        <td class="col-lg-2 value"></td>
        <td class="col-lg-2">
            <?= Html::hiddenInput($createName . '[value][]', '', [
                'class' => 'hideValue',
            ]); ?>
            <a href="javascript:void(0);" class="editValue" data-toggle="modal" data-target="#ajaxModalLgForAttribute"></a>
            <a href="javascript:void(0);" class="delete">删除</a>
        </td>
    </tr>
</script>

<script>
    var editValue;
    // 增加属性
    $('#add').click(function () {
        let html = template('addHtml', []);
        $('#position').before(html);
    });

    // 编辑属性值
    $(document).on("click", ".editValue", function () {
        editValue = $(this).parent().parent();
        var value = $(editValue).find('.value').text();
        if (value) {
            var value = value.split(',');
            var html = '';
            console.log(value);
            for (var i = 0; i < value.length; i++) {
                if (value[i] !== "") {
                    if ((i + 1) == value.length) {
                        html += value[i]
                    } else {
                        html += value[i] + "\n";
                    }
                }
            }
        }

        $('#tmpValue').val(html);
    });

    // 确定编辑属性
    $(document).on("click", ".js-confirm", function () {
        var tmpVal = $('#tmpValue').val();
        var value = tmpVal.split("\n");
        var html = '';
        for (var i = 0; i < value.length; i++) {
            if (value[i] !== "" && value[i].length > 0) {
                if ((i + 1) == value.length) {
                    html += value[i]
                } else {
                    html += value[i] + ",";
                }
            }
        }

        $(editValue).find('.hideValue').val(html);
        $(editValue).find('.value').text(html);
    });

    // 删除属性
    $(document).on("click", ".delete", function () {
        $(this).parent().parent().remove()
    });

    // 选择类型
    $(document).on("change", ".type", function () {
        let val = $(this).val();
        console.log(val);
        if (parseInt(val) === 1) {
            $(this).parent().parent().find('.editValue').text('');
        } else {
            $(this).parent().parent().find('.editValue').text('编辑属性值');
        }
    });

    // 验证提交
    function beforSubmit() {
        var submit = true;
        $('.title').each(function () {
            if (!$(this).val()) {
                rfAffirm('请填写属性内容');
                submit = false;
            }
        });

        $('.sort').each(function () {
            if (!$(this).val()) {
                rfAffirm('请填写排序内容');
                submit = false;
            }

            if (isNaN($(this).val())) {
                rfAffirm('排序只能为数字');
                submit = false;
            }
        });

        $('.type').each(function () {
            if ($(this).val() > 1) {
                var value = $(this).parent().parent().find('.value').text();
                if (!value) {
                    rfAffirm('单选/复选框的属性值不能为空');
                    submit = false;
                }
            }
        });

        if (submit === true) {
            $('#w0').submit();
        }
    }
</script>