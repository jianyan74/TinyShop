<?php

namespace addons\TinyShop\merchant\modules\common\controllers;

use common\enums\StatusEnum;
use common\helpers\ResultHelper;
use addons\TinyShop\common\models\common\AttributeValue;
use addons\TinyShop\merchant\controllers\BaseController;
use addons\TinyShop\common\enums\AttributeValueTypeEnum;

/**
 * Class AttributeValueController
 * @package addons\TinyShop\merchant\modules\common\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class AttributeValueController extends BaseController
{
    /**
     * @return array|mixed
     */
    public function actionList($attribute_id)
    {
        $data = AttributeValue::find()
            ->where([
                'attribute_id' => $attribute_id,
                'status' => StatusEnum::ENABLED
            ])
            ->orderBy('sort asc')
            ->asArray()
            ->all();

        foreach ($data as &$datum) {
            if ($datum['type'] != AttributeValueTypeEnum::TEXT) {
                $datum['value'] = explode(',', $datum['value']);
                $datum['data'] = [];
            }
        }

        return ResultHelper::json(200, 'ok', $data);
    }
}
