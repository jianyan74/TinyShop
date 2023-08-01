<?php

use common\helpers\Html;
use yii\web\JsExpression;
use kartik\select2\Select2;
use common\helpers\Url;
use common\helpers\ArrayHelper;
use addons\TinyShop\common\enums\MarketingEnum;
use addons\TinyShop\common\enums\ProductTypeEnum;
use addons\TinyShop\common\enums\ProductStockDeductionTypeEnum;

$this->title = '商品管理';
$this->params['breadcrumbs'][] = ['label' => $this->title];

?>

<div class="row">
    <div class="col-12 col-xs-12">
        <?= Html::beginForm(Url::to(ArrayHelper::merge([0 => 'index'], Yii::$app->request->get())), 'get') ?>
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">筛选查询</h3>
                <div class="box-tools">
                    <?= Html::a('重置', ['index'], [
                        'class' => "btn btn-sm btn-white"
                    ])?>
                    <button class="btn btn-sm btn-primary">筛选</button>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive">
                <div class="row">
                    <div class="col-lg-3">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-4 text-right">
                                    <label class="control-label" for="cate-sort">商品名称</label>
                                </div>
                                <div class="col-sm-8">
                                    <?= Html::textInput('name', $search->name, ['class' => 'form-control']) ?>
                                    <div class="help-block"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-4 text-right">
                                    <label class="control-label" for="cate-sort"><?= Yii::$app->services->devPattern->isB2B2C() ? '平台分类' : '商品分类' ?></label>
                                </div>
                                <div class="col-sm-8">
                                    <?= Html::dropDownList('cate_id', $search->cate_id, $cates, [
                                        'class' => 'form-control',
                                        'prompt' => '全部',
                                    ]) ?>
                                    <div class="help-block"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-4 text-right">
                                    <label class="control-label" for="cate-sort">销量</label>
                                </div>
                                <div class="col-sm-8">
                                    <div class="col-lg-12 input-group">
                                        <div class="input-group">
                                            <?= Html::textInput('min_sales', $search->min_sales, ['class' => 'form-control', 'placeholder' => '最低销量']) ?>
                                            <span class="input-group-addon" style="border-color: #fff;padding-left: 5px;padding-right: 5px;"> - </span>
                                            <?= Html::textInput('max_sales', $search->max_sales, ['class' => 'form-control', 'placeholder' => '最高销量']) ?>
                                        </div>
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-4 text-right">
                                    <label class="control-label" for="cate-sort">商品类型</label>
                                </div>
                                <div class="col-sm-8">
                                    <?= Html::dropDownList('type', $search->type, ProductTypeEnum::getMap(), [
                                        'class' => 'form-control',
                                        'prompt' => '全部',
                                    ]) ?>
                                    <div class="help-block"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-4 text-right">
                                    <label class="control-label" for="cate-sort">商品品牌</label>
                                </div>
                                <div class="col-sm-8">
                                    <?= Html::dropDownList('brand_id', $search->brand_id, $brands, [
                                        'class' => 'form-control',
                                        'prompt' => '全部',
                                    ]) ?>
                                    <div class="help-block"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <div class="row">
                                <?php if (!Yii::$app->services->devPattern->isPlatformLocation()) { ?>
                                    <div class="col-sm-4 text-right">
                                        <label class="control-label" for="cate-sort">供应商</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <?= Html::dropDownList('supplier_id', $search->supplier_id, Yii::$app->tinyShopService->supplier->getMap(), [
                                            'class' => 'form-control',
                                            'prompt' => '全部',
                                        ]) ?>
                                        <div class="help-block"></div>
                                    </div>
                                <?php } else { ?>
                                    <div class="col-sm-4 text-right">
                                        <label class="control-label" for="cate-sort">商家名称</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <?= Select2::widget([
                                            'name' => 'merchant_id',
                                            'value' => $search->merchant_id,
                                            'initValueText' => $search->getMerchant()->title ?? '',
                                            'options' => ['placeholder' => '请输入'],
                                            'pluginOptions' => [
                                                'allowClear' => true,
                                                'minimumInputLength' => 1,
                                                'language' => [
                                                    'errorLoading' => new JsExpression("function () { return '等待中...'; }"),
                                                ],
                                                'ajax' => [
                                                    'url' => Url::to(['/merchant-info/select2']),
                                                    'dataType' => 'json',
                                                    'data' => new JsExpression('function(params) { 
                                        return {q:params.term}; 
                                }')
                                                ],
                                                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                                'templateResult' => new JsExpression('function(city) { return city.text; }'),
                                                'templateSelection' => new JsExpression('function (city) { return city.text; }'),
                                            ],
                                        ]); ?>
                                        <div class="help-block"></div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-4 text-right">
                                    <label class="control-label" for="cate-sort">库存扣减方式</label>
                                </div>
                                <div class="col-sm-8">
                                    <?= Html::dropDownList('stock_deduction_type', $search->stock_deduction_type, ProductStockDeductionTypeEnum::getMap(), [
                                        'class' => 'form-control',
                                        'prompt' => '全部',
                                    ]) ?>
                                    <div class="help-block"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-4"></div>
                                <div class="col-sm-8">
                                    <input type="hidden" name="recommend">
                                    <?= Html::checkboxList('recommend', $search->recommend, [
                                        '1' => '热门',
                                        '2' => '推荐',
                                        '3' => '新品',
                                        '4' => '包邮',
                                        '5' => '分销',
                                    ]) ?>
                                    <div class="help-block"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        <?= Html::endForm() ?>
    </div>
</div>
