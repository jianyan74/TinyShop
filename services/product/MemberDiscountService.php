<?php

namespace addons\TinyShop\services\product;

use Yii;
use common\components\Service;
use common\helpers\ArrayHelper;
use common\enums\StatusEnum;
use addons\TinyShop\common\models\product\MemberDiscount;
use addons\TinyShop\common\enums\DecimalReservationEnum;
use addons\TinyShop\common\enums\PointExchangeTypeEnum;
use yii\web\UnprocessableEntityHttpException;

/**
 * Class MemberDiscountService
 * @package addons\TinyShop\services\product
 * @author jianyan74 <751393839@qq.com>
 */
class MemberDiscountService extends Service
{
    /**
     * 创建会员折扣
     *
     * @param $new_data
     * @param $member_level_decimal_reservation
     * @param $product_id
     * @param $is_open_presell
     * @param $point_exchange_type
     * @throws UnprocessableEntityHttpException
     * @throws \yii\db\Exception
     */
    public function create($new_data, $member_level_decimal_reservation, $product_id, $is_open_presell, $point_exchange_type)
    {
        $data = $this->findByProductId($product_id);
        // 已有的级别
        $oldLevel = ArrayHelper::getColumn($data, 'member_level');
        // 新的级别
        $newLevel = array_keys($new_data);

        list($updatedData, $deleteData) = ArrayHelper::comparisonIds($oldLevel, $newLevel);

        /** @var MemberDiscount $datum 更新数据 */
        foreach ($data as $datum) {
            if (in_array($datum->member_level, $updatedData)) {
                $datum->discount = $new_data[$datum->member_level];
                $datum->decimal_reservation_number = $member_level_decimal_reservation;
                if (!$datum->save()) {
                    throw new UnprocessableEntityHttpException($this->getError($datum));
                }
            }
        }

        // 插入数据
        $rows = [];
        foreach ($new_data as $key => $item) {
            if ($item > 0) {
                if ($is_open_presell == StatusEnum::ENABLED) {
                    throw new UnprocessableEntityHttpException('预售产品不可参与会员折扣');
                }

                if (PointExchangeTypeEnum::isIntegralBuy($point_exchange_type)) {
                    throw new UnprocessableEntityHttpException('积分产品不可参与会员折扣');
                }
            }

            if (!in_array($key, $updatedData)) {
                $row = [
                    'member_level' => $key,
                    'discount' => $item,
                    'product_id' => $product_id,
                    'decimal_reservation_number' => $member_level_decimal_reservation,
                ];
                $model = new MemberDiscount();
                $model->attributes = $row;
                if (!$model->validate()) {
                    throw new UnprocessableEntityHttpException($this->getError($model));
                }

                $rows[] = $row;
            }
        }

        // 判断插入
        $field = ['member_level', 'discount', 'product_id', 'decimal_reservation_number'];
        !empty($rows) && Yii::$app->db->createCommand()->batchInsert(MemberDiscount::tableName(), $field, $rows)->execute();
        // 批量删除冗余的数据
        !empty($deleteData) && MemberDiscount::deleteAll(['and', ['product_id' => $product_id], ['in', 'member_level', $deleteData]]);
    }

    /**
     * @param $product_id
     * @return array
     */
    public function getLevelListByProductId($product_id)
    {
        $allLevel = Yii::$app->services->memberLevel->findAllByEdit();
        $memberDiscount = $this->findByProductId($product_id);
        $memberDiscount = ArrayHelper::arrayKey($memberDiscount, 'member_level');

        $data = [];
        foreach ($allLevel as $value) {
            $data[] = [
                'name' => $value['name'],
                'member_level' => $value['level'],
                'product_id' => $product_id,
                'decimal_reservation_number' => $memberDiscount[$value['level']]['decimal_reservation_number'] ?? DecimalReservationEnum::DEFAULT,
                'discount' => $memberDiscount[$value['level']]['discount'] ?? 0,
            ];
        }

        return $data;
    }

    /**
     * @param $product_id
     * @param $level
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findByProductIdAndLevel($product_id, $level)
    {
        return MemberDiscount::find()
            ->where(['product_id' => $product_id, 'member_level' => $level])
            ->with('memberLevel')
            ->one();
    }

    /**
     * @param $product_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findByProductId($product_id)
    {
        return MemberDiscount::find()
            ->where(['product_id' => $product_id])
            ->with('memberLevel')
            ->all();
    }
}