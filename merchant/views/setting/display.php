<?php

use yii\widgets\ActiveForm;
use common\widgets\webuploader\Files;
use common\enums\WhetherEnum;
use addons\TinyShop\common\enums\OrderOversoldEnum;

$this->title = '参数设置';
$this->params['breadcrumbs'][] = ['label' => $this->title];

?>

<?php $form = ActiveForm::begin([
    'fieldConfig' => [
        'template' => "<div class='row'><div class='col-sm-2 text-right'>{label}</div><div class='col-sm-5'>{input}\n{hint}\n{error}</div></div>",
    ],
]); ?>
<div class="card card-primary card-outline card-outline-tabs">
    <div class="card-header border-bottom-0">
        <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
            <li class="nav-item"><a class="nav-link active" data-toggle="pill" href="#custom-order">订单设置</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#custom-pay">支付设置</a></li>
            <li class="nav-item <?= Yii::$app->services->devPattern->isB2B2C() ? '' : 'hide'; ?>"><a class="nav-link" data-toggle="pill" href="#custom-product">商品设置</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#custom-logistics">配送设置</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#custom-member">会员注册</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#custom-switch">显示开关</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#custom-app">应用配置</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#custom-share">默认分享设置</a></li>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content">
            <div class="tab-pane fade active show" id="custom-order">
                <blockquote>
                    <p>订单设置</p>
                </blockquote>
                <?= $form->field($model, 'order_buy_close_time')->textInput(); ?>
                <?= $form->field($model, 'order_auto_delivery')->textInput(); ?>
                <div class="hide">
                    <?= $form->field($model, 'order_not_pay_remind')->radioList(WhetherEnum::getOpenMap())
                        ->hint('开启后，当买家存在待付款订单时，进入首页、商品详情页、微页面、物流详情页时展示催付弹窗。');
                    ?>
                </div>
                <?= $form->field($model, 'order_auto_complete_time')->dropDownList([
                    0 => '立即',
                    1 => '1天',
                    2 => '2天',
                    3 => '3天',
                    4 => '4天',
                    5 => '5天',
                    6 => '6天',
                    7 => '7天',
                ]); ?>
                <?= $form->field($model, 'order_after_sale_date')->textInput(); ?>
                <?= $form->field($model, 'order_min_pay_money')->textInput(); ?>
                <?= $form->field($model, 'order_oversold')->radioList(OrderOversoldEnum::getMap()); ?>
                <blockquote>
                    <p>评价设置</p>
                </blockquote>
                <?= $form->field($model, 'order_evaluate_day')->textInput(); ?>
                <?= $form->field($model, 'order_evaluate')->textarea(); ?>
                <blockquote>
                    <p>发票设置</p>
                </blockquote>
                <?= $form->field($model, 'order_invoice_status')->radioList(WhetherEnum::getOpenMap()); ?>
                <?= $form->field($model, 'order_invoice_tax')->textInput(); ?>
                <?= $form->field($model, 'order_invoice_content')->textarea(); ?>
            </div>
            <div class="tab-pane fade" id="custom-pay">
                <blockquote>
                    <p>支付设置</p>
                </blockquote>
                <?= $form->field($model, 'order_balance_pay')->radioList(WhetherEnum::getOpenMap()); ?>
                <?= $form->field($model, 'order_wechat_pay')->radioList(WhetherEnum::getOpenMap()); ?>
                <?= $form->field($model, 'order_ali_pay')->radioList(WhetherEnum::getOpenMap()); ?>
                <?= $form->field($model, 'order_bytedance_pay')->radioList(WhetherEnum::getOpenMap()); ?>
                <?= $form->field($model, 'order_cash_against_pay')->radioList(WhetherEnum::getOpenMap()); ?>
            </div>
            <div class="tab-pane fade" id="custom-product">
                <blockquote>
                    <p>商品设置</p>
                </blockquote>
                <?= $form->field($model, 'product_audit_status')->radioList(WhetherEnum::getOpenMap()); ?>
            </div>
            <div class="tab-pane fade" id="custom-logistics">
                <blockquote>
                    <p>配送设置</p>
                </blockquote>
                <?= $form->field($model, 'logistics')->radioList(WhetherEnum::getOpenMap()); ?>
                <?= $form->field($model, 'logistics_select')->radioList(WhetherEnum::getOpenMap()); ?>
                <?= $form->field($model, 'logistics_pick_up')->radioList(WhetherEnum::getOpenMap()); ?>
                <?= $form->field($model, 'logistics_local_distribution')->radioList(WhetherEnum::getOpenMap()); ?>
                <?= $form->field($model, 'logistics_local_distribution_type')->radioList([1 => '商家自己配送', 2 => '骑手配送'])->hint('如果选择骑手配送需安装【配送】插件'); ?>
            </div>
            <div class="tab-pane fade" id="custom-member">
                <blockquote>
                    <p>普通登录注册设置</p>
                </blockquote>
                <?= $form->field($model, 'member_login_weight')->radioList(['account' => '账号登录页面', 'agent' => '授权登录页面']); ?>
                <?= $form->field($model, 'member_login')->radioList(WhetherEnum::getOpenMap()); ?>
                <?= $form->field($model, 'member_register')->radioList(WhetherEnum::getOpenMap()); ?>
                <?= $form->field($model, 'member_register_promoter_code')->radioList(WhetherEnum::getOpenMap()); ?>

                <?= $form->field($model, 'member_mobile_login_be_register')->radioList(WhetherEnum::getMap()); ?>
                <?= $form->field($model, 'member_agreement_default_select')->radioList(WhetherEnum::getMap()); ?>
                <blockquote>
                    <p>微信小程序</p>
                </blockquote>
                <?= $form->field($model, 'member_mini_program_register_get_mobile')->radioList(WhetherEnum::getOpenMap()); ?>
                <blockquote>
                    <p>第三方平台注册设置</p>
                </blockquote>
                <?= $form->field($model, 'member_third_party_login')->radioList(WhetherEnum::getOpenMap()); ?>
                <?= $form->field($model, 'member_third_party_binding_type')->radioList([0 => '强制绑定账户', 1 => '非强制绑定账户'])->hint('强制绑定账户：第三方账户（微信公众号，微信小程序，qq）不会获取粉丝信息后给系统直接注册会员，而是需要绑定系统中的现有账户（用户名，手机，邮箱），如果没有账户通过注册账户然后与第三方账户信息进行绑定。<br>非强制绑定账户：第三方账户（微信公众号，微信小程序，qq）通过系统回调获取到对应粉丝信息，系统会根据粉丝信息自动注册一个会员，会员昵称为粉丝名称，同时将第三方账户的信息与自动注册的会员进行绑定。'); ?>
            </div>
            <div class="tab-pane fade" id="custom-switch">
                <blockquote>
                    <p>显示开关</p>
                </blockquote>
                <?= $form->field($model, 'store_entrance')->radioList(WhetherEnum::getOpenMap()); ?>
                <?= $form->field($model, 'member_recharge')->radioList(WhetherEnum::getOpenMap()); ?>
                <?= $form->field($model, 'index_cate')->radioList(WhetherEnum::getOpenMap()); ?>
                <?= $form->field($model, 'index_decoration')->radioList(WhetherEnum::getOpenMap())->hint('关闭后显示默认的首页布局'); ?>
                <?= $form->field($model, 'address_select_type')->radioList([1 => '三级联动', 2 => '地图选点']); ?>
            </div>
            <div class="tab-pane fade" id="custom-app">
                <blockquote>
                    <p>应用配置</p>
                </blockquote>
                <?= $form->field($model, 'app_name')->textInput(); ?>
                <?= $form->field($model, 'app_logo')->widget(Files::class, [
                    'type' => 'images',
                    'theme' => 'default',
                    'themeConfig' => [],
                    'config' => [
                        'pick' => [
                            'multiple' => false,
                        ],
                    ],
                ]); ?>
                <?= $form->field($model, 'app_h5_url')->textInput(); ?>
            </div>
            <div class="tab-pane fade" id="custom-share">
                <blockquote>
                    <p>默认分享设置</p>
                </blockquote>
                <?= $form->field($model, 'share_title')->textInput(); ?>
                <?= $form->field($model, 'share_cover')->widget(Files::class, [
                    'type' => 'images',
                    'theme' => 'default',
                    'themeConfig' => [],
                    'config' => [
                        'pick' => [
                            'multiple' => false,
                        ],
                    ]
                ]); ?>
                <?= $form->field($model, 'share_desc')->textarea(); ?>
                <?= $form->field($model, 'share_link')->textInput(); ?>
            </div>
            <div class="box-footer text-center">
                <button class="btn btn-primary" type="submit">保存</button>
            </div>
        </div>
    </div>
    <!-- /.card -->
</div>
<?php ActiveForm::end(); ?>
