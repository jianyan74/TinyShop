<?php

namespace addons\TinyShop\api\modules\v1\forms;

use yii\base\Model;

/**
 * Class EvaluateStatForm
 * @package addons\TinyShop\api\modules\v1\forms
 * @author jianyan74 <751393839@qq.com>
 */
class EvaluateStatForm extends Model
{
    /**
     * 评价图
     *
     * @var bool
     */
    public $has_cover = false;
    /**
     * 评价视频
     *
     * @var bool
     */
    public $has_video = false;
    /**
     * 是否追加
     *
     * @var bool
     */
    public $has_again = false;
    /**
     * 好评
     *
     * @var bool
     */
    public $has_good = false;
    /**
     * 中评
     *
     * @var bool
     */
    public $has_ordinary = false;
    /**
     * 差评
     *
     * @var bool
     */
    public $has_negative = false;
}