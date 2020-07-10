<?php

use yii\widgets\ActiveForm;
use addons\TinyShop\common\enums\AdvLocalEnum;
use addons\TinyShop\common\enums\AdvJumpTypeEnum;
use common\helpers\ArrayHelper;
use common\widgets\webuploader\Files;
use kartik\datetime\DateTimePicker;

$addon = <<< HTML
<div class="input-group-append">
    <span class="input-group-text">
        <i class="fas fa-calendar-alt"></i>
    </span>
</div>
HTML;

$this->title = $model->isNewRecord ? '创建' : '编辑';
$this->params['breadcrumbs'][] = ['label' => '幻灯片', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">基本信息</h3>
            </div>
            <?php $form = ActiveForm::begin([
                'fieldConfig' => [
                    'template' => "<div class='col-sm-2 text-right'>{label}</div><div class='col-sm-10'>{input}{hint}{error}</div>",
                ]
            ]); ?>
            <div class="box-body">
                <div class="col-lg-12">
                    <?= $form->field($model, 'title')->textInput(); ?>
                    <?= $form->field($model, 'is_title_show')->checkbox(); ?>
                    <?= $form->field($model, 'location')->dropDownList(AdvLocalEnum::getMap())->hint('轮播图才会进行循环滚动，其他的如果添加了多条，只会在有效时间内取第一条'); ?>
                    <?= $form->field($model, 'sort')->textInput(); ?>
                    <?= $form->field($model, 'cover')->widget(Files::class, [
                        'config' => [
                            'pick' => [
                                'multiple' => false,
                            ]
                        ]
                    ])->hint("首页轮播建议大小： 高150xp x 宽350xp <br> 普通广告建议大小： 高100xp x 宽400xp"); ?>
                    <?= $form->field($model, 'silder_text')->textarea(); ?>
                    <?= $form->field($model, 'start_time')->widget(DateTimePicker::class, [
                        'language' => 'zh-CN',
                        'options' => [
                            'value' => $model->isNewRecord ? date('Y-m-d H:i:s') : date('Y-m-d H:i:s',$model->start_time),
                        ],
                        'pluginOptions' => [
                            'format' => 'yyyy-mm-dd hh:ii',
                            'todayHighlight' => true,//今日高亮
                            'autoclose' => true,//选择后自动关闭
                            'todayBtn' => true,//今日按钮显示
                        ]
                    ]); ?>
                    <?= $form->field($model, 'end_time')->widget(DateTimePicker::class, [
                        'language' => 'zh-CN',
                        'options' => [
                            'value' => $model->isNewRecord ? date('Y-m-d H:i:s') : date('Y-m-d H:i:s',$model->end_time),
                        ],
                        'pluginOptions' => [
                            'format' => 'yyyy-mm-dd hh:ii',
                            'todayHighlight' => true,//今日高亮
                            'autoclose' => true,//选择后自动关闭
                            'todayBtn' => true,//今日按钮显示
                        ]
                    ]);?>
                    <?= $form->field($model, 'jump_link')->textInput()->hint('跳转列表可以不填，跳转详情请填写对应的id, 不填写则不进行跳转'); ?>
                    <?= $form->field($model, 'jump_type')->dropDownList(ArrayHelper::merge(['' => '请选择'], AdvJumpTypeEnum::getMap())); ?>
                    <?= $form->field($model, 'status')->radioList(\common\enums\StatusEnum::getMap()); ?>
                </div>
            </div>
            <div class="box-footer text-center">
                <button class="btn btn-primary" type="submit">保存</button>
                <span class="btn btn-white" onclick="history.go(-1)">返回</span>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>