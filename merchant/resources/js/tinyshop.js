
$('.orderProductAgree').click(function () {
    var id = $(this).parent().attr('id');

    swal("确定同意退款吗？", {
        buttons: {
            cancel: "取消",
            defeat: '确定',
        },
    }).then((value) => {
        switch (value) {
            case "defeat":
                updateProductRefundStatus(id, orderProductAgreeUrl);
                break;
            default:
        }
    });
});

$('.orderProductRefuse').click(function () {
    var id = $(this).parent().attr('id');

    swal("您可以拒绝本次退款或者永久拒绝！", {
        buttons: {
            cancel: "取消",
            defeat: '永久拒绝',
            catch: {
                text: "拒绝本次",
                value: "catch",
            },
        },
    }).then((value) => {
        switch (value) {
            case "defeat":
                updateProductRefundStatus(id, orderProductRefuseUrl, true);
                break;

            case "catch":
                updateProductRefundStatus(id, orderProductRefuseUrl);
                break;
            default:
        }
    });
});

$('.orderProductDelivery').click(function () {
    var id = $(this).parent().attr('id');
    swal("确定收货吗？", {
        buttons: {
            cancel: "取消",
            defeat: '确定',
        },
    }).then((value) => {
        switch (value) {
            case "defeat":
                updateProductRefundStatus(id, orderProductDeliveryUrl);
                break;
            default:
        }
    });
});

// 备货完成
$('.ordersStockUpAccomplish').click(function () {
    var id = $(this).parent().parent().attr('id');
    swal("确定备货完成吗？", {
        buttons: {
            cancel: "取消",
            defeat: '确定',
        },
    }).then((value) => {
        switch (value) {
            case "defeat":
                updateOrderStatus(id, '', orderStockUpAccomplishUrl);
                break;
            default:
        }
    });
});

// 修改产品退款申请状态
function updateProductRefundStatus(id, url, always = '') {
    $.ajax({
        type: "post",
        url: url,
        dataType: "json",
        data: {id: id, always: always},
        success: function (result) {
            if (parseInt(result.code) === 200) {
                swal("操作成功", "", "success").then((value) => {
                    location.reload();
                });
            } else {
                rfError(result.message);
            }
        }
    });
}

// ------------------  订单操作 ------------------ //

$('.orderDelivery').click(function () {
    var id = $(this).parent().parent().attr('id');
    var status = $(this).data('status');

    swal("确定收货吗？", {
        buttons: {
            cancel: "取消",
            defeat: '确定',
        },
    }).then((value) => {
        switch (value) {
            case "defeat":
                updateOrderStatus(id, status, orderDeliveryUrl);
                break;
            default:
        }
    });
});

$('.orderClose').click(function () {
    var id = $(this).parent().parent().attr('id');
    var status = $(this).data('status');

    swal("确定关闭订单吗？", {
        buttons: {
            cancel: "取消",
            defeat: '确定',
        },
    }).then((value) => {
        switch (value) {
            case "defeat":
                updateOrderStatus(id, status, orderCloseUrl);
                break;
            default:
        }
    });
});

// 修改订单状态
function updateOrderStatus(id, status, url) {
    $.ajax({
        type: "get",
        url: url,
        dataType: "json",
        data: {id: id, order_status: status},
        success: function (result) {
            if (parseInt(result.code) === 200) {
                swal("操作成功", "", "success").then((value) => {
                    location.reload();
                });
            } else {
                rfError(result.message);
            }
        }
    });
}

// 预览用户
$(document).on("click",".member-view",function(){
    layer.open({
        type: 2,
        title: '用户详情',
        area: ['90%', '80%'],
        content: $(this).data('href')
    });
})

// 预览订单
$(document).on("click",".order-view",function(){
    layer.open({
        type: 2,
        title: '订单详情',
        area: ['90%', '80%'],
        content: $(this).data('href')
    });
})