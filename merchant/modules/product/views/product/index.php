<?php

use yii\grid\GridView;
use common\helpers\Html;
use yii\helpers\BaseHtml;
use common\helpers\ImageHelper;
use common\helpers\Url;
use common\helpers\ArrayHelper;
use addons\TinyShop\common\enums\ProductShippingTypeEnum;
use addons\TinyShop\common\enums\PointExchangeTypeEnum;

$this->title = '商品管理';
$this->params['breadcrumbs'][] = ['label' => $this->title];
?>

<div class="row">
    <div class="col-sm-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="<?php if ($product_status == 1 && $stock_warning != 1) { ?>active<?php } ?>"><a
                            href="<?= Url::to(['index', 'product_status' => 1]) ?>">出售中</a></li>
                <li class="<?php if ($product_status == 0) { ?>active<?php } ?>"><a
                            href="<?= Url::to(['index', 'product_status' => 0]) ?>">已下架</a></li>
                <li class="<?php if ($stock_warning == 1) { ?>active<?php } ?>"><a
                            href="<?= Url::to(['index', 'stock_warning' => 1]) ?>">库存报警</a></li>
                <li><a href="<?= Url::to(['recycle']) ?>">回收站</a></li>
                <li class="pull-right">
                    <?= Html::create(['edit']); ?>
                </li>
            </ul>
            <div class="tab-content">
                <div class="col-sm-12 m-b-sm">
                    <?= Html::a('批量删除', "javascript:void(0);",
                        ['class' => 'btn btn-white btn-sm m-l-n-md delete-all']); ?>
                    <?= Html::a('上架', "javascript:void(0);", ['class' => 'btn btn-white btn-sm putaway-all']); ?>
                    <?= Html::a('下架', "javascript:void(0);", ['class' => 'btn btn-white btn-sm sold-out-all']); ?>
                    <div class="btn-group">
                        <button type="button" class="btn btn-white btn-sm">推荐</button>
                        <button type="button" class="btn btn-white btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                            <span class="caret"></span>
                            <span class="sr-only">切换下拉</span>
                        </button>
                        <ul class="dropdown-menu text-center" role="menu">
                            <li class="text-center">
                                <label>
                                    <input type="checkbox" id='is_hot' name="is_hot value="1"> 热门
                                </label>
                            </li>
                            <li class="text-center">
                                <label>
                                    <input type="checkbox" id='is_recommend' name="is_recommend value="1"> 推荐
                                </label>
                            </li>
                            <li class="text-center">
                                <label>
                                    <input type="checkbox" id='is_new' name="is_new value="1" > 新品
                                </label>
                            </li>
                            <li class="divider"></li>
                            <li class="text-center"><a href="#" class="recommend">确定</a></li>
                        </ul>
                    </div>
                    <div class="pull-right">
                         <span>
                             <a href="#" data-toggle='modal' data-target="#query" class="btn btn-white btn-sm"><i class="fa fa-search"></i> 筛选查询</a>
                          </span>
                    </div>
                </div>
                <div class="active tab-pane">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        //重新定义分页样式
                        'tableOptions' => [
                            'class' => 'table table-hover rf-table',
                            'fixedNumber' => 3,
                            'fixedRightNumber' => 1,
                        ],
                        'options' => [
                            'id' => 'grid',
                        ],
                        'columns' => [
                            [
                                'class' => 'yii\grid\CheckboxColumn',
                                'checkboxOptions' => function ($model, $key, $index, $column) {
                                    return ['value' => $model->id];
                                },
                            ],
                            [
                                'class' => 'yii\grid\SerialColumn',
                                'visible' => false, // 不显示#
                            ],
                            'id',
                            [
                                'label' => '主图',
                                'filter' => false, //不显示搜索框
                                'value' => function ($model) {
                                    if (!empty($model->picture)) {
                                        return ImageHelper::fancyBox($model->picture);
                                    }
                                },
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'name',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    $html = '<a href="javascript:void(0)" class="view" data-id="' . $model->id . '" data-name="' . $model->name . '">' . $model->name . '</a>' . ' <i class="icon ion-compose" data-toggle="modal" data-target="#editTitle"></i><br>';
                                    $html .= !empty($model['is_hot']) ? Html::tag('span', '热门', ['class' => 'label label-default m-r-xs']) : '';
                                    $html .= !empty($model['is_recommend']) ? Html::tag('span', '推荐', ['class' => 'label label-default m-r-xs']) : '';
                                    $html .= !empty($model['is_new']) ? Html::tag('span', '新品', ['class' => 'label label-default m-r-xs']) : '';
                                    $html .= $model['shipping_type'] == ProductShippingTypeEnum::FULL_MAIL ? Html::tag('span', '包邮', ['class' => 'label label-default m-r-xs']) : '';

                                    if ($model->point_exchange_type > PointExchangeTypeEnum::NOT_EXCHANGE) {
                                        $html .= Html::tag('span', PointExchangeTypeEnum::getValue($model->point_exchange_type), ['class' => 'label label-default m-r-xs']);
                                    }


                                    return $html;
                                },
                            ],
                            [
                                'label' => '销售价',
                                'attribute' => 'price',
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'real_sales',
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'stock',
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'cate.title',
                                'label' => '产品分类',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'sort',
                                'filter' => false, //不显示搜索框
                                'value' => function ($model) {
                                    return Html::sort($model->sort);
                                },
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'header' => "操作",
                                'class' => 'yii\grid\ActionColumn',
                                'template' => '{update} {edit} {delete}',
                                'buttons' => [
                                    'update' => function ($url, $model, $key) {
                                        if ($model->product_status == \common\enums\StatusEnum::ENABLED) {
                                            return BaseHtml::a('下架', '#', [
                                                'class' => 'btn btn-white btn-sm sold-out',
                                                'data-id' => $model['id'],
                                            ]);
                                        }
                                        return BaseHtml::a('上架', '#', [
                                            'class' => 'btn btn-white btn-sm putaway',
                                            'data-id' => $model['id'],
                                        ]);
                                    },
                                    'edit' => function ($url, $model, $key) {
                                        return Html::edit(['edit', 'id' => $model['id']]);
                                    },
                                    'delete' => function ($url, $model, $key) {
                                        return Html::delete(['delete', 'id' => $model->id]);
                                    },
                                ],
                            ],
                        ],
                    ]); ?>
                </div>
            </div>
            <!-- /.tab-content -->
        </div>
        <!-- /.nav-tabs-custom -->
    </div>
