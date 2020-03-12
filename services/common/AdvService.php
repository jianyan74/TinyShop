<?php

namespace addons\TinyShop\services\common;

use common\components\Service;
use common\enums\StatusEnum;
use addons\TinyShop\common\enums\AdvLocalEnum;
use addons\TinyShop\common\models\common\Adv;

/**
 * Class AdvService
 * @package addons\TinyShop\services\common
 * @author jianyan74 <751393839@qq.com>
 */
class AdvService extends Service
{
    /**
     * 获取广告列表
     *
     * @param array $locals
     * @return array
     */
    public function getListByLocals(array $locals)
    {
        if (empty($locals)) {
            return $locals;
        }

        $data = Adv::find()
            ->where(['status' => StatusEnum::ENABLED])
            ->andWhere(['in', 'location', $locals])
            ->andWhere(['<', 'start_time', time()])
            ->andWhere(['>', 'end_time', time()])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->orderBy('sort asc, id desc')
            ->cache(60)
            ->asArray()
            ->all();

        $dataByLocal = [];
        foreach ($data as $datum) {
            $dataByLocal[$datum['location']][] = $datum;
        }

        $result = [];
        $config = AdvLocalEnum::config();
        foreach ($locals as $local) {
            if (isset($dataByLocal[$local]) && isset($config[$local])) {
                // 轮播
                if ($config[$local]['multiple'] == StatusEnum::ENABLED) {
                    $result[$local] = $dataByLocal[$local];
                } else {
                    $result[$local][] = $dataByLocal[$local][0];
                }
            } else {
                $result[$local] = [];
            }
        }

        return $result;
    }
}