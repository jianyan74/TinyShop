<?= $form->field($model, 'ladderPreferentialData')->widget(unclead\multipleinput\MultipleInput::class, [
    'max' => 10,
    'min' => 0,
    'columns' => [
        [
            'name'  => 'quantity',
            'title' => '数量',
            'options' => [
                'class' => 'input-priority'
            ]
        ],
        [
            'name'  => 'price',
            'title' => '金额',
            'options' => [
                'class' => 'input-priority'
            ]
        ]
    ]
])->label(false)->hint('<span class="orange">设置商品阶梯优惠，当购买数量达到所设数量时，商品单价 = 商品销售价 - 优惠价格</span>');
?>