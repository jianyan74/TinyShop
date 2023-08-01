<?php

namespace addons\TinyShop\services\product;

use Yii;
use yii\data\Pagination;
use yii\db\ActiveQuery;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;
use common\components\Service;
use common\enums\AuditStatusEnum;
use common\enums\StatusEnum;
use common\helpers\EchantsHelper;
use common\helpers\ArrayHelper;
use addons\TinyShop\common\models\product\Product;
use addons\TinyShop\common\forms\ProductSearchForm;
use addons\TinyShop\common\enums\ProductTypeEnum;
use yii\web\UnprocessableEntityHttpException;

/**
 * Class ProductService
 * @package addons\TinyShop\services\product
 */
class ProductService extends Service
{
    /**
     * @param ProductSearchForm $search
     * @param false $returnPageData
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getListBySearch(ProductSearchForm $search, $returnPageData = false)
    {
        // 记录搜索
        !empty($search->keyword) && Yii::$app->tinyShopService->searchHistory->create($search->keyword, $search->member_id);
        $order = ArrayHelper::merge($search->getOrderBy(), ['sort asc', 'id desc']);

        $data = Product::find()
            ->where(['status' => StatusEnum::ENABLED, 'audit_status' => StatusEnum::ENABLED, 'is_list_visible' => StatusEnum::ENABLED])
            ->andFilterWhere(['in', 'id', $search->getIds()])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->andFilterWhere(['merchant_id' => $search->merchant_id])
            ->andFilterWhere($search->getCateIds())
            ->andFilterWhere(['between', 'price', $search->min_price, $search->max_price])
            ->andFilterWhere(['is_member_discount' => $search->is_member_discount])
            ->andFilterWhere(['is_commission' => $search->is_commission])
            ->andFilterWhere(['in', 'brand_id', $search->getBrandIds()])
            ->andFilterWhere($search->getOrCondition())
            ->andFilterWhere($search->getGather())
            ->andFilterWhere(['like', 'name', trim($search->keyword)]);

        $pages = new Pagination([
            'totalCount' => $data->count(),
            'pageSize' => $search->limit,
            'validatePage' => false,
        ]);

        $models = $data->offset($pages->offset)
            ->orderBy(implode(',', $order))
            ->cache(Yii::$app->params['cacheExpirationTime']['common'])
            ->select([
                'id',
                'name',
                'sketch',
                'keywords',
                'picture',
                'tags',
                'view',
                'type',
                'match_point',
                'price',
                'market_price',
                'stock',
                'total_sales',
                'merchant_id',
                'shipping_type',
                'is_member_discount',
                'member_discount_config',
                'is_commission',
                'is_hot',
                'is_recommend',
                'is_new',
                'member_discount_type',
                'max_use_point',
                'give_point',
                'match_ratio',
                'unit',
            ])
            ->with($search->getWith())
            ->asArray()
            ->limit($pages->limit)
            ->all();

        foreach ($models as &$model) {
            $model['tags'] = Json::decode($model['tags']);
            $model['price'] = floatval($model['price']);
            $model['market_price'] = floatval($model['market_price']);
            // 营销
            $model['marketing_id'] = '';
            $model['marketing_type'] = '';
            $model['marketing_tags'] = [];

            // 营销标签
            $model['is_hot'] == StatusEnum::ENABLED && $model['marketing_tags'][] = '热门';
            $model['is_recommend'] == StatusEnum::ENABLED && $model['marketing_tags'][] = '推荐';
            $model['is_new'] == StatusEnum::ENABLED && $model['marketing_tags'][] = '新品';

            unset($model['sku']);
        }

        // 返回分页数量
        if ($returnPageData) {
            return [$models, $pages];
        }

        return $models;
    }

    /**
     * @param $id
     * @param $member_id
     * @return array|\yii\db\ActiveRecord|null
     * @throws NotFoundHttpException
     */
    public function findViewById($id, $member_id, $with = ['sku', 'cateMap', 'attributeValue', 'evaluate', 'evaluateStat'])
    {
        $model = Product::find()
            ->where(['id' => $id])
            ->andWhere(['>=', 'status', StatusEnum::DISABLED])
            ->with($with)
            ->with([
                'myCollect' => function (ActiveQuery $query) use ($member_id) {
                    return $query->andWhere(['member_id' => $member_id]);
                }
            ])
            ->cache(10)
            ->asArray()
            ->one();

        if (!$model) {
            throw new NotFoundHttpException('找不到该商品');
        }

        $model['spec_format'] = Json::decode($model['spec_format']);
        $model['covers'] = Json::decode($model['covers']);
        $model['tags'] = Json::decode($model['tags']);
        $model['delivery_type'] = Json::decode($model['delivery_type']);

        // 商户
        $model['merchant'] = [];
        $model['merchant_id'] > 0 && $model['merchant'] = Yii::$app->tinyShopService->merchant->findOneWithGrade($model['merchant_id']);

        // 提取最小sku和最大sku
        $skuPrices = array_column($model['sku'], 'price');
        asort($skuPrices);
        $model['minSkuPrice'] = array_shift($skuPrices);
        $model['maxSkuPrice'] = end($skuPrices);

        if (!empty($model['cateMap'])) {
            $model['cateIds'] = ArrayHelper::getColumn($model['cateMap'], 'cate_id');
            unset($model['cateMap']);
        }

        return $model;
    }

