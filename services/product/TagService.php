<?php

namespace addons\TinyShop\services\product;

use Yii;
use common\components\Service;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use addons\TinyShop\common\models\product\Tag;

/**
 * Class TagService
 * @package addons\TinyShop\services\product
 * @author jianyan74 <751393839@qq.com>
 */
class TagService extends Service
{
    /**
     * @param $arr
     * @return array
     */
    public function getMapByList($arr)
    {
        $tag = [];
        foreach ($arr as $item) {
            $tag[$item] = $item;
        }

        return ArrayHelper::merge($tag, $this->getMap());
    }

    /**
     * @return array
     */
    public function getMap()
    {
        return ArrayHelper::map($this->findAll(), 'title', 'title');
    }

    /**
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findAll()
    {
        return Tag::find()
            ->where(['status' => StatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->orderBy('sort asc, id desc')
            ->asArray()
            ->all();
    }
}