</div>

<div class="modal fade" id="editTitle" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close"><span aria-hidden="true">×</span><span class="sr-only">关闭</span>
                </button>
                <h4 class="modal-title">商品名称</h4>
            </div>
            <div class="modal-body">
                <textarea type="text" class="form-control" id="productName"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
                <button class="btn btn-primary submit-name" data-dismiss="modal">确定</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="query" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <?= Html::beginForm(Url::to(ArrayHelper::merge([0 => 'index'], Yii::$app->request->get())), 'get') ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close"><span aria-hidden="true">×</span><span class="sr-only">关闭</span>
                </button>
                <h4 class="modal-title">筛选查询</h4>
            </div>
            <div class="modal-body">
                <div class="col-lg-6">
                    <div class="form-group field-cate-sort">
                        <div class="col-sm-4 text-right">
                            <label class="control-label" for="cate-sort">商品名称</label>
                        </div>
                        <div class="col-sm-8">
                            <?= Html::textInput('name', $search->name, ['class' => 'form-control']) ?>
                            <div class="help-block"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group field-cate-sort">
                        <div class="col-sm-4 text-right">
                            <label class="control-label" for="cate-sort">商品分类</label>
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
                <div class="col-lg-6">
                    <div class="form-group field-cate-sort">
                        <div class="col-sm-4 text-right">
                            <label class="control-label" for="cate-sort">销量</label>
                        </div>
                        <div class="col-sm-8 ">
                            <div class="col-lg-12 input-group">
                                <div class="input-group">
                                    <?= Html::textInput('min_sales', $search->min_sales, ['class' => 'form-control', 'placeholder' => '最低销量']) ?>
                                    <span class="input-group-addon" style="border-color: #fff">-</span>
                                    <?= Html::textInput('max_sales', $search->max_sales, ['class' => 'form-control', 'placeholder' => '最高销量']) ?>
                                </div>
                                <div class="help-block"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group field-cate-sort">
                        <div class="col-sm-4 text-right">
                            <label class="control-label" for="cate-sort">供应商</label>
                        </div>
                        <div class="col-sm-8">
                            <?= Html::dropDownList('supplier_id', $search->supplier_id, Yii::$app->tinyShopService->baseSupplier->getMapList(), [
                                'class' => 'form-control',
                                'prompt' => '全部',
                            ]) ?>
                            <div class="help-block"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group field-cate-sort">
                        <div class="col-sm-4 text-right">
                            <label class="control-label" for="cate-sort"></label>
                        </div>
                        <div class="col-sm-8">
                            <input type="hidden" name="recommend">
                            <?= Html::checkboxList('recommend', $search->recommend, [
                                '1' => '热门',
                                '2' => '推荐',
                                '3' => '新品',
                                '4' => '包邮',
                                '7' => '积分兑换',
                            ]) ?>
                            <div class="help-block"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
                <button type="reset" class="btn btn-white">重置</button>
                <button class="btn btn-primary">确定</button>
            </div>
        </div>
        <?= Html::endForm() ?>
    </div>
