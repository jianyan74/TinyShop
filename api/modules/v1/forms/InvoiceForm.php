<?php

namespace addons\TinyShop\api\modules\v1\forms;

use common\models\member\Invoice;
use Yii;
use yii\base\Model;

/**
 * Class InvoiceForm
 * @package addons\TinyShop\api\modules\v1\forms
 * @author jianyan74 <751393839@qq.com>
 */
class InvoiceForm extends Model
{
    public $member_id;
    public $order_id;
    public $invoice_id;
    public $invoice_content;

    /**
     * @var Invoice
     */
    public $invoice;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['invoice_id', 'invoice_content', 'order_id', 'member_id'], 'required'],
            [['invoice_id', 'order_id', 'member_id'], 'integer'],
            [['invoice_content'], 'string', 'max' => 500],
            [['invoice_id'], 'invoiceVerify'],
        ];
    }

    /**
     * 发票校验
     *
     * @param $attribute
     */
    public function invoiceVerify($attribute)
    {
        $this->invoice = Yii::$app->services->memberInvoice->findById($this->invoice_id, $this->member_id);

        if (!$this->invoice) {
            $this->addError($attribute, '发票信息不存在');
        }
    }
}