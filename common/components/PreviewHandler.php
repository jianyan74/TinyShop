<?php

namespace addons\TinyShop\common\components;

use yii\web\UnprocessableEntityHttpException;
use addons\TinyShop\common\forms\PreviewForm;
use addons\TinyShop\common\enums\ShippingTypeEnum;
use addons\TinyShop\common\components\delivery\LogisticsDelivery;
use addons\TinyShop\common\components\delivery\PickupDelivery;
use addons\TinyShop\common\components\delivery\LocalDelivery;
use addons\TinyShop\common\traits\AutoCalculatePriceTrait;
use addons\TinyShop\common\components\delivery\ToStoreDelivery;

/**
 * Class PreviewHandler
 * @package addons\TinyShop\common\components
 * @author jianyan74 <751393839@qq.com>
 */
class PreviewHandler
{
    use AutoCalculatePriceTrait;

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
        ShippingTypeEnum::LOCAL_DISTRIBUTION => LocalDelivery::class, // 同城配送
        ShippingTypeEnum::TO_STORE => ToStoreDelivery::class, // 到店付款
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
        // 判断配送方式
        if ($isNewRecord == true && empty($form->shipping_type)) {
            throw new UnprocessableEntityHttpException('请选择配送方式');
        }

        /** @var PreviewInterface $delivery 配送类型 */
        $delivery = new $this->_delivery[$form->shipping_type];
        $delivery->isNewRecord = $isNewRecord;
        $form = $delivery->execute($form);
        $this->_names[] = $delivery::getName();

        foreach ($this->_handlers as $handler) {
            /** @var PreviewInterface $class */
            $class = new $handler();
            $class->isNewRecord = $isNewRecord;
            if ($this->reject($class->rejectNames())) {
                // 自动计算价格
                $form = $class->execute($this->calculatePrice($form));
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