</div>

<script>
    var h5Url = "<?= $h5Url ?>";
    var product_id;
    $(document).on("click",".view",function(){
        var product_id = $(this).data('id');
        if (h5Url.length > 0){
            layer.open({
                type: 2,
                title: '页面预览',
                area: ['375px', '90%'],
                content: h5Url + '/pages/product/product?id=' + product_id
            });
        } else {
            rfMsg('请先去 基础配置->页面设置 配置预览地址')
        }
    })

    $(document).on("click",".ion-compose",function(){
        product_id = $(this).prev().data('id');
        product_name = $(this).prev().data('name');
        $('#productName').val(product_name);
    });

    // 标题编辑
    $(document).on("click",".submit-name",function(){
        var name = $('#productName').val();
        if (name.length === 0) {
            rfMsg('请填写标题')
        }

        url = "<?= Url::to(['update-name'])?>" + '?id=' + product_id + '&name=' + name;
        sendData(url);
    });

    // 标题编辑
    $(document).on("click",".submit-info",function(){
        var data = $('#infoForm').serializeArray();
        data.push({
            'name' : 'ids',
            'value' : $("#grid").yiiGridView("getSelectedRows")
        })

        $.ajax({
            type: "post",
            url: '<?= Url::to(['update-info'])?>',
            dataType: "json",
            data: data,
            success: function (data) {
                if (parseInt(data.code) === 200) {
                    swal('小手一抖打开一个窗', {
                        buttons: {
                            defeat: '确定',
                        },
                        title: '操作成功',
                    }).then(function (value) {
                        switch (value) {
                            case "defeat":
                                location.reload();
                                break;
                            default:
                        }
                    });
                } else {
                    rfWarning(data.message);
                }
            }
        });
    });

    let url = '';
    // 删除全部
    $(".delete-all").on("click", function () {
        url = "<?= Url::to(['delete-all'])?>";
        sendData(url);
    });

    // 上架
    $(document).on("click",".putaway-all",function(){
        url = "<?= Url::to(['state-all', 'state' => true])?>";
        sendData(url);
    });

    // 下架
    $(document).on("click",".sold-out-all",function(){
        url = "<?= Url::to(['state-all', 'state' => false])?>";
        sendData(url);
    });

    // 上架
    $(document).on("click",".putaway",function(){
        url = "<?= Url::to(['state-all', 'state' => true])?>";
        var id = $(this).data('id');
        sendData(url, [id]);
    });

    // 下架
    $(document).on("click",".sold-out",function(){
        url = "<?= Url::to(['state-all', 'state' => false])?>";
        var id = $(this).data('id');
        sendData(url, [id]);
    });

    // 推荐
    $(".recommend").on("click", function () {
        var is_hot = $('#is_hot').is(':checked') ? 1 : 0;
        var is_recommend = $('#is_recommend').is(':checked') ? 1 : 0;
        var is_new = $('#is_new').is(':checked') ? 1 : 0;
        url = "<?= Url::to(['recommend'])?>" + '?is_hot=' + is_hot + '&is_recommend='+ is_recommend + '&is_new=' + is_new;

        sendData(url);
    });

    function sendData(url, ids = []) {
        if (ids.length === 0) {
            ids = $("#grid").yiiGridView("getSelectedRows");
        }

        $.ajax({
            type: "post",
            url: url,
            dataType: "json",
            data: {ids: ids},
            success: function (data) {
                if (parseInt(data.code) === 200) {
                    swal('小手一抖打开一个窗', {
                        buttons: {
                            defeat: '确定',
                        },
                        title: '操作成功',
                    }).then(function (value) {
                        switch (value) {
                            case "defeat":
                                location.reload();
                                break;
                            default:
                        }
                    });
                } else {
                    rfWarning(data.message);
                }
            }
        });
    }
</script>