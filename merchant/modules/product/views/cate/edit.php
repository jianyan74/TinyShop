<?php

use yii\widgets\ActiveForm;
use common\enums\StatusEnum;

?>

<?php $form = ActiveForm::begin([]); ?>
<?= $form->field($model, 'pid')->dropDownList([$map], [
    'readonly' => true
]) ?>
<?= $form->field($model, 'title')->textInput(); ?>
<?= $form->field($model, 'sort')->textInput(); ?>
<?= $form->field($model, 'cover')->widget(\common\widgets\webuploader\Files::class, [
    'type' => 'images',
    'theme' => 'default',
    'themeConfig' => [],
    'config' => [
        'pick' => [
            'multiple' => false,
        ],
    ],
])->hint('建议使用 宽100像素 - 高50像素 内的 GIF 或 PNG 透明图片'); ?>
<?= $form->field($model, 'index_block_status')->checkbox(['1' => '显示']); ?>
<?= $form->field($model, 'status')->radioList(StatusEnum::getMap()); ?>
<?php ActiveForm::end(); ?>