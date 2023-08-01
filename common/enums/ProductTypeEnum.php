<?php

namespace addons\TinyShop\common\enums;

use yii\base\Model;
use yii\helpers\Json;
use common\enums\BaseEnum;

/**
 * 商品类型
 *
 * Class ProductTypeEnum
 * @package addons\TinyShop\common\enums
 */
class ProductTypeEnum extends BaseEnum
{
    // 实物商品类型
    const ENTITY = 0;
    // 虚拟(核销)商品类型
    const VIRTUAL = 201;
    const CARD_VOLUME = 202;

    /**
     * @return string[]
     */
    public static function getMap(): array
    {
        return [
            self::ENTITY => '实物商品', // 物流发货
            self::VIRTUAL => '虚拟商品', // 无需物流(无需物流/可自定义提交内容/充值缴费)
            self::CARD_VOLUME => '电子卡密', // 支持核销 (卡卷有效期/吃喝玩乐类)
        ];
    }

    /**
     * @return string[]
     */
    public static function getList(): array
    {
        return [
            [
                'name' => self::ENTITY,
                'title' => self::getValue(self::ENTITY),
                'explain' => "物流发货",
            ],
            [
                'name' => self::VIRTUAL,
                'title' => self::getValue(self::VIRTUAL),
                'explain' => "无需物流",
            ],
            [
                'name' => self::CARD_VOLUME,
                'title' => self::getValue(self::CARD_VOLUME),
                'explain' => "支持核销",
            ]
        ];
    }

    /**
     * 物流/自提/同城商品类型
     *
     * @return int[]
     */
    public static function entity()
    {
        return [
            self::ENTITY
        ];
    }

    /**
     * 核销商品
     *
     * @return int[]
     */
    public static function verify()
    {
        return [
            self::VIRTUAL,
            self::CARD_VOLUME,
        ];
    }

    /**
     * 虚拟商品全部类型
     *
     * @return array
     */
    public static function virtual()
    {
        $keys = self::getKeys();
        $entity = self::entity();
        $data = [];
        foreach ($keys as $key) {
            !in_array($key, $entity) && $data[] = $key;
        }

        return $data;
    }

    /**
     * 获取实体类
     *
     * @param $key
     * @param $default
     * @return bool|Model
     */
    public static function getModel($key, $default)
    {
        if (!$key) {
            return false;
        }

        $class = '';
        switch ($key) {
            case self::VIRTUAL :
                break;
            case self::CARD_VOLUME :
                $class = CardVolume::class;
                break;
        }

        if (empty($class)) {
            return false;
        }

        if (!empty($default) && !is_array($default)) {
            $default = Json::decode($default);
        }

        /** @var Model $model */
        $model = new $class();
        $model->attributes = $default;
        $model->load($default);

        return $model;
    }
}
