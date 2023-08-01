<?php

use yii\widgets\ActiveForm;
use common\helpers\StringHelper;
use common\widgets\webuploader\Files;
use kartik\datetime\DateTimePicker;
use addons\TinyShop\common\enums\AdvLocalEnum;

$addon = <<< HTML
<div class="input-group-append">
    <span class="input-group-text">
        <i class="fas fa-calendar-alt"></i>
    </span>
</div>
HTML;

$this->title = $model->isNewRecord ? '创建' : '编辑';
$this->params['breadcrumbs'][] = ['label' => '广告管理', 'url' => ['index']];
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
                    'template' => "<div class='row'><div class='col-lg-1 col-sm-12 text-right'>{label}</div><div class='col-lg-11'>{input}{hint}{error}</div></div>",
                ]
            ]); ?>
            <div class="box-body">
                <div class="col-lg-12">
                    <?= $form->field($model, 'name')->textInput(); ?>
                    <div class="row">
                        <div class="col-sm-1"></div>
                        <div class="col-sm-5">
                            <?= $form->field($model, 'start_time', [
                                'template' => "{label}{input}\n{hint}\n{error}",
                            ])->widget(DateTimePicker::class, [
                                'language' => 'zh-CN',
                                'options' => [
                                    'value' => empty($model->start_time) ? '' : StringHelper::intToDate($model->start_time),
                                ],
                                'pluginOptions' => [
                                    'format' => 'yyyy-mm-dd hh:ii',
                                    'todayHighlight' => true,//今日高亮
                                    'autoclose' => true,//选择后自动关闭
                                    'todayBtn' => true,//今日按钮显示
                                ],
                            ]); ?>
                        </div>
                        <div class="col-sm-5">
                            <?= $form->field($model, 'end_time', [
                                'template' => "{label}{input}\n{hint}\n{error}",
                            ])->widget(DateTimePicker::class, [
                                'language' => 'zh-CN',
                                'options' => [
                                    'value' => empty($model->end_time) ? '' : StringHelper::intToDate($model->end_time),
                                ],
                                'pluginOptions' => [
                                    'format' => 'yyyy-mm-dd hh:ii',
                                    'todayHighlight' => true,//今日高亮
                                    'autoclose' => true,//选择后自动关闭
                                    'todayBtn' => true,//今日按钮显示
                                ],
                            ]); ?>
                        </div>
                    </div>
                    <?= $form->field($model, 'location')->dropDownList(AdvLocalEnum::getMap())->hint('首页的轮播图非装修页面可用<br>轮播图才会进行循环滚动，其他的如果添加了多条，只会在有效时间内取第一条'); ?>
                    <?= $form->field($model, 'cover')->widget(Files::class, [
                        'config' => [
                            'pick' => [
                                'multiple' => false,
                            ]
                        ]
                    ])->hint("首页轮播建议大小： 高 150 像素 * 宽 350 像素 <br> 普通广告建议大小： 高 100 像素 * 宽 400 像素"); ?>
                    <?= $form->field($model, 'describe')->textarea(); ?>
                    <?= $form->field($model, 'extend_link')->widget(\addons\TinyShop\common\widgets\link\Link::class)->hint('不选择则不进行跳转'); ?>
                    <?= $form->field($model, 'sort')->textInput(); ?>
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
