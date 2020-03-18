<?php

namespace addons\TinyShop\common\components;

use addons\TinyShop\common\models\forms\PreviewForm;

/**
 * Class PreviewHandler
 * @package addons\TinyShop\common\components
 * @author jianyan74 <751393839@qq.com>
 */
class PreviewHandler
{
    /**
     * @var PreviewHandler
     */
    private $_handlers;

    /**
     * @var array
     */
    private $_names = [];

    /**
     * PreviewHandler constructor.
     * @param $handlers
     */
    public function __construct($handlers)
    {
        $this->_handlers = $handlers;
    }

    /**
     * 运行
     *
     * @param PreviewForm $form
     * @return PreviewForm|PreviewForm|mixed
     */
    public function start(PreviewForm $form, $isNewRecord = false)
    {
        foreach ($this->_handlers as $handler) {
            /** @var PreviewInterface $class */
            $class = new $handler();
            $class->isNewRecord = $isNewRecord;
            if ($this->reject($class->rejectNames())) {
                $form = $class->execute($form);
                // 判断是否执行成功并加入已执行列表
                if ($class->status == true) {
                    $this->_names[] = $class::getName();
                }
            }
        }

        return $form;
    }

    /**
     * 获取已执行的营销名称
     *
     * @return array
     */
    public function executedNames(): array
    {
        return $this->_names;
    }

    /**
     * 互斥
     *
     * @param array $names
     * @return bool
     */
    protected function reject(array $names)
    {
        if (empty($names)) {
            return true;
        }

        foreach ($this->_names as $name) {
            if (in_array($name, $names)) {
                return false;
            }
        }

        return true;
    }
}