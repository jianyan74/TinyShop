<?php

namespace addons\TinyShop\services\member;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\web\UnprocessableEntityHttpException;
use common\components\Service;
use common\enums\StatusEnum;
use common\helpers\ResultHelper;
use addons\TinyShop\common\models\member\CartItem;
use addons\TinyShop\common\interfaces\CartItemInterface;
use addons\TinyShop\common\enums\PointExchangeTypeEnum;

/**
 * 已登录的购物车
 *
 * Class CartItemService
 * @package addons\TinyShop\common\services
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
     * 列表
     *
     * @return ActiveDataProvider
     */
    public function list($member_id)
    {
        return new ActiveDataProvider([
            'query' => $this->modelClass::find()
                ->where(['status' => StatusEnum::ENABLED, 'member_id' => $member_id])
                ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
                ->orderBy('id desc')
                ->with(['product', 'sku'])
                ->asArray(),
            'pagination' => [
                'pageSize' => 10,
                'validatePage' => false,// 超出分页不返回data
            ],
        ]);
    }

    /**
     * @param $sku_id
     * @param $member_id
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findBySukId($sku_id, $member_id)
    {
        return $this->modelClass::find()
            ->where(['sku_id' => $sku_id, 'status' => StatusEnum::ENABLED, 'member_id' => $member_id])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->one();
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
            ->orderBy('status desc, updated_at desc')
            ->with(['product', 'sku', 'ladderPreferential'])
            ->asArray()
            ->all();

        foreach ($data as &$datum) {
            $datum['remark'] = '';
            if ($datum['status'] == StatusEnum::DISABLED) {
                if ($datum['product']['stock'] <= 0) {
                    $datum['remark'] = '库存不足';
                }

                if (!isset($datum['sku'])) {
                    $datum['remark'] = '宝贝已不能购买';
                }
            }
        }

        return $data;
    }

    /**
     * 获取总数量
     *
     * @param $member_id
     * @return int|string
     */
    public function count($member_id)
    {
        return $this->modelClass::find()
            ->select('count(id)')
            ->where(['member_id' => $member_id, 'status' => StatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->scalar();
    }

    /**
     * 加入购物车
     *
     * @param $sku
     * @param $num
     * @param $member_id
     * @return CartItem|array|mixed|\yii\db\ActiveRecord|null
     * @throws UnprocessableEntityHttpException
     */
    public function create($sku, $num, $member_id)
    {
        $model = $this->findProductModel($sku['product_id'], $sku['id'], $member_id);
        $model->number += $num;
        $model->sku_id = $sku['id'];
        $model->sku_name = $sku['name'];
        $model->member_id = $member_id;
        $model->price = $sku['price'];
        $model->product_id = $sku['product']['id'];
        $model->product_img = $sku['product']['picture'];
        $model->product_name = $sku['product']['name'];

        if ($sku['product']['is_virtual'] == StatusEnum::ENABLED) {
            throw new UnprocessableEntityHttpException('虚拟商品不可加入购物车');
        }

        if ($sku['product']['is_open_presell'] == StatusEnum::ENABLED) {
            throw new UnprocessableEntityHttpException('预售商品不可加入购物车');
        }

        if (PointExchangeTypeEnum::isIntegralBuy($sku['product']['point_exchange_type'])) {
            throw new UnprocessableEntityHttpException('积分商品不可加入购物车');
        }

        if ($sku['stock'] < $model->number) {
            throw new UnprocessableEntityHttpException('库存不足');
        }

        if ($sku['product']['status'] != StatusEnum::ENABLED || $sku['product']['product_status'] != StatusEnum::ENABLED) {
            throw new UnprocessableEntityHttpException('产品已下架或者不存在');
        }

        if ($sku['product']['max_buy'] > 0) {
            // 当前购物车所有的数量
            $sum = Yii::$app->tinyShopService->memberCartItem->getSumByProductId($sku['product']['id'], $member_id) + $num;

            if ($sum > $sku['product']['max_buy']) {
                throw new UnprocessableEntityHttpException('每人最多购买数量为' . $sku['product']['max_buy']);
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
     * @param $product_id
     * @param $sku_id
     * @param $num
     * @param $member_id
     * @return CartItem|array|mixed|null|\yii\db\ActiveRecord
     * @throws UnprocessableEntityHttpException
     */
    public function updateNum($sku, $num, $member_id)
    {
        $model = $this->findBySukId($sku['id'], $member_id);
        if (!$model) {
            throw new UnprocessableEntityHttpException('购物车产品已被移除');
        }

        if ($sku['product']['max_buy'] > 0) {
            // 当前购物车所有的数量
            $sum = Yii::$app->tinyShopService->memberCartItem->getSumByProductId($sku['product']['id'], $member_id);
            $sum = ($sum - $model->number) + $num;

            if ($sum > $sku['product']['max_buy']) {
                throw new UnprocessableEntityHttpException('每人最多购买数量为' . $sku['product']['max_buy']);
            }
        }

        $model->number = $num;
        if ($sku['stock'] < $model->number) {
            return ResultHelper::json(422, '库存不足');
        }

        if (!$model->save()) {
            return ResultHelper::json(422, $this->getError($model));
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
    public function deleteBySkuIds(array $sku_ids, $member_id)
    {
        $this->modelClass::deleteAll([
            'and',
            ['in', 'sku_id', $sku_ids],
            ['member_id' => $member_id]
        ]);

        return true;
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
     * 让购物车内的这些sku失效
     *
     * @param $skus
     */
    public function loseBySkus($skus)
    {
        $this->modelClass::updateAll(['status' => StatusEnum::DISABLED], [
            'and',
            ['in', 'sku_id', $skus],
            ['merchant_id' => Yii::$app->services->merchant->getId()]
        ]);
    }

    /**
     * 设置为禁用的
     *
     * @param $product_id
     */
    public function loseByProductIds(array $product_ids)
    {
        $this->modelClass::updateAll(['status' => StatusEnum::DISABLED], ['in', 'product_id', $product_ids]);

        // TODO 设置该产品订单为关闭
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
            ->scalar();
    }

    /**
     * @param $ids
     * @param $member_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findByIds($ids, $member_id)
    {
        return $this->modelClass::find()
            ->where(['in', 'id', $ids])
            ->andWhere(['status' => StatusEnum::ENABLED, 'member_id' => $member_id])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->with(['product.ladderPreferential', 'product.discountProduct', 'product.myGet' => function(ActiveQuery $query) use ($member_id) {
                return $query->andWhere(['member_id' => $member_id]);
            }, 'sku'])
            ->asArray()
            ->all();
    }

    /**
     * @param $product_id
     * @param $sku_id
     * @param $member_id
     * @return CartItem|array|\yii\db\ActiveRecord|null
     */
    protected function findProductModel($product_id, $sku_id, $member_id)
    {
        $model = $this->modelClass::find()
            ->where(['product_id' => $product_id, 'status' => StatusEnum::ENABLED, 'member_id' => $member_id])
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