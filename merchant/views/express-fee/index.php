<?php
use yii\widgets\LinkPager;
use common\helpers\Url;
use common\helpers\Html;

$this->title = '运费模板';
$this->params['breadcrumbs'][] = ['label' => '物流公司', 'url' => ['express-company/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= $this->title; ?></h3>
                <div class="box-tools">
                    <?= Html::a('<i class="fa fa-trash-o fa-lg"></i> 批量删除</a>', "javascript:void(0);", ['class' => 'btn btn-white btn-xs deleteAll']) ?>
                    <?= Html::create(['edit', 'company_id' => $company_id]); ?>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive">
                <?php foreach ($models as $model){ ?>
                    <table class="table">
                        <tbody>
                        <tr class="info">
                            <td colspan="6">
                                <?php if ($model['is_default'] != true) { ?>
                                    <?= Html::checkbox('fee_id', false, [
                                            'value' => $model['id']
                                    ])?>
                                <?php } ?>
                                <?= $model['title']; ?>
                                <div class="pull-right">
                                    <?= Html::a('编辑', ['edit', 'id' => $model['id'], 'company_id' => $company_id]); ?>
                                    <?php if ($model['is_default'] != true) { ?>
                                        <?= Html::a('删除', ['destroy', 'id' => $model['id'], 'company_id' => $company_id]); ?>
                                    <?php } ?>
                                </div>
                            </td>
                        </tr>
                        <tr class="address">
                            <td colspan="6">
                                <?php if ($model['is_default'] == true) { ?>
                                    <span>全国</span>
                                <?php } else { ?>
                                    <?php foreach ($model['region'] as $region) { ?>
                                        <span><?= $region['title']; ?></span>
                                    <?php } ?>
                                    <span class="btn btn-white btn-xs region-view" data-toggle="modal" data-target="#ajaxModalRegion" data-value=<?= json_encode($model['region']); ?>>查看详情</span>
                                <?php } ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="center">按件数</td>
                            <td>
                                首件(件)：
                                <span class="text-danger pull-right"><?= $model['bynum_snum']; ?></span>
                            </td>
                            <td>
                                首件运费(元)：
                                <span class="text-danger pull-right"><?= $model['bynum_sprice']; ?></span>
                            </td>
                            <td>
                                续件(件)：
                                <span class="text-danger pull-right"><?= $model['bynum_xnum']; ?></span>
                            </td>
                            <td>
                                续件运费(元)
                                <span class="text-danger pull-right"><?= $model['bynum_xprice']; ?></span>
                            </td>
                            <td align="center">
                                <?php if ($model['bynum_is_use'] == true) { ?>
                                    <span class="text-success">启用</span>
                                <?php } else { ?>
                                    <span class="text-danger">停用</span>
                                <?php } ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="center">按重量</td>
                            <td>
                                首重(kg)：
                                <span class="text-danger pull-right"><?= $model['weight_snum']; ?></span>
                            </td>
                            <td>
                                首重运费(元)：
                                <span class="text-danger pull-right"><?= $model['weight_sprice']; ?></span>
                            </td>
                            <td>
                                续重(kg)：
                                <span class="text-danger pull-right"><?= $model['weight_xnum']; ?></span>
                            </td>
                            <td>
                                续重运费(元)：
                                <span class="text-danger pull-right"><?= $model['weight_xprice']; ?></span>
                            </td>
                            <td align="center">
                                <?php if ($model['weight_is_use'] == true) { ?>
                                    <span class="text-success">启用</span>
                                <?php } else { ?>
                                    <span class="text-danger">停用</span>
                                <?php } ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="center">按体积</td>
                            <td>
                                首体积量(m³)：
                                <span class="text-danger pull-right"><?= $model['volume_snum']; ?></span>
                            </td>
                            <td>
                                首体积运费(元)：
                                <span class="text-danger pull-right"><?= $model['volume_sprice']; ?></span>
                            </td>
                            <td>
                                续体积量(m³)：
                                <span class="text-danger pull-right"><?= $model['volume_xnum']; ?></span>
                            </td>
                            <td>
                                续体积运费(元)：
                                <span class="text-danger pull-right"><?= $model['volume_xprice']; ?></span>
                            </td>
                            <td align="center">
                                <?php if ($model['volume_is_use'] == true) { ?>
                                    <span class="text-success">启用</span>
                                <?php } else { ?>
                                    <span class="text-danger">停用</span>
                                <?php } ?>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                <?php } ?>
                <div class="row">
                    <div class="col-sm-12">
                        <?= LinkPager::widget([
                            'pagination' => $pages,
                        ]);?>
                    </div>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
    </div>
</div>

<div class="modal fade" id="ajaxModalRegion" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">关闭</span></button>
                <h4 class="modal-title">地区详情</h4>
            </div>
            <div class="modal-body mask-address-info">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>

<!-- 模板 -->
<script id="region" type="text/html">
    {{each list as value i}}
    <div>
        <h3>{{value.title}}</h3>
        <ul>
            {{each value.child as item i}}
            <li>{{item.title}}</li>
            {{/each}}
        </ul>
    </div>
    {{/each}}
</script>

<script>
    // 视图详情
    $('.region-view').click(function () {
        var newData = [];
        newData['list'] = $(this).data('value');

        var html = template('region', newData);
        $('.modal-body').html(html);
    });

    // 批量删除
    $('.deleteAll').click(function () {
        var feeIds = [];
        $("tbody input[type='checkbox']:checked").each(function() {
            if (!isNaN($(this).val())) {
                feeIds.push($(this).val());
            }
        });

        if (feeIds.length == 0) {
            rfWarning('请选择需要操作的记录');
        } else {
            $.ajax({
                type : "post",
                url : "<?= Url::to(['destroy-all'])?>",
                dataType : "json",
                data : {ids: feeIds},
                success: function(data){
                    if (data.code == 200) {
                        swal({
                            title: data.message,
                            text  : '小手一抖就打开了一个框',
                            type  : "success"
                        }, function (){
                            location.reload();
                        });
                    } else {
                        rfWarning(data.message);
                    }
                }
            });
        }
    })
</script>