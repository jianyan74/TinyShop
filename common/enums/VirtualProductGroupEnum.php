<?php

namespace addons\TinyShop\common\enums;

use yii\base\Model;
use common\enums\BaseEnum;
use addons\TinyShop\common\models\virtual\Card;
use addons\TinyShop\common\models\virtual\Download;
use addons\TinyShop\common\models\virtual\NetworkDisk;
use addons\TinyShop\common\models\virtual\Virtual;

/**
 * 虚拟物品组别
 *
 * Class VirtualProductGroupEnum
 * @package addons\TinyShop\common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class VirtualProductGroupEnum extends BaseEnum
{
    const CARD = 'card';
    const NETWORK_DISK = 'network_disk';
    const DOWNLOAD = 'download';
    const VIRTUAL = 'virtual';

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::CARD => '卡卷商品', // 支持卡券类的账户秘钥销售
            self::NETWORK_DISK => '网盘商品', // 提供网络虚拟化产品的购买交易
            self::DOWNLOAD => '下载商品', // 下载商品支持网上下载
            self::VIRTUAL => '虚拟商品', // 虚拟商品支持核销管理
        ];
    }

    /**
     * 获取实体类
     *
     * @param $key
     * @param array $default
     * @return Model
     */
    public static function getModel($key, $default = [])
    {
        switch ($key) {
            case self::CARD :
                $class = Card::class;
                break;
            case self::NETWORK_DISK :
                $class = NetworkDisk::class;
                break;
            case self::DOWNLOAD :
                $class = Download::class;
                break;
            case self::VIRTUAL :
                $class = Virtual::class;
                break;
        }

        /** @var Model $model */
        $model = new $class();
        $model->attributes = $default;
        $model->load($default);

        return $model;
    }
}