<?php

use common\helpers\Html;

?>

<div class="modal-header">
    <h4 class="modal-title">物流状态</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
</div>
<div class="modal-body">
    <div class="col-md-12 changelog-info">
        <ul class="time-line">
            <?php foreach ($trace as $item) { ?>
                <li>
                    <time><?= Html::encode($item['datetime']) ?></time>
                    <h5><?= Html::encode($item['remark']) ?></h5>
                </li>
            <?php } ?>
            <?= empty($trace) ? '查不到物流信息' : ''; ?>
        </ul>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
</div>
