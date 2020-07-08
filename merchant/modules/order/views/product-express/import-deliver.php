<?php
use yii\widgets\ActiveForm;
use common\helpers\Html;
use common\enums\StatusEnum;
?>

<?php $form = ActiveForm::begin([
    'options' => [
        'enctype' => 'multipart/form-data'
    ]
]); ?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">关闭</span></button>
    <h4 class="modal-title">一键发货</h4>
</div>
<div class="modal-body">
    <div class="form-group">
        <div class="input-group m-b">
            <span class="input-group-btn">
                <?= Html::linkButton(['deliver-template-download'], '下载 Excel 模板', [
                    'class' => "btn btn-white"
                ]); ?>
            </span>
            <input id="excel-file" type="file" name="excelFile" style="display:none">
            <input type="text" class="form-control" id="fileName" name="fileName" readonly>
            <span class="input-group-btn">
                <a class="btn btn-white" onclick="$('#excel-file').click();">选择文件</a>
            </span>
        </div>
        <div class="help-block">提醒：每次请不要上传太多的订单号进行发货。</div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
    <button class="btn btn-primary" type="submit">发货</button>
</div>
<?php ActiveForm::end(); ?>

<script type="text/javascript">
    $('input[id=excel-file]').change(function() {
        $('#fileName').val($(this).val());
    });
</script>