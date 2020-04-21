<?php

namespace addons\TinyShop\common\components;

use addons\TinyShop\common\models\forms\PreviewForm;

/**
 * 营销基类
 *
 * Class PreviewHandler
 * @package addons\TinyShop\common\components
 * @author jianyan74 <751393839@qq.com>
 */
abstract class PreviewInterface
{
    /**
     * @var bool
     */
    public $status = false;
    /**
     * 创建记录
     *
     * @var bool
     */
    public $isNewRecord = false;

    /**
     * 执行
     *
     * @param PreviewForm $form
     * @return mixed
     */
    abstract public function execute(PreviewForm $form): PreviewForm;

    /**
     * 排斥的营销名称
     *
     * 例如: ['fee']
     *
     * @return array
     */
    abstract public function rejectNames();

    /**
     * 营销名称
     *
     * @return string
     */
    abstract public static function getName(): string;

    /**
     * 触发营销成功
     *
     * @param PreviewForm $form
     * @return PreviewForm
     */
    public function success(PreviewForm $form): PreviewForm
    {
        $this->status = true;

        return $form;
    }
}