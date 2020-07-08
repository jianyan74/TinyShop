<?php

namespace addons\TinyShop\services\product;

use addons\TinyShop\common\enums\ProductMemberDiscountTypeEnum;
use common\enums\WhetherEnum;
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
     * @param $product_id
     * @param $is_open_presell
     * @param $point_exchange_type
     * @throws UnprocessableEntityHttpException
     * @throws \yii\db\Exception
     */
    public function create($new_data, $product_id, $is_open_presell, $point_exchange_type)
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
        $field = ['member_level', 'discount', 'product_id'];
        !empty($rows) && Yii::$app->db->createCommand()->batchInsert(MemberDiscount::tableName(), $field, $rows)->execute();
        // 批量删除冗余的数据
        !empty($deleteData) && MemberDiscount::deleteAll(['and', ['product_id' => $product_id], ['in', 'member_level', $deleteData]]);
    }

    /**
     * @param $product_id
     * @param $data
     * @throws \yii\db\Exception
     */
    public function createByCopy($product_id, $data)
    {
        $rows = [];
        foreach ($data as $datum) {
            $rows[] = [
                'member_level' => $datum['member_level'],
                'discount' => $datum['discount'],
                'product_id' => $product_id,
            ];
        }

        $field = ['member_level', 'discount', 'product_id'];
        !empty($rows) && Yii::$app->db->createCommand()->batchInsert(MemberDiscount::tableName(), $field, $rows)->execute();
    }

    /**
     * 获取当前产品的会员折扣
     *
     * @param $product
     * @param $currentLevel
     * @return array|mixed|\yii\db\ActiveRecord|null
     */
    public function getOneMemberDiscount($product, $currentLevel)
    {
        if ($product['is_open_member_discount'] == WhetherEnum::ENABLED) {
            // 折扣类型
            if ($product['member_discount_type'] == ProductMemberDiscountTypeEnum::SYSTEM) {
                // 参与系统折扣
                return Yii::$app->services->memberLevel->findByLevel($currentLevel);
            } else {
                // 参与自定义折扣
                $discount = $this->findByProductIdAndLevel($product['id'], $currentLevel);
                $memberLevel = $discount['memberLevel'];
                $memberLevel['discount'] = $discount['discount'];

                return $memberLevel;
            }
        }

        return [];
    }

    /**
     * 获取会员折扣
     *
     * @param array $defaultProducts
     * @param $currentLevel
     * @return array|array[]
     */
    public function getMemberDiscount(array $defaultProducts, $currentLevel)
    {
        if (empty($currentLevel)) {
            return [[], []];
        }

        $memberDiscountSystemProductIds = $memberDiscountCustomProductIds = [];
        foreach ($defaultProducts as $defaultProduct) {
            if ($defaultProduct['is_open_member_discount'] == WhetherEnum::ENABLED) {
                // 折扣类型
                if ($defaultProduct['member_discount_type'] == ProductMemberDiscountTypeEnum::SYSTEM) {
                    // 参与系统折扣
                    $memberDiscountSystemProductIds[] = $defaultProduct['id'];
                } else {
                    // 参与自定义折扣
                    $memberDiscountCustomProductIds[] = $defaultProduct['id'];
                }
            }
        }

        $memberDiscounts = [];
        // 系统折扣
        if ($memberDiscountSystemProductIds) {
            $level = Yii::$app->services->memberLevel->findByLevel($currentLevel);
            foreach ($memberDiscountSystemProductIds as $discountSystemProductId) {
                $memberDiscounts[$discountSystemProductId] = $level;
            }
        }

        // 自定义折扣
        if ($memberDiscountCustomProductIds) {
            $productMemberDiscount = Yii::$app->tinyShopService->productMemberDiscount->findByProductIdsAndLevel($memberDiscountCustomProductIds, $currentLevel);
            foreach ($productMemberDiscount as $item) {
                if (!empty($item['memberLevel'])) {
                    $memberDiscounts[$item['product_id']] = $item['memberLevel'];
                    $memberDiscounts[$item['product_id']]['discount'] = $item['discount'];
                }
            }
        }

        return $memberDiscounts;
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
                'discount' => $memberDiscount[$value['level']]['discount'] ?? 0,
                'sys_discount' => $value['discount'],
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
     * 获取一组会员折扣
     *
     * @param $product_ids
     * @param $level
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findByProductIdsAndLevel($product_ids, $level)
    {
        return MemberDiscount::find()
            ->where(['member_level' => $level])
            ->andWhere(['in', 'product_id', $product_ids])
            ->with('memberLevel')
            ->asArray()
            ->all();
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