    public function copy($product)
    {
        // 复制商品
        $model = new Product();
        $model = $model->loadDefaultValues();
        $model->attributes = ArrayHelper::toArray($product);
        $model->sales = $model->real_sales = $model->collect_num = $model->transmit_num = $model->comment_num = 0;
        $model->name = $model->name . ' - 复制';
        $model->star = 5;
        $model->status = StatusEnum::DISABLED;
        if (!$model->save()) {
            throw new UnprocessableEntityHttpException($this->getError($model));
        }

        $product_id = $model->id;
        // 复制 sku
        if ($sku = Yii::$app->tinyShopService->productSku->findByProductId($product->id)) {
            Yii::$app->tinyShopService->productSku->createByCopy($product_id, $sku);
        }

        // 复制 规格
        if ($spec = Yii::$app->tinyShopService->productSpec->findByProductId($product->id)) {
            Yii::$app->tinyShopService->productSpec->createByCopy($product_id, $spec);
        }

        // 复制 规格值
        if ($specValue = Yii::$app->tinyShopService->productSpecValue->findByProductId($product->id)) {
            Yii::$app->tinyShopService->productSpecValue->createByCopy($product_id, $specValue);
        }

        // 属性
        if ($attributeValue = Yii::$app->tinyShopService->productAttributeValue->findByProductId($product->id)) {
            Yii::$app->tinyShopService->productAttributeValue->createByCopy($product_id, $attributeValue);
        }

        // 复制 分类映射
        if ($cateMap = Yii::$app->tinyShopService->productCateMap->findByProductId($product->id)) {
            Yii::$app->tinyShopService->productCateMap->create($product_id, $cateMap);
        }
    }

    /**
     * 获取商品构成类型
     *
     * @return array
     */
    public function getProductTypeStat()
    {
        $fields = ProductTypeEnum::getMap();

        // 获取时间和格式化
        list($time, $format) = EchantsHelper::getFormatTime('all');
        // 获取数据
        return EchantsHelper::pie(function ($start_time, $end_time) use ($fields) {
            $data = Product::find()
                ->select(['count(id) as value', 'type'])
                ->where(['status' => StatusEnum::ENABLED])
                ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
                ->groupBy(['type'])
                ->asArray()
                ->all();

            foreach ($data as &$datum) {
                $datum['name'] = ProductTypeEnum::getValue($datum['type']);
            }

            return [$data, $fields];
        }, $time);
    }

    /**
     * 评论数量改变
     *
     * @param $product_id
     * @param int $scores
     * @param int $num
     */
    public function updateCommentNum($product_id, $scores = 5, $num = 1)
    {
        $product = $this->findById($product_id);
        $product->comment_num += $num;

        $star = $product->star + $scores;
        $match_point = $star / ($product->comment_num + 1);
        $match_ratio = ($match_point / 5) * 100;

        $product->star = $star;
        $product->match_point = $match_point;
        $product->match_ratio = $match_ratio;
        $product->save();
    }

    /**
     * 审核数量
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getAuditStatusCount()
    {
        return ArrayHelper::map(Product::find()
            ->select(['count(id) as count', 'audit_status'])
            ->where(['status' => StatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->groupBy('audit_status')
            ->asArray()
            ->all(), 'audit_status', 'count');
    }

    /**
     * 获取销量排名
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getRank($limit = 8)
    {
        return Product::find()
            ->select(['id', 'real_sales', 'name'])
            ->where(['status' => StatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->orderBy('real_sales desc')
            ->limit($limit)
            ->asArray()
            ->all();
    }

    /**
     * @return false|int|string|null
     */
    public function findWarehouseCount()
    {
        return Product::find()
            ->select(['count(id)'])
            ->where(['status' => StatusEnum::DISABLED, 'audit_status' => AuditStatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->scalar() ?? 0;
    }

    /**
     * @return false|int|string|null
     */
    public function findSellCount($merchant_id = '')
    {
        $merchant_id === '' && $merchant_id = $this->getMerchantId();

        return Product::find()
            ->select(['count(id)'])
            ->where(['status' => StatusEnum::ENABLED, 'audit_status' => AuditStatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $merchant_id])
            ->scalar() ?? 0;
    }

    /**
     * 获取库存不足数量
     *
     * @return false|string|null
     */
    public function getWarningStockCount()
    {
        return Product::find()
            ->select(['count(id) as count'])
            ->andWhere(['status' => StatusEnum::ENABLED, 'audit_status' => AuditStatusEnum::ENABLED])
            ->andWhere('stock_warning_num > stock')
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->scalar() ?? 0;
    }

    /**
     * @param $id
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findById($id)
    {
        return Product::find()
            ->where(['id' => $id, 'status' => StatusEnum::ENABLED])
            ->one();
    }

    /**
     * @param $ids
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findByIds($ids, $select = ['*'])
    {
        return Product::find()
            ->select($select)
            ->where(['status' => StatusEnum::ENABLED, 'audit_status' => AuditStatusEnum::ENABLED])
            ->andWhere(['in', 'id', $ids])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->orderBy('id asc')
            ->all();
    }
}
