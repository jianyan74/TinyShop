<?php

namespace addons\TinyShop\services\member;

use Yii;
use yii\db\ActiveQuery;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;
use yii\web\UnprocessableEntityHttpException;
use common\components\Service;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use addons\TinyShop\common\forms\CartItemForm;
use addons\TinyShop\common\models\member\CartItem;
use addons\TinyShop\common\interfaces\CartItemInterface;
use addons\TinyShop\common\enums\ProductTypeEnum;
use addons\TinyShop\common\enums\MarketingEnum;

/**
 * Class CartItemService
 * @package addons\TinyShop\services\member
 * @author jianyan74 <751393839@qq.com>
 */
class CartItemService extends Service implements CartItemInterface
{
    /**
     * 驱动
     *
     * 默认mysql
     *
     * @var string
     */
    public $drive;

    /**
     * @var CartItem
     */
    protected $modelClass;

    public function init()
    {
        if ($this->drive == 'mysql') {
            $this->modelClass = CartItem::class;
        }

        parent::init();
    }

    /**
     * @param $member_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public function all($member_id)
    {
        $data = $this->modelClass::find()
            ->where(['member_id' => $member_id])
            ->andWhere(['>=', 'status', StatusEnum::DISABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->orderBy('marketing_id desc, created_at asc')
            ->with(['baseMerchant', 'product', 'sku'])
            ->asArray()
            ->all();

        $carts = [];
        $carts[0] = [
            'merchant' => [],
            'items' => [
                [
                    'marketing' => [],
                    'products' => [],
                    'updated_at' => '', // 排序时间
                ]
            ],
        ];

        $loseEfficacy = [];
        $deleteIds = [];
        foreach ($data as $key => &$datum) {
            $datum['original_price'] = $datum['price'];
            $datum['min_buy'] = 0;
            $datum['max_buy'] = 0;
            $datum['remark'] = '';
            $datum['price'] = floatval($datum['sku']['price'] ?? $datum['price']);
            $datum['marketing_price'] = $datum['price'];
            $datum['product_name'] = $datum['product']['name'];
            $datum['product_picture'] = $datum['sku']['picture'] ?? '';
            $datum['stock'] = $datum['sku']['stock'] ?? 0;
            empty($datum['product_picture']) && $datum['product_picture'] = $datum['product']['picture'];

            // 无效商品
            $datum['product']['stock'] <= 0 && $datum['remark'] = '库存不足';
            !isset($datum['sku']) && $datum['remark'] = '宝贝已不能购买';
            if ($datum['status'] == StatusEnum::DISABLED || !empty($datum['remark'])) {
                empty($datum['remark']) && $datum['remark'] = '宝贝已失效';

                unset(
                    $datum['product'],
                    $datum['sku'],
                    $datum['baseMerchant'],
                    $datum['created_at']
                );

                $loseEfficacy[] = $datum;
                unset($data[$key]);
                continue;
            }

            unset(
                $datum['product'],
                $datum['sku'],
                $datum['baseMerchant'],
                $datum['created_at']
            );
        }

        !empty($deleteIds) && $this->deleteIds($deleteIds, $member_id);
        // 重构营销
        $marketingData = [];
        foreach ($data as $value) {
            $marketingData[] = [
                'marketing' => [],
                'marketing_tag' => '',
                'marketing_explain' => '',
                'products' => [$value],
                'updated_at' => $value['updated_at'],
            ];
        }

        // 写入购物车
        foreach ($marketingData as $marketingDatum) {
            $carts[0]['items'][] = $marketingDatum;
        }

        return [$carts, $loseEfficacy];
    }

    /**
     * 加入购物车
     *
     * @param CartItemForm $form
     * @return mixed|void
     */
    public function create(CartItemForm $form)
    {
        $sku = $form->getSku();
        $model = $this->findModel($sku['product_id'], $sku['id'], $form->member_id, $form->marketing_id, $form->marketing_type);
        $model->number += $form->number;
        $model->sku_id = $sku['id'];
        $model->sku_name = $sku['name'];
        $model->member_id = $form->member_id;
        $model->marketing_id = $form->marketing_id;
        $model->marketing_type = $form->marketing_type;
        $model->price = $sku['price'];
        $model->product_id = $sku['product']['id'];
        $model->product_picture = $sku['product']['picture'];
        $model->product_name = $sku['product']['name'];
        $model->merchant_id = $sku['merchant_id'];

        if (!in_array($sku['product']['type'], ProductTypeEnum::entity())) {
            throw new UnprocessableEntityHttpException('虚拟商品不可加入购物车');
        }

        if ($sku['stock'] < $model->number) {
            throw new UnprocessableEntityHttpException('购物车已有数量已超出库存');
        }

        if ($sku['product']['max_buy'] > 0) {
            // 当前购物车所有的数量
            $sum = Yii::$app->tinyShopService->memberCartItem->getSumByProductId($sku['product']['id'], $form->member_id) + $form->number;
            if ($sum > $sku['product']['max_buy']) {
                throw new UnprocessableEntityHttpException('购物车已存在该商品，每人最多购买数量为' . $sku['product']['max_buy']);
            }
        }

        if ($sku['product']['min_buy'] > $model->number) {
            throw new UnprocessableEntityHttpException('每人最少购买数量为' . $sku['product']['min_buy']);
        }

        if (!$model->save()) {
            throw new UnprocessableEntityHttpException($this->getError($model));
        }

        return $model;
    }

