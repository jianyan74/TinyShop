<?php

namespace addons\TinyShop\api\modules\v1\forms;

use BaconQrCode\Common\Mode;
use common\enums\SortEnum;
use common\helpers\ArrayHelper;

/**
 * Class PickupPointSearchForm
 * @package addons\TinyShop\api\modules\v1\forms
 * @author jianyan74 <751393839@qq.com>
 */
class PickupPointSearchForm extends Mode
{
    /**
     * @var string[]
     */
    public $select = ['*'];

    /*-- 查询 --*/
    public $keyword;

    /**
     * @var
     */
    public $longitude;
    /**
     * @var
     */
    public $latitude;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['longitude', 'latitude'], 'string'],
        ];
    }

    /**
     * @return array
     */
    public function getOrderBy()
    {
        // 排序
        $orderBy = [];
        (!empty($this->longitude) && !empty($this->latitude)) && $orderBy[] = 'distance ' . SortEnum::ASC;

        return $orderBy;
    }

    /**
     * @return array|string[]
     */
    public function getSelect()
    {
        if (!empty($this->longitude) && !empty($this->latitude)) {
            return ArrayHelper::merge($this->select, [
                "(st_distance(point(lng, lat), point($this->longitude, $this->latitude)) / 0.0111) as distance",
            ]);
        }

        return $this->select;
    }
}