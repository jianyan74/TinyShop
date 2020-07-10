<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">关闭</span></button>
    <h4 class="modal-title">物流状态</h4>
</div>
<div class="modal-body">
    <div class="col-md-12 changelog-info">
        <ul class="time-line">
            <?php foreach ($trace as $item) { ?>
                <li>
                    <time><?= \common\helpers\Html::encode($item['datetime']) ?></time>
                    <h5><?= \common\helpers\Html::encode($item['remark']) ?></h5>
                </li>
            <?php } ?>
        </ul>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
</div>