    /**
     * 修改购物车数量
     *
     * @param CartItemForm $form
     * @return mixed|void
     */
    public function updateNumber(CartItemForm $form)
    {
        $model = $this->findById($form->id, $form->member_id);
        if (!$model) {
            throw new UnprocessableEntityHttpException('购物车商品已被移除');
        }

        $sku = Yii::$app->tinyShopService->productSku->findById($model->sku_id);
        if ($sku['product']['max_buy'] > 0) {
            // 当前购物车所有的数量
            $sum = Yii::$app->tinyShopService->memberCartItem->findSumByProductId($sku['product']['id'], $form->member_id);
            $sum = ($sum - $model->number) + $form->number;

            if ($sum > $sku['product']['max_buy']) {
                throw new UnprocessableEntityHttpException('每人最多购买数量为' . $sku['product']['max_buy']);
            }
        }

        $model->number = $form->number;
        if ($sku['stock'] < $model->number) {
            throw new UnprocessableEntityHttpException('购物车已有数量已超出库存');
        }

        if ($sku['product']['min_buy'] > $model->number) {
            throw new UnprocessableEntityHttpException('每人最少购买数量为' . $sku['product']['min_buy']);
        }

        if (!$model->save()) {
            throw new UnprocessableEntityHttpException($this->getError($model));
        }

        return $model;
    }

    /**
     * 修改规格
     *
     * @param CartItemForm $form
     */
    public function updateSku(CartItemForm $form)
    {
        $sku = $form->getSku();
        /** @var CartItem $oldCartItem */
        $oldCartItem = Yii::$app->tinyShopService->memberCartItem->findById($form->id, $form->member_id);
        if (!$oldCartItem) {
            throw new UnprocessableEntityHttpException('购物车找不到该商品');
        }

        Yii::$app->tinyShopService->memberCartItem->deleteIds([$form->id], $form->member_id);
        $model = $this->findModel($sku['product_id'], $sku['id'], $form->member_id);
        $model->number = $oldCartItem->number;
        $model->member_id = $oldCartItem->member_id;
        $model->sku_id = $sku->id;
        $model->sku_name = $sku->name;
        $model->price = $sku->price;
        $model->merchant_id = $sku->merchant_id;
        $model->product_id = $sku->product_id;
        $model->product_name = $oldCartItem->product_name;
        $model->product_picture = $oldCartItem->product_picture;
        $form->verifyValid($model->number);
        if (!$model->save()) {
            throw new NotFoundHttpException($this->getError($model));
        }

        return $model;
    }

    /**
     * 删除一组
     *
     * @param array $sku_ids
     * @param $member_id
     * @return bool
     */
    public function deleteIds(array $ids, $member_id)
    {
        return $this->modelClass::deleteAll([
            'and',
            ['in', 'id', $ids,],
            ['member_id' => $member_id]
        ]);
    }

    /**
     * 清空购物车
     *
     * @param $member_id
     * @param bool $lose_status 失效
     * @return bool
     */
    public function clear($member_id, $lose_status = false)
    {
        $where = [];
        $where['member_id'] = $member_id;
        if ($lose_status == StatusEnum::ENABLED) {
            $where['status'] = StatusEnum::DISABLED;
        }

        $this->modelClass::deleteAll($where);

        return true;
    }

    /**
     * 获取该产品总数量
     *
     * @return false|string|null
     */
    public function getSumByProductId($product_id, $member_id)
    {
        return $this->modelClass::find()
            ->select('sum(number)')
            ->where(['product_id' => $product_id])
            ->andWhere(['member_id' => $member_id])
            ->andWhere(['status' => StatusEnum::ENABLED])
            ->scalar() ?? 0;
    }

