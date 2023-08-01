<?php

namespace addons\TinyShop\merchant\modules\marketing\forms;

use yii\base\Model;
use addons\TinyShop\common\enums\product\PosteCoverTypeEnum;
use addons\TinyShop\common\enums\product\PosteQrTypeEnum;

/**
 * Class ProductPosterForm
 * @package addons\TinyShop\merchant\modules\marketing\forms
 * @author jianyan74 <751393839@qq.com>
 */
class ProductPosterForm extends Model
{
    public $product_poster_cover_type = PosteCoverTypeEnum::ROUNDNESS;
    public $product_poster_qr_type = PosteQrTypeEnum::COMMON_QR;
    public $product_poster_title = '为你挑选了一个好物';

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['product_poster_title'], 'required'],
            [['product_poster_title', 'product_poster_cover_type'], 'string', 'max' => 20],
            [['product_poster_qr_type'], 'string', 'max' => 30],
        ];
    }

    /**
     * @return array|string[]
     */
    public function attributeLabels()
    {
        return [
            'product_poster_title' => '推广语',
            'product_poster_cover_type' => '左上角头像显示类型',
            'product_poster_qr_type' => '二维码显示类型',
        ];
    }
}
