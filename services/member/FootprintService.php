<?php

namespace addons\TinyShop\services\member;

use common\components\Service;
use common\enums\StatusEnum;
use addons\TinyShop\common\models\member\Footprint;

/**
 * Class FootprintService
 * @package addons\TinyShop\services\member
 * @author jianyan74 <751393839@qq.com>
 */
class FootprintService extends Service
{
    /**
     * @param $product
     * @param $member_id
     */
    public function create($product, $member_id)
    {
        if (!($model = $this->findByProductId($member_id, $product['id']))) {
            $model = new Footprint();
            $model = $model->loadDefaultValues();
            $model->member_id = $member_id;
            $model->merchant_id = $product['merchant_id'];
            $model->product_id = $product['id'];
            $model->cate_id = $product['cate_id'];
        }

        $model->num += 1;
        $model->save();
    }

    /**
     * 获取推荐的分类
     *
     * @param $member_id
     * @return array
     */
    public function findCateIdsByMemberId($member_id)
    {
        return Footprint::find()
            ->select(['cate_id'])
            ->where(['status' => StatusEnum::ENABLED])
            ->andFilterWhere(['member_id' => $member_id])
            ->limit(20)
            ->column();
    }

    /**
     * @param $product_id
     * @param $member_id
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findByProductId($member_id, $product_id)
    {
        return Footprint::find()
            ->where(['member_id' => $member_id, 'product_id' => $product_id])
            ->andWhere(['status' => StatusEnum::ENABLED])
            ->one();
    }
}