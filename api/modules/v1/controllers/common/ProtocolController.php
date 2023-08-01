<?php

namespace addons\TinyShop\api\modules\v1\controllers\common;

use Yii;
use common\enums\StatusEnum;
use api\controllers\OnAuthController;
use addons\TinyShop\common\models\common\Protocol;

/**
 * Class ProtocolController
 * @package addons\TinyShop\api\modules\v1\controllers\common
 * @author jianyan74 <751393839@qq.com>
 */
class ProtocolController extends OnAuthController
{
    /**
     * @var string
     */
    public $modelClass = '';

    /**
     * 不用进行登录验证的方法
     * 例如： ['index', 'update', 'create', 'view', 'delete']
     * 默认全部需要验证
     *
     * @var array
     */
    public $authOptional = ['detail'];

    /**
     * @param $name
     * @return array|\yii\db\ActiveRecord|null
     */
    public function actionDetail($name)
    {
        $data = Protocol::find()
            ->where(['name' => $name, 'status' => StatusEnum::ENABLED])
            ->andWhere(['merchant_id' => Yii::$app->services->merchant->getNotNullId()])
            ->one();

        if (empty($data)) {
            $data = new Protocol();
        }

        return $data;
    }
}
