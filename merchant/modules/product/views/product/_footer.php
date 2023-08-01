<?php

use common\helpers\Html;
use common\helpers\Url;

?>

<div class="modal fade" id="editTitle" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">商品名称</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
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

<script>
    var h5Url = "<?= $h5Url; ?>";
    var merchant_id;
    var product_id;

    $(document).on("click",".view",function(){
        var product_id = $(this).data('id');
        var merchant_id = $(this).data('merchant_id');
        if (h5Url.length > 0){
            var url = h5Url + '/pages/product/product?id=' + product_id;

            layer.open({
                type: 2,
                title: '页面预览',
                area: ['375px', '90%'],
                content: url
            });
        } else {
            rfMsg('请先去 基础设置->应用配置 配置预览地址')
        }
    })

    $(document).on("click",".ion-compose",function(){
        product_id = $(this).prev().data('id');
        merchant_id = $(this).prev().data('merchant_id');
        product_name = $(this).prev().data('name');
        $('#productName').val(product_name);
    });

    $(document).on("click",".miniProgramAddress",function(){
        var product_id = $(this).parent().data('id');
        swal('/pages/product/product?id=' + product_id, {
            buttons: {
                defeat: '确定',
            },
            title: '小程序地址',
        });
    });

    // 标题编辑
    $(document).on("click",".submit-name",function(){
        var name = $('#productName').val();
        if (name.length === 0) {
            rfMsg('请填写标题');
            return;
        }

        url = "<?= Url::to(['update-name'])?>" + '?id=' + product_id + '&name=' + name;
        sendData(url);
    });

    let url = '';
    // 删除全部
    $(".destroy-all").on("click", function () {
        url = "<?= Url::to(['destroy-all'])?>";
        sendData(url);
    });

    // 上架
    $(document).on("click",".putaway-all",function(){
        url = "<?= Url::to(['state-all', 'status' => true])?>";
        sendData(url);
    });

    // 下架
    $(document).on("click",".sold-out-all",function(){
        url = "<?= Url::to(['state-all', 'status' => false])?>";
        sendData(url);
    });

    // 上架
    $(document).on("click",".putaway",function(){
        url = "<?= Url::to(['state-all', 'status' => true])?>";
        var id = $(this).data('id');
        sendData(url, [id]);
    });

    // 下架
    $(document).on("click",".sold-out",function(){
        url = "<?= Url::to(['state-all', 'status' => false])?>";
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
