<?php

namespace addons\TinyShop\api\modules\v1\controllers\member;

use Yii;
use common\models\member\Invoice;
use api\controllers\UserAuthController;

/**
 * 发票
 *
 * Class InvoiceController
 * @package addons\TinyShop\api\modules\v1\controllers\member
 * @author jianyan74 <751393839@qq.com>
 */
class InvoiceController extends UserAuthController
{
    /**
     * @var Invoice
     */
    public $modelClass = Invoice::class;

    /**
     * @return array|\yii\db\ActiveRecord|null
     */
    public function actionDefault()
    {
        return Yii::$app->tinyShopService->memberInvoice->findDefaultByMemberId(Yii::$app->user->identity->member_id);
    }
}