    /**
     * @param $member_id
     * @param $marketing_id
     * @return false|string|null
     */
    public function getCountByPlusBuyId($member_id, $marketing_id)
    {
        return $this->modelClass::find()
            ->where(['member_id' => $member_id, 'marketing_id' => $marketing_id, 'marketing_type' => MarketingEnum::PLUS_BUY, 'status' => StatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->sum('number');
    }

    /**
     * @param $ids
     * @param $all
     * @return void
     */
    public function loseByProductIds($ids, $all = false)
    {
        $this->modelClass::updateAll(['status' => StatusEnum::DISABLED], ['in', 'product_id', $ids]);
    }

    /**
     * @param $skuIds
     * @return mixed|void
     */
    public function loseBySkus($skuIds)
    {
        $this->modelClass::updateAll(['status' => StatusEnum::DISABLED], ['in', 'sku_id', $skuIds]);
    }

    /**
     * 获取总数量
     *
     * @param $member_id
     * @return int|string
     */
    public function findCountByMemberId($member_id)
    {
        return $this->modelClass::find()
            ->select('count(id)')
            ->where(['member_id' => $member_id, 'status' => StatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->scalar() ?? 0;
    }

    /**
     * @param $member_id
     * @param $marketing_id
     * @return false|string|null
     */
    public function findSumByPlusBuyId($member_id, $marketing_id)
    {
        return $this->modelClass::find()
                ->where([
                        'member_id' => $member_id,
                        'marketing_id' => $marketing_id,
                        'marketing_type' => MarketingEnum::PLUS_BUY,
                        'status' => StatusEnum::ENABLED]
                )
                ->sum('number') ?? 0;
    }

    /**
     * 获取该商品总数量
     *
     * @return false|string|null
     */
    public function findSumByProductId($product_id, $member_id)
    {
        return $this->modelClass::find()
                ->select('sum(number)')
                ->where(['member_id' => $member_id, 'product_id' => $product_id, 'status' => StatusEnum::ENABLED])
                ->scalar() ?? 0;
    }

    /**
     * @param $sku_id
     * @param $member_id
     * @return array|\yii\db\ActiveRecord|null|CartItem
     */
    public function findBySukId($sku_id, $member_id)
    {
        return $this->modelClass::find()
            ->where(['member_id' => $member_id, 'sku_id' => $sku_id, 'status' => StatusEnum::ENABLED])
            ->one();
    }

    /**
     * @param $id
     * @param $member_id
     * @return array|\yii\db\ActiveRecord|null|CartItem
     */
    public function findById($id, $member_id)
    {
        return $this->modelClass::find()
            ->where(['id' => $id, 'member_id' => $member_id, 'status' => StatusEnum::ENABLED])
            ->one();
    }

    /**
     * 查询
     *
     * @param $ids
     * @param $member_id
     * @param false $plus_buy
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findByIds($ids, $member_id, $plus_buy = false)
    {
        $condition = $plus_buy == false ? ['marketing_id' => 0] : [];

        return $this->modelClass::find()
            ->where(['in', 'id', $ids])
            ->andWhere(['status' => StatusEnum::ENABLED, 'member_id' => $member_id])
            ->andFilterWhere($condition)
            ->with(['product.myGet' => function(ActiveQuery $query) use ($member_id) {
                return $query->andWhere(['buyer_id' => $member_id]);
            }, 'product.cateMap', 'sku', 'merchant'])
            ->asArray()
            ->all();
    }

    /**
     * @param $product_id
     * @param $sku_id
     * @param $member_id
     * @param $marketing_id
     * @param $marketing_type
     * @return CartItem|array|\yii\db\ActiveRecord
     */
    protected function findModel($product_id, $sku_id, $member_id, $marketing_id = 0, $marketing_type = '')
    {
        $model = $this->modelClass::find()
            ->where([
                'product_id' => $product_id,
                'member_id' => $member_id,
                'marketing_id' => $marketing_id,
                'status' => StatusEnum::ENABLED,
            ])
            ->andFilterWhere(['marketing_type' => $marketing_type])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->andFilterWhere(['sku_id' => $sku_id])
            ->one();

        if (!$model) {
            /** @var CartItem $model */
            $model = new $this->modelClass();
            return $model->loadDefaultValues();
        }

        return $model;
    }
}
