<?php
use yii\widgets\ActiveForm;
use common\helpers\Url;
use common\helpers\Html;

$this->title = $model->isNewRecord ? '创建' : '编辑';
$this->params['breadcrumbs'][] = ['label' => '物流公司', 'url' => ['express-company/index']];
$this->params['breadcrumbs'][] = ['label' => '运费模板', 'url' => ['index', 'company_id' => $company_id]];
$this->params['breadcrumbs'][] = $this->title;
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
                    <table class="table m-b-none">
                        <tbody>
                        <tr>
                            <td align="right"><div class="required"><label>模板名称</label></div></td>
                            <td><?= $form->field($model, 'title')->textInput()->label(false); ?></td>
                        </tr>
                        <tr>
                            <td align="right"><label>模板地区类型</label></td>
                            <td>
                                <?= $form->field($model, 'is_default')->dropDownList([1 => '默认地区', 0 => '指定地区'], [
                                    'disabled' => $model->is_default == true ? false : true,
                                    'id' => 'is_default',
                                ])->label(false); ?>
                                <?php if ($model->is_default == false){ ?>
                                    <?= $form->field($model, 'is_default')->hiddenInput()->label(false); ?>
                                <?php }?>
                            </td>
                        </tr>
                        <tr>
                            <td align="right">
                                <div class="required"><label>选择地区</label></div>
                            </td>
                            <td id="select"></td>
                        </tr>
                        <tr>
                            <td align="right">
                                <label>已选择地区</label>
                            </td>
                            <td>
                                <span class="js-region-info region-info"></span>
                            </td>
                        </tr>
                        <tr>
                            <td align="right"><label>按件数</label></td>
                            <td>
                                <table class="table m-b-none">
                                    <tbody>
                                    <tr>
                                        <td width="20%" align="center">首件(件)</td>
                                        <td width="20%" align="center">首件运费(元)</td>
                                        <td width="20%" align="center">续件(件)</td>
                                        <td width="20%" align="center">续件运费(元)</td>
                                        <td width="20%" align="center">是否启用计件方式运费</td>
                                    </tr>
                                    <tr>
                                        <td><?= $form->field($model, 'bynum_snum')->textInput()->label(false); ?></td>
                                        <td><?= $form->field($model, 'bynum_sprice')->textInput()->label(false); ?></td>
                                        <td><?= $form->field($model, 'bynum_xnum')->textInput()->label(false); ?></td>
                                        <td><?= $form->field($model, 'bynum_xprice')->textInput()->label(false); ?></td>
                                        <td align="center"><?= $form->field($model, 'bynum_is_use')->checkbox(['label' => false]); ?></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td align="right"><label>按重量</label></td>
                            <td>
                                <table class="table m-b-none">
                                    <tbody>
                                    <tr>
                                        <td width="20%" align="center">首重(kg)</td>
                                        <td width="20%" align="center">首重运费(元)</td>
                                        <td width="20%" align="center">续重(kg)</td>
                                        <td width="20%" align="center">续重运费(元)</td>
                                        <td width="20%" align="center">是否启用重量运费</td>
                                    </tr>
                                    <tr>
                                        <td><?= $form->field($model, 'weight_snum')->textInput()->label(false); ?></td>
                                        <td><?= $form->field($model, 'weight_sprice')->textInput()->label(false); ?></td>
                                        <td><?= $form->field($model, 'weight_xnum')->textInput()->label(false); ?></td>
                                        <td><?= $form->field($model, 'weight_xprice')->textInput()->label(false); ?></td>
                                        <td align="center"><?= $form->field($model, 'weight_is_use')->checkbox(['label' => false]); ?></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td align="right"><label>按体积</label></td>
                            <td>
                                <table class="table m-b-none">
                                    <tbody>
                                    <tr>
                                        <td width="20%" align="center">首体积量(m³)</td>
                                        <td width="20%" align="center">首体积运费(元)</td>
                                        <td width="20%" align="center">续体积量(m³)</td>
                                        <td width="20%" align="center">续体积运费(元)</td>
                                        <td width="20%" align="center">是否启用体积计算运费</td>
                                    </tr>
                                    <tr>
                                        <td><?= $form->field($model, 'volume_snum')->textInput()->label(false); ?></td>
                                        <td><?= $form->field($model, 'volume_sprice')->textInput()->label(false); ?></td>
                                        <td><?= $form->field($model, 'volume_xnum')->textInput()->label(false); ?></td>
                                        <td><?= $form->field($model, 'volume_xprice')->textInput()->label(false); ?></td>
                                        <td align="center"><?= $form->field($model, 'volume_is_use')->checkbox(['label' => false]); ?></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="box-footer text-center">
                <button class="btn btn-primary" type="submit">保存</button>
                <span class="btn btn-white" onclick="history.go(-1)">返回</span>
            </div>
            <!-- 地区选择工具 -->
            <?= \common\widgets\area\Area::widget([
                'model' => $model,
                'form' => $form,
                'notChooseProvinceIds' => $allProvinceIds,
                'notChooseCityIds' => $allCityIds,
                'notChooseAreaIds' => $allAreaIds,
            ])?>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<script>
    var allBtn = '<a class="btn btn-default btn-sm" href="javascript:void(0);">默认地区(全国)</a>';
    var assignBtn = '<a class="js-select-city btn btn-primary btn-sm" data-toggle="modal" data-target="#ajaxModalLgForExpress">指定地区城市</a>';

    $(document).ready(function () {
        if ($('#is_default').val() == true) {
            $('#select').html(allBtn);
        } else {
            $('#select').html(assignBtn);
        }
    });

    $('#is_default').change(function () {
        if ($(this).val() == true) {
            $('#select').html(allBtn);
            $(".js-regions input[type='checkbox']").prop("checked", false);
        } else {
            $('#select').html(assignBtn);
        }
    });
</script>