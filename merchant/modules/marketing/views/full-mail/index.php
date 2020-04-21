<?php
use common\helpers\Html;
use yii\widgets\ActiveForm;
use common\enums\WhetherEnum;

$this->title = '满额包邮';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= $this->title; ?></h3>
            </div>
            <?php $form = ActiveForm::begin([
                'fieldConfig' => [
                    'template' => "<div class='col-sm-2 text-right'>{label}</div><div class='col-sm-10'>{input}\n{hint}\n{error}</div>",
                ]
            ]); ?>
            <div class="box-body">
                <div class="col-lg-12">
                    <?= $form->field($model, 'is_open')->radioList(WhetherEnum::getMap()); ?>
                    <?= $form->field($model, 'full_mail_money')->textInput(); ?>
                    <div class="form-group">
                        <div class="col-sm-2 text-right">
                            <label class="control-label">不包邮地区</label>
                        </div>
                        <div class="col-sm-10">
                            <a class="js-select-city btn btn-primary btn-sm" data-toggle="modal" data-target="#ajaxModalLgForExpress">指定地区城市</a>
                            <div class="help-block"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-2 text-right">
                            <label class="control-label">已选择地区</label>
                        </div>
                        <div class="col-sm-10">
                            <span class="js-region-info region-info"></span>
                            <div class="help-block"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-footer text-center">
                <button class="btn btn-primary" type="submit">保存</button>
            </div>

            <!-- 地区选择工具 -->
            <?= \common\widgets\area\Area::widget([
                'model' => $model,
                'form' => $form,
                'provincesName' => 'no_mail_province_ids',
                'cityName' => 'no_mail_city_ids',
                'level' => 2
            ]) ?>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
