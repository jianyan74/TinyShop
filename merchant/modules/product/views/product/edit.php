<?php

use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use common\helpers\Url;
use common\helpers\ArrayHelper;
use common\enums\StatusEnum;
use common\enums\AppEnum;
use common\enums\WhetherEnum;
use common\widgets\cascader\Cascader;
use common\widgets\ueditor\UEditor;
use common\widgets\webuploader\Files;
use addons\TinyShop\common\enums\ShippingTypeEnum;
use addons\TinyShop\common\enums\ProductTypeEnum;
use addons\TinyShop\common\enums\ProductShippingTypeEnum;

/** @var  $productTypeList */
$productTypeList = ProductTypeEnum::getList();

$this->title = $model->isNewRecord ? '创建' : '编辑';
$this->params['breadcrumbs'][] = ['label' => '商品管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="card card-primary card-outline card-outline-tabs">
    <div class="card-header border-bottom-0">
        <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
            <li class="nav-item"><a class="nav-link symbol-required active" data-toggle="pill" href="#custom-1">商品信息</a></li>
            <li class="nav-item"><a class="nav-link symbol-required" data-toggle="pill" href="#custom-2">库存规格</a></li>
            <li class="nav-item"><a class="nav-link symbol-required" data-toggle="pill" href="#custom-3">封面详情</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#custom-4">商品参数</a></li>
        </ul>
    </div>

    <div class="card-body">
        <?php $form = ActiveForm::begin([
            'id' => 'productForm',
        ]); ?>
        <div class="tab-content">
            <div class="tab-pane fade active show" id="custom-1">
                <?= $form->field($model, 'name')->textInput(); ?>
                <?= $form->field($model, 'type')->hiddenInput()->label(false); ?>
                <?= $form->field($model, 'sketch')->textInput()->hint('在商品详情页标题下面展示卖点信息，建议60字以内'); ?>
                <?php if (Yii::$app->services->devPattern->isB2B2C()) { ?>
                    <?= $form->field($model, 'platformCateId')->widget(Cascader::class, [
                        'data' => $platformCates,
                    ]); ?>
                <?php } ?>
                <?= $form->field($model, 'cateIds')->widget(Cascader::class, [
                    'data' => $cates,
                    'multiple' => true,
                ]); ?>
                <div class="row">
                    <div class="col-sm-6"><?= $form->field($model, 'brand_id')->dropDownList(ArrayHelper::merge([0 => '请选择'], $brands)) ?></div>
                    <div class="col-sm-6"><?= $form->field($model, 'supplier_id')->dropDownList(ArrayHelper::merge([0 => '请选择'], $supplier)) ?></div>
                </div>
                <?= $form->field($model, 'tags')->widget(Select2::class, [
                    'data' => $tags,
                    'options' => [
                        'placeholder' => '请选择标签',
                        'multiple' => true,
                    ],
                    'pluginOptions' => [
                        'tags' => true,
                        'tokenSeparators' => [',', ' '],
                        'maximumInputLength' => 20,
                    ],
                ])->hint('输入后请回车，会显示在商品列表的标题下面展示'); ?>
                <?= \common\widgets\linkage\Linkage::widget([
                    'form' => $form,
                    'model' => $model,
                    'template' => 'short',
                ]); ?>
                <div class="<?= in_array($model->type, ProductTypeEnum::entity()) ? '' : 'hide'; ?>">
                    <?= $form->field($model, 'delivery_type')->checkboxList(ShippingTypeEnum::getDeliveryType()); ?>
                </div>
                <!-- 实物商品支持显示-->
                <div class="<?= in_array($model->type, [ProductTypeEnum::ENTITY]) ? '' : 'hide'; ?>">
                    <?= $form->field($model, 'shipping_type')->radioList(ProductShippingTypeEnum::getMap())->hint('运费模板支持按地区设置运费，按购买件数计算运费，按重量计算运费等'); ?>
                    <div class="row shipping">
                        <div class="col-sm-6"><?= $form->field($model, 'shipping_fee_id')->dropDownList(ArrayHelper::merge(['0' => '请选择'], $company)) ?></div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'shipping_fee_type')->radioList([
                                '1' => '计件',
                                '2' => '体积',
                                '3' => '重量',
                            ]); ?>
                        </div>
                    </div>
                    <div class="shipping_fee">
                        <?= $form->field($model, 'shipping_fee')->textInput() ?>
                    </div>
                    <?= $form->field($model, 'unit')->textInput(); ?>
                    <div class="row">
                        <div class="col-sm-4"><?= $form->field($model, 'min_buy')->textInput()->hint('起售数量超出商品库存时，买家无法购买该商品。'); ?></div>
                        <div class="col-sm-4"><?= $form->field($model, 'max_buy')->textInput()->hint('<span class="orange">输入0表示不限购</span>'); ?></div>
                        <div class="col-sm-4"><?= $form->field($model, 'order_max_buy')->textInput()->hint('<span class="orange">输入0表示不限购</span>'); ?></div>
                    </div>
                </div>
                <?php if (Yii::$app->id == AppEnum::BACKEND) { ?>
                    <div class="row">
                        <div class="col-sm-4"><?= $form->field($model, 'sales')->textInput(); ?></div>
                        <div class="col-sm-4"><?= $form->field($model, 'view')->textInput(); ?></div>
                        <div class="col-sm-4"><?= $form->field($model, 'transmit_num')->textInput(); ?></div>
                    </div>
                <?php } ?>
                <div class="row <?= in_array($model->type, ProductTypeEnum::entity()) ? '' : 'hide'; ?>">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'production_date')->widget(kartik\date\DatePicker::class, [
                            'language' => 'zh-CN',
                            'layout' => '{picker}{input}',
                            'pluginOptions' => [
                                'format' => 'yyyy-mm-dd',
                                'todayHighlight' => true, // 今日高亮
                                'autoclose' => true, // 选择后自动关闭
                                'todayBtn' => true, // 今日按钮显示
                            ],
                            'options' => [
                                'class' => 'form-control no_bor',
                                'value' => empty($model->production_date) ? '' : date('Y-m-d', $model->production_date),
                            ],
                        ]); ?>
                    </div>
                    <div class="col-sm-6"><?= $form->field($model, 'shelf_life')->textInput()->hint('单位：天'); ?></div>
                </div>
                <?= $form->field($model, 'start_time')->widget(\kartik\datetime\DateTimePicker::class, [
                    'language' => 'zh-CN',
                    'options' => [
                        'value' => $model->isNewRecord ? date('Y-m-d H:i:s') : date('Y-m-d H:i:s',
                            $model->start_time),
                    ],
                    'pluginOptions' => [
                        'format' => 'yyyy-mm-dd hh:ii',
                        'todayHighlight' => true,//今日高亮
                        'autoclose' => true,//选择后自动关闭
                        'todayBtn' => true,//今日按钮显示
                    ],
                ])->hint('如果商品状态为已下架，则该设置无效'); ?>
                <?= $form->field($model, 'is_list_visible')->radioList(WhetherEnum::getMap())->hint('关闭后无法通过商品搜索或者商品列表查询到商品，可以通过分享链接/自定义装修选用进入详情'); ?>
                <?= $form->field($model, 'status')->radioList([
                    StatusEnum::ENABLED => '上架',
                    StatusEnum::DISABLED => '下架',
                ]); ?>
                <?= $form->field($model, 'is_hot')->checkbox(); ?>
                <?= $form->field($model, 'is_recommend')->checkbox(); ?>
                <?= $form->field($model, 'is_new')->checkbox(); ?>
            </div>
            <div class="tab-pane fade" id="custom-2">
                <?= $this->render('_specification', [
                    'model' => $model,
                    'form' => $form,
                    'specTemplate' => $specTemplate,
                    'spec' => $spec,
                    'pitchOn' => $pitchOn,
                    'sku' => $sku,
                ]) ?>
            </div>
            <div class="tab-pane fade" id="custom-3">
                <?= $form->field($model, 'covers')->widget(Files::class, [
                    'config' => [
                        // 可设置自己的上传地址, 不设置则默认地址
                        // 'server' => '',
                        'pick' => [
                            'multiple' => true,
                        ],
                    ],
                ])->hint('建议尺寸：800*800像素，第一张图片将作为商品主图,支持同时上传多张图片,多张图片之间可拖动调整位置'); ?>
                <?= $form->field($model, 'video_url')->widget(Files::class, [
                    'type' => 'videos',
                    'config' => [
                        // 可设置自己的上传地址, 不设置则默认地址
                        // 'server' => '',
                        'pick' => [
                            'multiple' => false,
                        ],
                        'accept' => [
                            'extensions' => ['rm', 'rmvb', 'wmv', 'avi', 'mpg', 'mpeg', 'mp4'],
                            'mimeTypes' => 'video/*',
                        ],
                    ],
                ])->hint('添加主图视频可提升商品的成交转化，有利于获取更多流量，建议时长 9-30 秒、视频宽高和商品图一致。'); ?>
                <?= $form->field($model, 'intro')->widget(UEditor::class) ?>
            </div>
            <div class="tab-pane fade" id="custom-4">
                <?= $this->render('_attribute', [
                    'model' => $model,
                    'form' => $form,
                    'attribute' => $attribute,
                    'attributeValue' => $attributeValue,
                ]) ?>
            </div>
            <div class="box-footer text-center">
                <?= $form->field($model, 'id')->hiddenInput()->label(false); ?>
                <div class="hide" id="specValue"></div>
                <button class="btn btn-primary" type="button" onclick="beforeSubmit()">保存</button>
                <span class="btn btn-white" onclick="rfTwiceAffirmBack(this, '确定返回吗？', '未保存的内容可能丢失');return false;">返回</span>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
    <!-- /.card -->
