$(document).on("click", ".orderProductAgree", function () {
    var id = $(this).parent().attr('id');
    var title = "确定同意退款吗？";
    swal(title, {
        buttons: {
            cancel: "取消",
            defeat: '确定',
        },
        title: title,
        text: '请谨慎操作',
    }).then((value) => {
        switch (value) {
            case "defeat":
                updateProductRefundStatus(id, orderProductAgreeUrl);
                break;
            default:
        }
    });
});

$(document).on("click", ".orderProductRefuse", function () {
    var id = $(this).parent().attr('id');
    var title = "您可以拒绝本次退款或者永久拒绝!";
    swal(title, {
        buttons: {
            cancel: "取消",
            defeat: '永久拒绝',
            catch: {
                text: "拒绝本次",
                value: "catch",
            },
        },
        title: title,
        text: '请谨慎操作',
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

$(document).on("click", ".orderProductTakeDelivery", function () {
    var id = $(this).parent().attr('id');
    var title = "确定收货吗？";
    swal(title, {
        buttons: {
            cancel: "取消",
            defeat: '确定',
        },
        title: title,
        text: '请谨慎操作',
    }).then((value) => {
        switch (value) {
            case "defeat":
                updateProductRefundStatus(id, orderProductTakeDeliveryUrl);
                break;
            default:
        }
    });
});

// 备货完成
$(document).on("click", ".ordersStockUpAccomplish", function () {
    var id = $(this).parent().parent().attr('id');
    var title = "确定备货完成吗？";
    swal(title, {
        buttons: {
            cancel: "取消",
            defeat: '确定',
        },
        title: title,
        text: '请谨慎操作',
    }).then((value) => {
        switch (value) {
            case "defeat":
                updateOrderStatus(id, '', orderStockUpAccomplishUrl);
                break;
            default:
        }
    });
});

// 确认接单
$(document).on("click", ".orderAffirm", function () {
    var id = $(this).parent().parent().attr('id');
    var title = "确定接单吗？";
    swal(title, {
        buttons: {
            cancel: "取消",
            defeat: '确定',
        },
        title: title,
        text: '请谨慎操作',
    }).then((value) => {
        switch (value) {
            case "defeat":
                updateOrderStatus(id, '', orderAffirmUrl);
                break;
            default:
        }
    });
});

// 修改商品退款申请状态
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

$(document).on("click", ".orderDelivery", function () {
    var id = $(this).parent().parent().attr('id');
    var status = $(this).data('status');
    var title = "确定收货吗？";

    swal(title, {
        buttons: {
            cancel: "取消",
            defeat: '确定',
        },
        title: title,
        text: '请谨慎操作',
    }).then((value) => {
        switch (value) {
            case "defeat":
                updateOrderStatus(id, status, orderDeliveryUrl);
                break;
            default:
        }
    });
});

$(document).on("click", ".orderClose", function () {
    var id = $(this).parent().parent().attr('id');
    var status = $(this).data('status');
    var title = "确定关闭订单吗？";

    swal(title, {
        buttons: {
            cancel: "取消",
            defeat: '确定',
        },
        title: title,
        text: '请谨慎操作',
    }).then((value) => {
        switch (value) {
            case "defeat":
                updateOrderStatus(id, status, orderCloseUrl);
                break;
            default:
        }
    });
});

$(document).on("click", ".orderChargeback", function () {
    var id = $(this).parent().parent().attr('id');
    var status = $(this).data('status');
    var title = "确定退掉整个订单吗？";

    swal(title, {
        buttons: {
            cancel: "取消",
            defeat: '确定',
        },
        title: title,
        text: '请谨慎操作',
    }).then((value) => {
        switch (value) {
            case "defeat":
                updateOrderStatus(id, status, orderChargebackUrl);
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
