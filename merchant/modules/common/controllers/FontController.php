<?php

namespace addons\TinyShop\merchant\modules\common\controllers;

use addons\TinyShop\merchant\controllers\BaseController;

/**
 * Class FontController
 * @package addons\TinyShop\merchant\modules\common\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class FontController extends BaseController
{
    /**
     * @return string
     */
    public function actionSelector()
    {
        return $this->render($this->action->id, [

        ]);
    }
}