</div>

<script>
    $(document).ready(function () {
        shipping();
        pointExchangeType();
    });

    // 防止重复提交
    var submitStatus = true;

    // 验证并提交表单
    function beforeSubmit() {
        if (submitStatus === false) {
            // rfWarning('正在提交中...');
            // return;
        }

        // 序列化数据
        var data = $('#productForm').serializeArray();
        // 参数
        data.push({
            'name': 'ProductForm[attributeData]',
            'value': JSON.stringify(attribute.attributeList)
        })

        data.push({
            'name': 'ProductForm[skuData]',
            'value': spec.isSpecVal > 0 ? JSON.stringify(spec.skuList) : []
        })

        data.push({
            'name': 'ProductForm[specData]',
            'value': spec.isSpecVal > 0 ? JSON.stringify(spec.specList) : []
        })

        submitStatus = false;
        $.ajax({
            type: "post",
            url: "<?= Url::to(['edit', 'id' => $model->id]); ?>",
            dataType: "json",
            data: data,
            success: function (data) {
                submitStatus = true;
                if (parseInt(data.code) === 200) {
                    var editId = '<?= $model->id?>';
                    if (editId) {
                        swal("操作成功", "小手一抖就打开了一个框", "success").then((value) => {
                            window.location = "<?= $referrer; ?>";
                        });
                    } else {
                        swal('小手一抖打开一个窗', {
                            buttons: {
                                defeat: '继续创建商品',
                                catch: {
                                    text: "完成",
                                    value: "catch",
                                },
                            },
                            title: '操作成功',
                        }).then((value) => {
                            switch (value) {
                                case "defeat":
                                    location.reload();
                                    break;
                                case "catch":
                                    window.location = "<?= $referrer; ?>";
                                    break;
                                default:
                            }
                        });
                    }
                } else {
                    rfWarning(data.message);
                }
            }
        });
    }

    // 包邮
    $("input[name='ProductForm[shipping_type]']").click(function () {
        shipping();
    });

    // 积分
    $("input[name='ProductForm[point_exchange_type]']").click(function () {
        pointExchangeType();
    });

    function shipping() {
        var val = $("input[name='ProductForm[shipping_type]']:checked").val();

        $('.shipping').addClass('hide');
        $('.shipping_fee').addClass('hide');
        if (parseInt(val) === 2) {
            $('.shipping').removeClass('hide');
        }

        if (parseInt(val) === 3) {
            $('.shipping_fee').removeClass('hide');
        }
    }

    function pointExchangeType() {
        var val = $("input[name='ProductForm[point_exchange_type]']:checked").val();
        $('.shipping-point-for-now').addClass('hide');
        $('.shipping-point').addClass('hide');
        if (parseInt(val) === 1) {
            $('.shipping-point-for-now').removeClass('hide');
        } else {
            $('.shipping-point').removeClass('hide');
        }
    }
</script>
