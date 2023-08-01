<?php

namespace addons\TinyShop\common\enums;

use common\enums\BaseEnum;

/**
 * Class MemberActiveEnum
 * @package addons\TinyShop\common\enums
 */
class MemberActiveEnum extends BaseEnum
{
    const ACTIVE = 1;
    const ACTIVE_HALF = 2;
    const SLEEP_HALF = 3;
    const SLEEP = 4;

    /**
     * @return string[]
     */
    public static function getMap(): array
    {
        return [
            self::ACTIVE => '活跃顾客',
            self::ACTIVE_HALF => '半活跃顾客',
            self::SLEEP_HALF => '半沉睡顾客',
            self::SLEEP => '沉睡顾客',
        ];
    }

    /**
     * @param $key
     * @return array
     */
    public static function getTime($key)
    {
        $data = [];
        $day = 24 * 3600;

        switch ($key) {
            case self::ACTIVE :
                $data = [
                    'start_time' => time() - $day * 90,
                    'end_time' => time(),
                    'explain' => '3个月内有消费',
                ];
                break;
            case self::ACTIVE_HALF :
                $data = [
                    'start_time' => time() - $day * 180,
                    'end_time' => time() - $day * 90,
                    'explain' => '3-6个月内有消费',
                ];
                break;
            case self::SLEEP_HALF :
                $data = [
                    'start_time' => time() - $day * 270,
                    'end_time' => time() - $day * 180,
                    'explain' => '6-9个月内有消费',
                ];
                break;
            case self::SLEEP :
                $data = [
                    'start_time' => 0,
                    'end_time' => time() - $day * 270,
                    'explain' => '9个月以上没消费',
                ];
                break;
        }

        return $data;
    }
}