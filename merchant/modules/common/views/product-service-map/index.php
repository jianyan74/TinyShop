<?php

use common\helpers\Url;
use common\helpers\Html;
use common\enums\AuditStatusEnum;

$this->title = '商品服务';
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <?php foreach ($models as $model) { ?>
        <div class="col-3">
            <div class="box box-solid">
                <div class="box-header">
                    <i class="fa fa-circle rf-circle" style="font-size: 8px"></i>
                    <h3 class="box-title"><?= Html::encode($model['name']) ?></h3>
                    <div class="box-tools" style="font-size: 14px">
                        <?php if(empty($model['map'])) { ?>
                            <a href="<?= Url::to(['apply', 'service_id' => $model['id']])?>" class="blue pull-right">立即申请</a>
                        <?php } else { ?>
                            <?php if($model['map']['audit_status'] == AuditStatusEnum::DELETE) { ?>
                                <span class="orange pull-right" style="padding-left: 10px;padding-right: 10px">被拒绝</span>
                                <a href="<?= Url::to(['apply', 'service_id' => $model['id']])?>" class="blue pull-right">重新申请</a>
                            <?php } ?>
                            <?php if($model['map']['audit_status'] == AuditStatusEnum::DISABLED) { ?>
                                <span class="orange pull-right">审核中</span>
                            <?php } ?>
                            <?php if($model['map']['audit_status'] == AuditStatusEnum::ENABLED) { ?>
                                <span class="green pull-right">已通过</span>
                                <a href="<?= Url::to(['delete', 'id' => $model['map']['id']])?>" class="red pull-right" style="padding-left: 10px;padding-right: 10px">退出</a>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="row">
                   <span href="javascript:void(0)" class="product-title">
                        </span>
                        <span class="product-description">
                            <?= Html::encode($model['explain']) ?>
                        </span>
                        <!-- ./col -->
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.box-body -->
            </div>
        </div>
    <?php } ?>
</div>
