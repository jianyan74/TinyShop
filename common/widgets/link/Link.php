<?php

namespace addons\TinyShop\common\widgets\link;

use Yii;
use yii\helpers\Json;
use common\helpers\Html;

/**
 * Class Link
 *
 * $form->field($model, 'link')->widget(addons\TinyShop\common\widgets\link\Link::class);
 *
 * @package addons\TinyShop\common\widgets\link
 * @author jianyan74 <751393839@qq.com>
 */
class Link extends \yii\widgets\InputWidget
{
    /**
     * @return string
     * @throws \Exception
     */
    public function run()
    {
        $value = $this->hasModel() ? Html::getAttributeValue($this->model, $this->attribute) : $this->value;
        $name = $this->hasModel() ? Html::getInputName($this->model, $this->attribute) : $this->name;
        empty($value) && $value = [];
        // 不引入 vue
        Yii::$app->params['notRequireVue'] = true;

        return $this->render('index', [
            'value' => Json::encode($value),
            'name' => $name,
            'title' => $value['title'] ?? '',
            'merchant_id' => Yii::$app->services->merchant->getNotNullId()
        ]);
    }
}
