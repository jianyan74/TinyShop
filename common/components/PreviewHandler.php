<?php

namespace addons\TinyShop\common\components;

use addons\TinyShop\common\enums\ShippingTypeEnum;
use addons\TinyShop\common\models\forms\PreviewForm;
use addons\TinyShop\common\components\delivery\CashAgainstDelivery;
use addons\TinyShop\common\components\delivery\LogisticsDelivery;
use addons\TinyShop\common\components\delivery\PickupDelivery;
use addons\TinyShop\common\components\delivery\LocalDistributionDelivery;
use addons\TinyShop\common\components\delivery\VirtualDelivery;

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
     * 配送类型
     *
     * @var array
     */
    private $_delivery = [
        ShippingTypeEnum::LOGISTICS => LogisticsDelivery::class, // 物流配送
        ShippingTypeEnum::PICKUP => PickupDelivery::class, // 自提
        ShippingTypeEnum::CASH_AGAINST => CashAgainstDelivery::class, // 货到付款
        ShippingTypeEnum::LOCAL_DISTRIBUTION => LocalDistributionDelivery::class, // 本地配送
        ShippingTypeEnum::VIRTUAL => VirtualDelivery::class, // 虚拟商品
    ];

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
     * @param bool $isNewRecord 创建记录
     * @return PreviewForm|mixed
     */
    public function start(PreviewForm $form, $isNewRecord = false)
    {
        /** @var PreviewInterface $delivery 配送类型 */
        $delivery = new $this->_delivery[$form->shipping_type];
        $form = $delivery->execute($form);
        $this->_names[] = $delivery::getName();

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