<?php

use addons\TinyShop\common\models\product\VirtualType;
use addons\TinyShop\common\enums\VirtualProductGroupEnum;

/** @var VirtualType $virtualType 虚拟商品 */
if (!$virtualType) {
    return false;
}

$virtual = VirtualProductGroupEnum::getModel($virtualType->group, $virtualType->value);

switch ($virtualType->group) {
    case VirtualProductGroupEnum::VIRTUAL :
        echo $form->field($virtual, 'text_use_time')->textInput();
        echo $form->field($virtual, 'text_rule')->textInput();
        break;
    case VirtualProductGroupEnum::CARD :
        echo $form->field($virtual, 'card_password')->textarea();
        break;
    case VirtualProductGroupEnum::NETWORK_DISK :
        echo $form->field($virtual, 'cloud_address')->textInput();
        echo $form->field($virtual, 'cloud_password')->textInput();
        break;
    case VirtualProductGroupEnum::DOWNLOAD :
        echo $form->field($virtual, 'text_download_resources')->textInput();
        echo $form->field($virtual, 'unzip_password')->textInput();
        break;
}

echo $form->field($virtualType, 'group')->hiddenInput()->label(false);
echo '<div class="row">';
echo '<div class="col-sm-6">' . $form->field($virtualType, 'period')->textInput()->hint('如果值为 0 表示不限制') . '</div>';
echo '<div class="col-sm-6">' . $form->field($virtualType, 'confine_use_number')->textInput([
    'readonly' => true
])->hint('如果值为 0 表示不限制') . '</div>';
echo '</div>';

echo $form->field($model, 'is_virtual')->hiddenInput([
    'value' => 1,
])->label(false);
