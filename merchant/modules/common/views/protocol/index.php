<?php

use common\helpers\Html;

$this->title = '协议管理';

?>

<div class="row">
    <div class="col-12 col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= $this->title; ?></h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>协议名称</th>
                        <th>标识</th>
                        <th class="action-column">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($protocolNameMap as $key => $item) {?>
                        <tr>
                            <td>《<?= $item; ?>》</td>
                            <td><?= $key; ?></td>
                            <td><?= Html::linkButton(['edit', 'name' => $key], '编辑')?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
    </div>
</div>
