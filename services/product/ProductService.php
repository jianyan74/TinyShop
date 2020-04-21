<?php

namespace addons\TinyShop\services\product;

use addons\TinyShop\common\enums\AccessTokenGroupEnum;
use addons\TinyShop\common\models\order\Order;
use common\helpers\EchantsHelper;
use Yii;
use yii\data\Pagination;
use yii\db\ActiveQuery;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;
use common\helpers\ArrayHelper;
use common\components\Service;
use common\enums\WhetherEnum;
use common\enums\StatusEnum;
use common\helpers\StringHelper;
use common\helpers\BcHelper;
use common\helpers\AddonHelper;
use addons\TinyShop\common\models\SettingForm;
use addons\TinyShop\common\models\product\Product;
use addons\TinyShop\common\models\forms\ProductSearch;
use addons\TinyShop\common\models\base\Spec;

/**
 * Class ProductService
 * @package addons\TinyShop\services\product
 * @author jianyan74 <751393839@qq.com>
 */
class ProductService extends Service
{
    /**
     * 获取猜你喜欢的产品
     *
     * @param $member_id
     * @return mixed|\yii\db\ActiveRecord
     */
    public function getGuessYouLike($member_id)
    {
        $cates = Yii::$app->tinyShopService->memberFootprint->findCateIdsByMemberId($member_id);

        $data = Product::find()
            ->where(['status' => StatusEnum::ENABLED, 'product_status' => StatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->andFilterWhere(['in', 'cate_id', $cates]);
        $pages = new Pagination(['totalCount' => $data->count(), 'pageSize' => 10, 'validatePage' => false]);
        $models = $data->offset($pages->offset)
            ->orderBy('rand()')
            ->select([
                'id',
                'name',
                'sketch',
                'keywords',
                'picture',
                'view',
                'star',
                'price',
                'market_price',
                'cost_price',
                'stock',
                'real_sales',
                'sales',
                'merchant_id',
                'is_open_presell',
                'is_open_commission',
                'point_exchange_type',
                'point_exchange',
                'max_use_point',
                'integral_give_type',
                'give_point',
            ])
            ->asArray()
            ->limit($pages->limit)
            ->all();

        foreach ($models as &$model) {
            $model['sales'] = $model['sales'] + $model['real_sales'];
            unset($model['real_sales']);
        }

        return $models;
    }

    /**
     * @param ProductSearch $search
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getListBySearch(ProductSearch $search)
    {
        $order = ArrayHelper::merge($search->getOrderBy(), ['sort asc', 'id desc']);

        // 所有下级分类
        $cate_ids = [];
        if ($cate_id = $search->cate_id) {
            $cate_ids = Yii::$app->tinyShopService->productCate->findChildIdsById($cate_id);
        }

        $data = Product::find()
            ->where(['status' => StatusEnum::ENABLED, 'product_status' => StatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->andFilterWhere(['like', 'name', $search->keyword])
            ->andFilterWhere(['is_hot' => $search->is_hot])
            ->andFilterWhere(['is_new' => $search->is_new])
            ->andFilterWhere(['is_recommend' => $search->is_recommend])
            ->andFilterWhere(['brand_id' => $search->brand_id])
            ->andFilterWhere(['in', 'cate_id', $cate_ids]);
        $pages = new Pagination([
            'totalCount' => $data->count(),
            'pageSize' => $search->page_size,
            'validatePage' => false,
        ]);
        $models = $data->offset($pages->offset)
            ->orderBy(implode(',', $order))
            ->select([
                'id',
                'name',
                'sketch',
                'keywords',
                'picture',
                'view',
                'match_point',
                'price',
                'market_price',
                'cost_price',
                'stock',
                'real_sales',
                'sales',
                'merchant_id',
                'is_open_presell',
                'is_open_commission',
                'point_exchange_type',
                'point_exchange',
                'max_use_point',
                'integral_give_type',
                'give_point',
            ])
            ->with('merchant')
            ->asArray()
            ->limit($pages->limit)
            ->all();

        $product_ids = [];
        foreach ($models as &$model) {
            $model['sales'] = $model['sales'] + $model['real_sales'];
            unset($model['real_sales']);

            // 开启分销
            if ($model['is_open_commission'] == true) {
                $product_ids[] = $model['id'];
            }

            $model['commissionRate'] = 0.00;
        }

        // 查询开启分销的产品
        $setting = new SettingForm();
        $setting->attributes = AddonHelper::getConfig();
        if (
            $setting->is_open_commission == StatusEnum::ENABLED &&
            !empty($product_ids) &&
            ($commissionRate = Yii::$app->tinyShopService->productCommissionRate->findByProductIds($product_ids))
        ) {
            $commissionRate = ArrayHelper::arrayKey($commissionRate, 'product_id');
            foreach ($models as &$model) {
                if (isset($commissionRate[$model['id']])) {
                    $distribution_commission_rate = $commissionRate[$model['id']]['distribution_commission_rate'] / 100;
                    $model['commissionRate'] = BcHelper::mul($model['price'], $distribution_commission_rate) ;
                }
            }
        }

        return $models;
    }

    /**
     * 评论数量改变
     *
     * @param $product_id
     * @param int $scores
     * @param int $num
     */
    public function commentNumChange($product_id, $scores = 5, $num = 1)
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
     * 绑定营销
     *
     * @param $product_ids
     * @param int $scores
     * @param int $num
     */
    public function bindingMarketing($product_ids, $marketing_id, $marketing_type)
    {
        Product::updateAll(['marketing_id' => $marketing_id, 'marketing_type' => $marketing_type], ['in', 'id', $product_ids]);
        // 触发购物车失效
        Yii::$app->tinyShopService->memberCartItem->loseByProductIds($product_ids);
    }

    /**
     * @param string $keyword
     * @return array
     */
    public function getList($keyword = '')
    {
        $data = Product::find()
            ->where(['status' => StatusEnum::ENABLED, 'product_status' => StatusEnum::ENABLED])
            ->andFilterWhere(['like', 'name', $keyword])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()]);
        $pages = new Pagination(['totalCount' => $data->count(), 'pageSize' => 10]);
        $models = $data->offset($pages->offset)
            ->orderBy('sort asc, id desc')
            ->with(['minPriceSku'])
            ->select(['id', 'name', 'sketch', 'keywords', 'picture', 'view', 'star', 'real_sales', 'sales'])
            ->asArray()
            ->limit($pages->limit)
            ->all();

        foreach ($models as &$model) {
            $model['covers'] = unserialize($model['covers']);
        }

        return [$models, $pages];
    }

