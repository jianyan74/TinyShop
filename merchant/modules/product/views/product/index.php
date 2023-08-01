<?php

use yii\grid\GridView;
use common\helpers\Html;
use yii\helpers\BaseHtml;
use common\helpers\ImageHelper;
use common\helpers\Url;
use common\enums\AppEnum;
use common\enums\StatusEnum;
use addons\TinyShop\common\enums\ProductTypeEnum;
use addons\TinyShop\common\enums\ProductShippingTypeEnum;
use addons\TinyShop\common\enums\product\AuditStatusEnum;

$merchant_id = Yii::$app->services->merchant->getNotNullId();
$isHide = Yii::$app->services->devPattern->isB2B2C() ? '' : 'hide';

$this->title = '商品管理';
$this->params['breadcrumbs'][] = ['label' => $this->title];

?>

<?= $this->render('_search', [
    'search' => $search,
    'cates' => $cates,
    'merchantCates' => $merchantCates,
    'brands' => $brands,
]) ?>

<div class="row">
    <div class="col-sm-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="<?php if ($status == 1 && $stockWarning != 1 && $sellOut != 1 && !in_array($auditStatus, [0, -1, -10])) { ?>active<?php } ?>"><a href="<?= Url::to(['index', 'status' => 1]) ?>">出售中</a></li>
                <li class="<?php if ($status == 0) { ?>active<?php } ?>"><a href="<?= Url::to(['index', 'status' => 0]) ?>">已下架</a></li>
                <li class="<?php if ($stockWarning == 1) { ?>active<?php } ?>"><a href="<?= Url::to(['index', 'stock_warning' => 1]) ?>">库存报警(<?= $warningStockCount; ?>)</a></li>
                <li class="<?php if ($sellOut == 1) { ?>active<?php } ?>"><a href="<?= Url::to(['index', 'sell_out' => 1]) ?>">已售罄</a></li>
                <li><a href="<?= Url::to(['recycle']) ?>">回收站</a></li>
                <li class="pull-right">
                    <?php if (!Yii::$app->services->devPattern->isPlatformLocation()) { ?>
                        <?= Html::create(['edit'], '创建', ['class' => 'btn btn-primary btn-sm m-r-n']); ?>
                    <?php } ?>
                </li>
            </ul>
            <div class="tab-content">
                <div class="col-sm-12 m-b-sm m-l">
                    <?= Html::a('批量删除', "javascript:void(0);", ['class' => 'btn btn-white btn-sm m-l-n-md destroy-all']); ?>
                    <?= Html::a('批量上架', "javascript:void(0);", ['class' => 'btn btn-white btn-sm putaway-all']); ?>
                    <?= Html::a('批量下架', "javascript:void(0);", ['class' => 'btn btn-white btn-sm sold-out-all']); ?>
                    <div class="btn-group">
                        <button type="button" class="btn btn-white btn-sm" data-toggle="dropdown">推荐</button>
                        <button type="button" class="btn btn-white btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                            <span class="caret"></span>
                            <span class="sr-only">切换下拉</span>
                        </button>
                        <ul class="dropdown-menu text-center" role="menu" style="">
                            <li class="text-center">
                                <label>
                                    <input type="checkbox" id='is_hot' name="is_hot" value="1"> 热门
                                </label>
                            </li>
                            <li class="text-center">
                                <label>
                                    <input type="checkbox" id='is_recommend' name="is_recommend" value="1"> 推荐
                                </label>
                            </li>
                            <li class="text-center">
                                <label>
                                    <input type="checkbox" id='is_new' name="is_new" value="1" > 新品
                                </label>
                            </li>
                            <li class="divider"></li>
                            <li class="text-center"><a href="#" class="recommend">确定</a></li>
                        </ul>
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
                                'value' => function ($model) use ($marketing) {
                                    $html = '<a href="javascript:void(0)" class="view" data-id="' . $model->id . '" data-merchant_id="' . $model->merchant_id . '" data-name="' . $model->name . '">' . Html::textNewLine($model->name, 30) . '</a>' . ' <i class="icon ion-compose" data-toggle="modal" data-target="#editTitle"></i><br>';
                                    if (isset($model['merchant']['title']) && Yii::$app->services->devPattern->isPlatformLocation()) {
                                        $html .=  Html::tag('span', '「' . $model['merchant']['title'] . '」', ['class' => 'label label-default m-r-xs']);
                                    }

                                    $html .= !empty($model['is_hot']) ? Html::tag('span', '热门', ['class' => 'label label-default m-r-xs']) : '';
                                    $html .= !empty($model['is_recommend']) ? Html::tag('span', '推荐', ['class' => 'label label-default m-r-xs']) : '';
                                    $html .= !empty($model['is_new']) ? Html::tag('span', '新品', ['class' => 'label label-default m-r-xs']) : '';
                                    $html .= $model['shipping_type'] == ProductShippingTypeEnum::FULL_MAIL ? Html::tag('span', '包邮', ['class' => 'label label-default m-r-xs']) : '';
                                    if (isset($marketing[$model['id']])) {
                                        foreach ($marketing[$model['id']] as $value) {
                                            $html .= Html::tag('span', $value, ['class' => 'label label-default m-r-xs']);
                                        }
                                    }

                                    $html .= !empty($model['is_spec']) ? Html::tag('span', '多规格', ['class' => 'label label-default m-r-xs']) : '';

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
                                'label' => Yii::$app->services->devPattern->isB2B2C() ? '平台分类' : '商品分类',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'label' => '商品类型',
                                'attribute' => 'type',
                                'value' => function ($model) {
                                    return ProductTypeEnum::getValue($model->type);
                                },
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
                                'template' => '{edit} {more}',
                                'buttons' => [
                                    'edit' => function ($url, $model, $key) {
                                        if (Yii::$app->services->devPattern->isB2B2C() && Yii::$app->id == AppEnum::BACKEND) {
                                            return false;
                                        }

                                        return Html::a('编辑', ['edit', 'id' => $model['id']], [
                                            'class' => 'blue',
                                        ]);
                                    },
                                    'more' => function ($url, $model, $key) {
                                        $updateHtml = '';
                                        if ($model->status == StatusEnum::ENABLED) {
                                            $updateHtml = BaseHtml::a('下架', '#', [
                                                'class' => 'dropdown-item sold-out',
                                                'data-id' => $model['id'],
                                            ]);
                                        } else {
                                            $updateHtml = BaseHtml::a('上架', '#', [
                                                'class' => 'dropdown-item putaway',
                                                'data-id' => $model['id'],
                                            ]);
                                        }

                                        $copyHtml = Html::a('复制', ['copy', 'id' => $model['id']], [
                                            'class' => 'dropdown-item',
                                            'onclick' => "rfTwiceAffirm(this, '确认复制商品吗？', '复制成功后请到已下架列表查看');return false;"
                                        ]);

                                        $deleteHtml = Html::a('删除', ['destroy', 'id' => $model->id], [
                                            'class' => 'dropdown-item',
                                            'onclick' => "rfDelete(this);return false;"
                                        ]);

                                        if (Yii::$app->services->devPattern->isB2B2C() && Yii::$app->id == AppEnum::BACKEND) {
                                            $deleteHtml = '';
                                            $copyHtml = '';
                                            $updateHtml = '';
                                        }

                                        if ($model->audit_status == AuditStatusEnum::GET_OUT_OF_LINE) {
                                            $copyHtml = '';
                                        }

                                        return ' <div class="btn-group">
                                                <a href="#" class="blue" data-toggle="dropdown" aria-expanded="false">更多</a>
                                                <ul class="dropdown-menu dropdown-menu-left" role="menu" data-id="' . $model['id'] .'" style="left: -70px;text-align: center">
                                                    <a href="#" class="dropdown-item miniProgramAddress">推广</a>
                                                    ' . $updateHtml .'
                                                    ' . $copyHtml .'
                                                    ' . $deleteHtml .'
                                                </ul>
                                            </div>';
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

<?= $this->render('_footer', [
    'cates' => $cates,
    'merchantCates' => $merchantCates,
    'brands' => $brands,
    'h5Url' => $h5Url,
]) ?>

