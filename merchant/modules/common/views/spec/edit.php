<?php

use yii\widgets\ActiveForm;
use common\helpers\Html;
use addons\TinyShop\common\enums\SpecTypeEnum;

$this->title = $model->isNewRecord ? '创建' : '编辑';
$this->params['breadcrumbs'][] = ['label' => '规格管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$updateName = $model->formName() . '[valueData][update]';
$createName = $model->formName() . '[valueData][create]';

?>

<div class="row">
    <div class="col-12 col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">基本信息</h3>
            </div>
            <?php $form = ActiveForm::begin([]); ?>
            <div class="box-body">
                <div class="col-lg-12">
                    <?= $form->field($model, 'title')->textInput()->label('规格名称'); ?>
                    <?= $form->field($model, 'type')->radioList(SpecTypeEnum::getMap()); ?>
                    <?= $form->field($model, 'sort')->textInput(); ?>
                    <?= $form->field($model, 'explain')->textarea(); ?>
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>规格值</th>
                            <th>排序</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($model->value as $key => $option) { ?>
                            <tr id="<?= $option['id']; ?>">
                                <td class="col-lg-1">
                                    <?= Html::textInput($updateName . '[title][]', $option['title'], [
                                        'class' => 'form-control title',
                                    ]) ?>
                                </td>
                                <td class="col-lg-1">
                                    <?= Html::textInput($updateName . '[sort][]', $option['sort'], [
                                        'class' => 'form-control sort',
                                    ]) ?>
                                </td>
                                <td class="col-lg-2">
                                    <?= Html::hiddenInput($updateName . '[id][]', $option['id']) ?>
                                    <a href="javascript:void(0);" class="delete update blue"> 删除</a>
                                </td>
                            </tr>
                        <?php } ?>
                        <tr id="position">
                            <td colspan="2"><a href="javascript:void(0);" id="add"><i class="iconfont iconplus-circle pointer"></i> 添加规格值</a></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="box-footer text-center">
                <span class="btn btn-primary" onclick="beforeSubmit()">保存</span>
                <span class="btn btn-white" onclick="history.go(-1)">返回</span>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<!-- 添加模板 -->
<script id="addHtml" type="text/html">
    <tr>
        <td class="col-lg-1">
            <?= Html::textInput($createName . '[title][]', '', [
                'class' => 'form-control title',
            ]) ?>
        </td>
        <td class="col-lg-1">
            <?= Html::textInput($createName . '[sort][]', 999, [
                'class' => 'form-control sort',
            ]) ?>
        </td>
        <td class="col-lg-2">
            <a href="javascript:void(0);" class="delete blue"> 删除</a>
        </td>
    </tr>
</script>

<script>
    // 增加属性
    $('#add').click(function () {
        let html = template('addHtml', []);
        $('#position').before(html);
    });

    // 删除属性
    $(document).on("click", ".delete", function () {
        $(this).parent().parent().remove()
    });

    // 验证提交
    function beforeSubmit() {
        var submit = true;
        $('.title').each(function () {
            if (!$(this).val()) {
                rfAffirm('请填写规格值内容');
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

        if (submit === true) {
            $('#w0').submit();
        }
    }
</script>
