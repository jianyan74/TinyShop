<?php

namespace addons\TinyShop\common\models\product;

use Yii;
use yii\db\ActiveQuery;
use common\behaviors\MerchantBehavior;
use common\enums\StatusEnum;
use common\models\base\BaseModel;
use common\traits\HasOneMerchant;
use common\helpers\StringHelper;
use addons\TinyShop\common\enums\CommonModelMapEnum;
use addons\TinyShop\common\enums\OrderStatusEnum;
use addons\TinyShop\common\models\common\Collect;
use addons\TinyShop\common\models\order\OrderProduct;
use addons\TinyShop\common\enums\MarketingEnum;
use addons\TinyShop\common\models\marketing\MarketingProduct;
use addons\TinyShop\common\models\common\Supplier;
use addons\TinyShop\common\models\marketing\MarketingProductSku;
use addons\TinyShop\common\models\repertory\Stock;

/**
 * This is the model class for table "{{%addon_tiny_shop_product}}".
 *
 * @property int $id
 * @property int|null $merchant_id 商家编号
 * @property string|null $name 商品标题
 * @property string|null $picture 商品主图
 * @property int|null $cate_id 商品分类编号
 * @property int|null $brand_id 品牌编号
 * @property int|null $type 商品类型
 * @property string|null $sketch 简述
 * @property string|null $intro 商品描述
 * @property string|null $keywords 商品关键字
 * @property string|null $tags 标签
 * @property string|null $sku_no 商品编码
 * @property string|null $barcode 商品条码
 * @property int|null $sales 虚拟购买量
 * @property int|null $real_sales 实际销量
 * @property int|null $total_sales 总销量
 * @property float|null $price 商品价格
 * @property float|null $market_price 市场价格
 * @property float|null $cost_price 成本价
 * @property int|null $stock 库存量
 * @property int|null $stock_warning_num 库存警告数量
 * @property int|null $stock_deduction_type 库存扣减类型
 * @property string|null $covers 幻灯片
 * @property string|null $extend 扩展
 * @property string|null $video_url 展示视频
 * @property int|null $sort 排序
 * @property array|null $delivery_type 配送方式
 * @property int|null $shipping_type 运费类型 1免邮2买家付邮费3固定运费
 * @property float|null $shipping_fee 运费
 * @property int|null $shipping_fee_id 物流模板id
 * @property int|null $shipping_fee_type 计价方式1.计件2.体积3.重量
 * @property float|null $weight 商品重量
 * @property float|null $volume 商品体积
 * @property int|null $marketing_id 促销活动ID
 * @property string|null $marketing_type 促销类型
 * @property float|null $marketing_price 商品促销价格
 * @property int|null $point_exchange_type 积分兑换类型
 * @property int|null $point_exchange 积分兑换
 * @property int|null $point_give_type 积分赠送类型 0固定值 1按比率
 * @property int|null $give_point 购买商品赠送积分
 * @property int|null $max_use_point 积分抵现最大可用积分数 0为不可使用
 * @property int|null $min_buy 最少买几件
 * @property int|null $max_buy 限购 0 不限购
 * @property int|null $order_max_buy 单笔订单限购 0 不限购
 * @property int|null $view 商品点击数量
 * @property int|null $star 好评星级
 * @property int|null $collect_num 收藏数量
 * @property int|null $comment_num 评价数
 * @property int|null $transmit_num 分享数
 * @property int|null $province_id 所在省
 * @property int|null $city_id 所在市
 * @property int|null $area_id 所在区
 * @property string|null $address_name 地址
 * @property int|null $attribute_id 参数
 * @property int|null $is_spec 启用商品规格
 * @property int|null $is_stock_visible 库存显示 0不显示1显示
 * @property int|null $is_sales_visible 销量显示 0不显示1显示
 * @property int|null $is_hot 是否热销商品
 * @property int|null $is_recommend 是否推荐
 * @property int|null $is_new 是否新品
 * @property int|null $is_bill 是否开具增值税发票 1是，0否
 * @property string|null $spec_format 商品规格
 * @property float|null $match_point 实物与描述相符（根据评价计算）
 * @property float|null $match_ratio 实物与描述相符（根据评价计算）百分比
 * @property int|null $production_date 生产日期
 * @property int|null $shelf_life 保质期
 * @property int|null $growth_give_type 成长值赠送类型 0固定值 1按比率
 * @property int|null $give_growth 购买商品赠送成长值
 * @property string|null $unit 商品单位
 * @property int|null $supplier_id 供货商id
 * @property int|null $spec_template_id 规格模板
 * @property int|null $is_commission 是否支持分销
 * @property int|null $is_member_discount 是否支持会员折扣
 * @property int|null $member_discount_type 折扣类型 1:系统;2:自定义
 * @property int|null $active_blacklist 活动黑名单
 * @property int|null $is_list_visible 列表可见
 * @property int|null $start_time 上架时间
 * @property int|null $end_time 下架时间
 * @property string|null $refusal_cause 拒绝原因
 * @property int $audit_status 审核状态[0:申请;1通过;-1失败]
 * @property int|null $audit_time 审核时间
 * @property int|null $status 状态
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class Product extends BaseModel
{
    use MerchantBehavior, HasOneMerchant;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_product}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'name',
                    'cate_id',
                    'price',
                    'market_price',
                    'cost_price',
                    'shipping_fee',
                    'intro',
                    'covers',
                    'stock',
                    'stock_warning_num',
                    'weight',
                    'volume',
                    'min_buy',
                    'max_buy',
                    'give_point',
                    'give_growth',
                    'order_max_buy',
                ],
                'required',
            ],
            [['delivery_type',], 'required', 'on' => 'entity'],
            [
                [
                    'merchant_id',
                    'cate_id',
                    'brand_id',
                    'type',
                    'sales',
                    'real_sales',
                    'total_sales',
                    'stock',
                    'stock_warning_num',
                    'stock_deduction_type',
                    'sort',
                    'shipping_type',
                    'shipping_fee_id',
                    'shipping_fee_type',
                    'marketing_id',
                    'point_exchange_type',
                    'point_exchange',
                    'point_give_type',
                    'give_point',
                    'max_use_point',
                    'min_buy',
                    'max_buy',
                    'order_max_buy',
                    'view',
                    'star',
                    'collect_num',
                    'comment_num',
                    'transmit_num',
                    'province_id',
                    'city_id',
                    'area_id',
                    'is_spec',
                    'is_stock_visible',
                    'is_sales_visible',
                    'is_hot',
                    'is_recommend',
                    'is_new',
                    'is_bill',
                    'shelf_life',
                    'growth_give_type',
                    'give_growth',
                    'supplier_id',
                    'spec_template_id',
                    'is_commission',
                    'is_member_discount',
                    'member_discount_type',
                    'active_blacklist',
                    'is_list_visible',
                    'audit_status',
                    'audit_time',
                    'created_at',
                    'updated_at',
                ],
                'integer',
                'min' => 0,
            ],
            [['intro'], 'string'],
            [
                [
                    'price',
                    'market_price',
                    'cost_price',
                    'shipping_fee',
                    'weight',
                    'volume',
                    'marketing_price',
                    'match_point',
                    'match_ratio',
                ],
                'number',
                'min' => 0,
                'max' => 9999999,
            ],
            [
                [
                    'covers',
                    'extend',
                    'spec_format',
                    'delivery_type',
                    'production_date',
                    'start_time',
                    'end_time',
                ],
                'safe',
            ],
            [['name', 'picture'], 'string', 'max' => 255],
            [['sketch', 'keywords', 'video_url', 'address_name', 'refusal_cause'], 'string', 'max' => 200],
            [['sku_no', 'barcode'], 'string', 'max' => 100],
            [['marketing_type', 'unit'], 'string', 'max' => 50],
            [['min_buy'], 'integer', 'min' => 1],
            [['attribute_id', 'status'], 'integer'],
            [['tags'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'merchant_id' => '商家编号',
            'name' => '商品名称',
            'picture' => '商品主图',
            'cate_id' => '商品分类',
            'brand_id' => '品牌',
            'type' => '商品类型',
            'sketch' => '商品卖点',
            'intro' => '商品描述',
            'keywords' => '商品关键字',
            'tags' => '标签',
            'sku_no' => '商品编码',
            'barcode' => '商品条码',
            'sales' => '虚拟购买量',
            'real_sales' => '实际销量',
            'total_sales' => '总销量',
            'price' => '商品价格',
            'market_price' => '划线价',
            'cost_price' => '成本价',
            'stock' => '库存',
            'stock_warning_num' => '库存预警',
            'stock_deduction_type' => '库存扣减类型',
            'covers' => '幻灯片',
            'extend' => '扩展',
            'video_url' => '展示视频',
            'sort' => '排序',
            'delivery_type' => '配送方式',
            'shipping_type' => '运费类型',
            'shipping_fee' => '运费',
            'shipping_fee_id' => '物流模板',
            'shipping_fee_type' => '计价方式', // 1.计件2.体积3.重量
            'weight' => '商品重量',
            'volume' => '商品体积',
            'marketing_id' => '促销活动ID',
            'marketing_type' => '促销类型',
            'marketing_price' => '商品促销价格',
            'point_exchange_type' => '积分兑换类型',
            'point_exchange' => '积分兑换',
            'point_give_type' => '积分赠送类型', //  0固定值 1按比率
            'give_point' => '购买单件商品赠送积分',
            'max_use_point' => '积分抵现最大可用积分数', // 0为不可使用
            'min_buy' => '最少买几件',
            'max_buy' => '总限购', //  0 不限购
            'order_max_buy' => '单笔下单限购', //  0 不限购
            'view' => '商品点击数量',
            'star' => '好评星级',
            'collect_num' => '收藏数量',
            'comment_num' => '评价数',
            'transmit_num' => '分享数',
            'province_id' => '所在省',
            'city_id' => '所在市',
            'area_id' => '所在区',
            'address_name' => '地址',
            'attribute_id' => '商品参数模板',
            'is_spec' => '商品规格',
            'is_stock_visible' => '库存显示', //  0 不显示1显示
            'is_sales_visible' => '销量显示', //  0 不显示1显示
            'is_hot' => '热销',
            'is_recommend' => '推荐',
            'is_new' => '新品',
            'is_bill' => '是否开具增值税发票', //  1是，0否
            'spec_format' => '商品规格',
            'match_point' => '实物与描述相符（根据评价计算）',
            'match_ratio' => '实物与描述相符（根据评价计算）百分比',
            'production_date' => '生产日期',
            'shelf_life' => '保质期',
            'growth_give_type' => '成长值赠送类型', //  0固定值 1按比率
            'give_growth' => '购买单件商品赠送成长值',
            'unit' => '商品单位',
            'spec_template_id' => '规格模板',
            'supplier_id' => '供货商',
            'is_commission' => '分销设置',
            'is_member_discount' => '会员折扣',
            'member_discount_type' => '折扣类型', //  1:系统;2:自定义
            'active_blacklist' => '活动黑名单',
            'is_list_visible' => '搜索/列表可见',
            'start_time' => '定时上架时间',
            'end_time' => '下架时间',
            'refusal_cause' => '拒绝原因',
            'audit_status' => '审核状态',
            'audit_time' => '审核时间',
            'status' => '商品状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * 关联规格
     *
     * @return ActiveQuery
     */
    public function getSpec()
    {
        return $this->hasMany(Spec::class, ['product_id' => 'id']);
    }

    /**
     * 关联规格值
     *
     * @return ActiveQuery
     */
    public function getSpecValue()
    {
        return $this->hasMany(SpecValue::class, ['product_id' => 'id']);
    }

    /**
     * 关联sku
     *
     * @return ActiveQuery
     */
    public function getSku()
    {
        return $this->hasMany(Sku::class, ['product_id' => 'id']);
    }

    /**
     * 关联第一个sku
     *
     * @return ActiveQuery
     */
    public function getFirstSku()
    {
        return $this->hasOne(Sku::class, ['product_id' => 'id'])
            ->orderBy('id asc');
    }

    /**
     * 关最小sku
     *
     * @return ActiveQuery
     */
    public function getMinPriceSku()
    {
        return $this->hasOne(Sku::class, ['product_id' => 'id'])->orderBy('price');
    }

    /**
     * 分销配置
     *
     * @return ActiveQuery
     */
    public function getCommissionRate()
    {
        return $this->hasOne(CommissionRate::class, ['product_id' => 'id']);
    }

    /**
     * 会员折扣
     *
     * @return ActiveQuery
     */
    public function getMemberDiscount()
    {
        return $this->hasOne(MemberDiscount::class, ['product_id' => 'id']);
    }

    /**
     * 关联当前营销
     *
     * @return ActiveQuery
     */
    public function getMarketingProduct()
    {
        return $this->hasOne(MarketingProduct::class, ['product_id' => 'id'])
            ->where(['status' => StatusEnum::ENABLED])
            ->andWhere([
                'in',
                'marketing_type',
                [
                    MarketingEnum::DISCOUNT,
                    MarketingEnum::SECOND_HALF_DISCOUNT,
                    MarketingEnum::BALE,
                    MarketingEnum::MEMBER_DISCOUNT,
                ],
            ])
            ->andWhere(['<', 'start_time', time()])
            ->andWhere(['>', 'end_time', time()])
            ->orderBy('id desc');
    }

    /**
     * 关联分类
     *
     * @return ActiveQuery
     */
    public function getCate()
    {
        return $this->hasOne(Cate::class, ['id' => 'cate_id'])
            ->select(['id', 'title']);
    }

    /**
     * 关联分类
     *
     * @return ActiveQuery
     */
    public function getCateMap()
    {
        return $this->hasMany(CateMap::class, ['product_id' => 'id']);
    }

    /**
     * 关联品牌
     *
     * @return ActiveQuery
     */
    public function getBrand()
    {
        return $this->hasOne(Brand::class, ['id' => 'brand_id'])->select(['id', 'title']);
    }

    /**
     * 关联供应商
     *
     * @return ActiveQuery
     */
    public function getSupplier()
    {
        return $this->hasOne(Supplier::class, ['id' => 'supplier_id']);
    }

    /**
     * 关联仓库库存
     *
     * @return ActiveQuery
     */
    public function getRepertoryStock()
    {
        return $this->hasMany(Stock::class, ['product_id' => 'id']);
    }

    /**
     * 关联仓库库存
     *
     * @return ActiveQuery
     */
    public function getFirstRepertoryStock()
    {
        return $this->hasOne(Stock::class, ['product_id' => 'id'])->andWhere(['sku_id' => 0]);
    }

    /**
     * 营销
     *
     * @return ActiveQuery
     */
    public function getMarketing()
    {
        return $this->hasOne(MarketingProductSku::class, ['product_id' => 'id'])
            ->andWhere(['<', 'prediction_time', time()])
            ->andWhere(['>', 'end_time', time()])
            ->andWhere(['is_min_price' => StatusEnum::ENABLED, 'is_template' => 0])
            ->andWhere(['status' => StatusEnum::ENABLED])
            ->andWhere(['in', 'marketing_type', array_keys(MarketingEnum::getBackendSearchMap())]);
    }

    /**
     * 关联评价标签
     *
     * @return ActiveQuery
     */
    public function getEvaluateStat()
    {
        return $this->hasOne(EvaluateStat::class, ['product_id' => 'id']);
    }

    /**
     * 关联评价
     *
     * @return ActiveQuery
     */
    public function getEvaluate()
    {
        return $this->hasMany(Evaluate::class, ['product_id' => 'id'])
            ->limit(10)
            ->andWhere(['status' => StatusEnum::ENABLED])
            ->orderBy('id desc')
            ->asArray();
    }

    /**
     * 关联第一条评价
     *
     * @return ActiveQuery
     */
    public function getFirstEvaluate()
    {
        return $this->hasOne(Evaluate::class, ['product_id' => 'id'])
            ->select([
                'id',
                'product_id',
                'sku_name',
                'content',
                'member_id',
                'member_nickname',
                'member_head_portrait',
                'is_anonymous',
                'scores',
                'explain_type',
                'created_at',
            ])
            ->andWhere([
                'status' => StatusEnum::ENABLED,
                'is_auto' => StatusEnum::DISABLED,
                'scores' => 5
            ])
            ->orderBy('id desc');
    }

    /**
     * 关联参数
     *
     * @return ActiveQuery
     */
    public function getAttributeValue()
    {
        return $this->hasMany(AttributeValue::class, ['product_id' => 'id'])
            ->select(['product_id', 'title', 'data'])
            ->andWhere(['status' => StatusEnum::ENABLED])
            ->asArray();
    }

    /**
     * 阶梯优惠
     *
     * @return ActiveQuery
     */
    public function getLadderPreferential()
    {
        return $this->hasMany(LadderPreferential::class, ['product_id' => 'id'])->orderBy('quantity desc, id asc')->asArray();
    }

    /**
     * 我的收藏
     *
     * @return ActiveQuery
     */
    public function getMyCollect()
    {
        return $this->hasOne(Collect::class, ['topic_id' => 'id'])
            ->where(['topic_type' => CommonModelMapEnum::PRODUCT])
            ->andWhere(['status' => StatusEnum::ENABLED]);
    }

    /**
     * 我的购买总数量
     *
     * @return ActiveQuery
     */
    public function getMyGet()
    {
        return $this->hasOne(OrderProduct::class, ['product_id' => 'id'])
            ->select(['sum(num) as all_num', 'product_id'])
            ->where(['status' => StatusEnum::ENABLED])
            ->andWhere(['in', 'order_status', OrderStatusEnum::haveBought()])
            ->groupBy('product_id');
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        $this->production_date = StringHelper::dateToInt($this->production_date);
        $this->start_time = StringHelper::dateToInt($this->start_time);
        $this->end_time = StringHelper::dateToInt($this->end_time);
        empty($this->supplier_id) && $this->supplier_id = 0;
        empty($this->brand_id) && $this->brand_id = 0;
        empty($this->province_id) && $this->province_id = 0;
        empty($this->city_id) && $this->city_id = 0;
        empty($this->area_id) && $this->area_id = 0;

        // 让sku失效
        if (in_array($this->status, [StatusEnum::DELETE, StatusEnum::DISABLED]) || $this->audit_status == StatusEnum::DISABLED) {
            Yii::$app->tinyShopService->memberCartItem->loseByProductIds([$this->id]);
            // 下架产品
            // Yii::$app->tinyShopService->marketing->unShelveProduct([$this->id]);
        }

        return parent::beforeSave($insert);
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        // 增加关联的评价统计
        if ($insert) {
            $stat = new EvaluateStat();
            $stat = $stat->loadDefaultValues();
            $stat->product_id = $this->id;
            $stat->save();
        }

        parent::afterSave($insert, $changedAttributes);
    }
}