    /**
     * @param $id
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findById($id)
    {
        return Product::find()
            ->where(['id' => $id, 'status' => StatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->one();
    }

    /**
     * @param $id
     * @param $member_id
     * @return array|\yii\db\ActiveRecord|null
     * @throws NotFoundHttpException
     */
    public function findViewById($id, $member_id)
    {
        $model = Product::find()
            ->where(['id' => $id, 'product_status' => StatusEnum::ENABLED])
            ->andWhere(['>=', 'status', StatusEnum::DISABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->with(['sku', 'attributeValue', 'ladderPreferential', 'merchant'])
            ->with([
                'myCollect' => function (ActiveQuery $query) use ($member_id) {
                    return $query->andWhere(['member_id' => $member_id]);
                },
                'evaluate',
                'evaluateStat',
            ])
            ->asArray()
            ->one();

        if (!$model) {
            throw new NotFoundHttpException('找不到该产品');
        }

        $model['base_attribute_format'] = Json::decode($model['base_attribute_format']);
        $model['covers'] = unserialize($model['covers']);

        // 提取最小sku和最大sku
        $skuPrices = array_column($model['sku'], 'price');
        asort($skuPrices);
        $model['minSkuPrice'] = array_shift($skuPrices);
        $model['maxSkuPrice'] = end($skuPrices);

        return $model;
    }

    /**
     * 获取属性值、规格属性、规格值
     *
     * @param Product $model
     * @return array
     */
    public function getSpecValueAttribute(Product $model)
    {
        $attributeValue = $specValue = $jsData = [];
        if (!$model->id || $model->is_attribute != WhetherEnum::ENABLED) {
            return [$attributeValue, $specValue, $jsData];
        }

        // 获取基础属性
        $baseAttribute = Yii::$app->tinyShopService->baseAttribute->getDataById($model->base_attribute_id);

        // 获取产品属性值
        $attributeValue = $this->getAttributeValue($model, $baseAttribute);
        // 获取规格(规格值)和js选中规格
        list($specValue, $jsData) = $this->getSpecValue($model, $baseAttribute['spec_ids']);

        unset($baseAttribute, $model);

        return [$attributeValue, $specValue, $jsData];
    }

    /**
     * 获取产品规格
     *
     * @param $model
     * @param $spec_ids
     * @return array
     */
    protected function getSpecValue($model, $spec_ids)
    {
        $tmpSpecValue = [];
        $jsData = [];
        $spec_ids = explode(',', $spec_ids);
        $baseSpecValue = Yii::$app->tinyShopService->baseSpec->getListWithValueByIds($spec_ids);
        /* @var $model Product 获取已选择的规格属性 */
        if (!empty($specValue = $model->getSpecWithSpecValue($model->id)->all())) {
            foreach ($specValue as &$item) {
                $item['id'] = $item['base_spec_id'];
                foreach ($item['value'] as &$value) {
                    $value['id'] = $value['base_spec_value_id'];
                    $jsData[] = [
                        'id' => $value['base_spec_value_id'],
                        'title' => $value['title'],
                        'pid' => $item['base_spec_id'],
                        'ptitle' => $item['title'],
                        'sort' => $value['sort'],
                        'data' => $value['data'],
                    ];

                    // 加入临时规格值数据方便调用
                    $tmpSpecValue[$value['id']] = $value;
                }
            }

            // 判断模型是否被删除如果被删除则直接替换
            empty($baseSpecValue) && $baseSpecValue = $specValue;
        }

        // 重新赋值已有数据并判断颜色是否正常
        foreach ($baseSpecValue as &$item) {
            foreach ($item['value'] as &$value) {
                $value['data'] = $tmpSpecValue[$value['id']]['data'] ?? '';

                if (substr($value['data'], 0, 1) == "#") {
                    $value['data'] = StringHelper::clipping($value['data'], '#', 1);
                } else {
                    $item['show_type'] == Spec::SHOW_TYPE_COLOR && $value['data'] = '';
                }
            }
        }

        unset($tmpSpecValue, $model);

        return [$baseSpecValue, $jsData];
    }

    /**
     * 返回产品编辑的属性
     *
     * @param $model
     * @return array
     */
    protected function getAttributeValue($model, $baseAttribute)
    {
        $attributeValue = [];
        if (empty($baseAttribute['value'])) {
            return $attributeValue;
        }

        // 获取商品类型自带属性
        foreach ($baseAttribute['value'] as $value) {
            // 调整属性显示
            $baseValue = !empty($value['value']) ? explode(',', $value['value']) : [];
            $config = [];
            foreach ($baseValue as $item) {
                $config[$item] = $item;
            }

            $attributeValue[$value['id']] = [
                'id' => $value['id'],
                'title' => $value['title'],
                'type' => $value['type'],
                'value' => '',
                'sort' => $value['sort'],
                'config' => $config,
            ];
        }

        // 获取已有的属性数据
        if (!empty($attributeValueModel = $model->attributeValue)) {
            foreach ($attributeValueModel as $item) {
                if (isset($attributeValue[$item['base_attribute_value_id']])) {
                    $attributeValue[$item['base_attribute_value_id']]['value'] = $item['value'];
                }
            }
        }

        unset($model, $baseAttribute);

        return ArrayHelper::arraySort($attributeValue, 'sort');
    }

    /**
     * 获取销量排名
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getRank($limit = 8)
    {
        return Product::find()
            ->where(['status' => StatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->orderBy('real_sales desc')
            ->limit($limit)
            ->asArray()
            ->all();
    }

    /**
     * 获取商品构成类型
     *
     * @return array
     */
    public function getGroupVirtual()
    {
        $fields = [
            '虚拟物品', '实物'
        ];

        // 获取时间和格式化
        list($time, $format) = EchantsHelper::getFormatTime('all');
        // 获取数据
        return EchantsHelper::pie(function ($start_time, $end_time) use ($fields) {
            $data = Product::find()
                ->select(['count(id) as value', 'is_virtual'])
                ->where(['status' => StatusEnum::ENABLED])
                ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
                ->groupBy(['is_virtual'])
                ->asArray()
                ->all();

            foreach ($data as &$datum) {
                if ($datum['is_virtual'] == StatusEnum::ENABLED) {
                    $datum['name'] = '虚拟物品';
                } else {
                    $datum['name'] = '实物';
                }

                unset($datum['is_virtual']);
            }

            return [$data, $fields];
        }, $time);
    }

    /**
     * 获取产品发布/出售/仓库的数量
     *
     * @return array
     */
    public function getCountStat()
    {
        $stat = [
            'sellCount' => 0,
            'warehouseCount' => 0,
            'allCount' => 0,
        ];

        $model = Product::find()
            ->select(['count(id) as count', 'product_status'])
            ->where(['status' => StatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->groupBy('product_status')
            ->asArray()
            ->all();

        $model = ArrayHelper::arrayKey($model, 'product_status');
        $stat['sellCount'] = isset($model[Product::PRODUCT_STATUS_PUTAWAY]) ? $model[Product::PRODUCT_STATUS_PUTAWAY]['count'] : 0;
        $stat['warehouseCount'] = isset($model[Product::PRODUCT_STATUS_SOLD_OUT]) ? $model[Product::PRODUCT_STATUS_SOLD_OUT]['count'] : 0;
        $stat['allCount'] = $stat['sellCount'] + $stat['warehouseCount'];

        return $stat;
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
            ->andWhere(['status' => StatusEnum::ENABLED, 'product_status' => StatusEnum::ENABLED])
            ->andWhere('warning_stock > stock')
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->scalar();
    }

    /**
     * @param $ids
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findByIds($ids)
    {
        return Product::find()
            ->where(['status' => StatusEnum::ENABLED, 'product_status' => StatusEnum::ENABLED])
            ->andWhere(['in', 'id', $ids])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->orderBy('id asc')
            ->all();
    }
}