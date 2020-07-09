<?php

namespace addons\TinyShop\common\models\product;

use Yii;
use yii\db\ActiveQuery;
use common\behaviors\MerchantBehavior;
use common\enums\StatusEnum;
use common\helpers\StringHelper;
use common\traits\HasOneMerchant;
use addons\TinyShop\common\enums\CommonTypeEnum;
use addons\TinyShop\common\enums\OrderStatusEnum;
use addons\TinyShop\common\models\common\Collect;
use addons\TinyShop\common\models\order\OrderProduct;

/**
 * This is the model class for table "{{%addon_shop_product}}".
 *
 * @property string $id
 * @property int $merchant_id 商家编号
 * @property string $name 商品标题
 * @property string $picture 商品主图
 * @property string $cate_id 商品分类编号
 * @property string $brand_id 品牌编号
 * @property int $type_id 类型编号
 * @property string $sketch 简述
 * @property string $intro 商品描述
 * @property string $keywords 商品关键字
 * @property string $tags 标签
 * @property string $marque 商品型号
 * @property string $barcode 仓库条码
 * @property int $sales 虚拟购买量
 * @property int $real_sales 实际销量
 * @property string $price 商品价格
 * @property string $market_price 市场价格
 * @property string $cost_price 成本价
 * @property string $wholesale_price 拼团价格
 * @property int $stock 库存量
 * @property int $warning_stock 库存警告
 * @property string $covers 幻灯片
 * @property string $posters 宣传海报
 * @property int $state 审核状态 -1 审核失败 0 未审核 1 审核成功
 * @property string $is_package 是否是套餐
 * @property string $is_attribute 启用商品规格
 * @property int $sort 排序
 * @property int $product_status 商品状态 0下架，1正常，10违规（禁售）
 * @property int $shipping_type 运费类型 1免邮2买家付邮费
 * @property string $shipping_fee 运费
 * @property int $shipping_fee_id 物流模板id
 * @property int $shipping_fee_type 计价方式1.计件2.体积3.重量
 * @property string $product_weight 商品重量
 * @property string $product_volume 商品体积
 * @property int $marketing_type 促销类型 0无促销，1团购，2限时折扣
 * @property int $marketing_id 促销活动ID
 * @property string $marketing_price 商品促销价格
 * @property int $point_exchange_type 积分兑换类型
 * @property int $point_exchange 积分兑换
 * @property int $max_use_point 积分抵现最大可用积分数 0为不可使用
 * @property int $integral_give_type 积分赠送类型 0固定值 1按比率
 * @property int $give_point 购买商品赠送积分
 * @property int $min_buy 最少买几件
 * @property int $max_buy 限购 0 不限购
 * @property string $view 商品点击数量
 * @property string $star 好评星级
 * @property string $collect_num 收藏数量
 * @property string $comment_num 评价数
 * @property int $transmit_num 分享数
 * @property string $province_id 一级地区id
 * @property string $city_id 二级地区id
 * @property int $area_id 三级地区
 * @property string $address_name 地址
 * @property int $is_stock_visible 库存显示 0不显示1显示
 * @property int $is_hot 是否热销商品
 * @property int $is_recommend 是否推荐
 * @property int $is_new 是否新品
 * @property int $is_bill 是否开具增值税发票 1是，0否
 * @property int $base_attribute_id 商品类型
 * @property string $base_attribute_format 商品规格
 * @property double $match_point 实物与描述相符（根据评价计算）
 * @property double $match_ratio 实物与描述相符（根据评价计算）百分比
 * @property int $sale_date 上下架时间
 * @property int $is_virtual 是否虚拟商品
 * @property int $production_date 生产日期
 * @property string $shelf_life 保质期
 * @property int $is_open_presell 是否支持预售
 * @property int $presell_time 预售发货时间
 * @property int $presell_day 预售发货天数
 * @property int $presell_delivery_type 预售发货方式1. 按照预售发货时间 2.按照预售发货天数
 * @property string $presell_price 预售金额
 * @property string $unit 商品单位
 * @property string $video_url 展示视频
 * @property int $supplier_id 供货商id
 * @property int $is_open_commission 是否支持分销
 * @property int $status 状态
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class Product extends \common\models\base\BaseModel
{
    use MerchantBehavior, HasOneMerchant;

    const PRODUCT_STATUS_PUTAWAY = 1;
    const PRODUCT_STATUS_SOLD_OUT = 0;
    const PRODUCT_STATUS_FORBID = 10; // 禁售

    /**
     * 上下架
     *
     * @var array
     */
    public static $productStatusExplain = [
        self::PRODUCT_STATUS_PUTAWAY => '上架',
        self::PRODUCT_STATUS_SOLD_OUT => '下架',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_shop_product}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'cate_id', 'price', 'intro', 'covers', 'stock', 'warning_stock'], 'required'],
            [
                [
                    'supplier_id',
                    'integral_give_type',
                    'base_attribute_id',
                    'merchant_id',
                    'type_id',
                    'brand_id',
                    'stock',
                    'warning_stock',
                    'state',
                    'sort',
                    'product_status',
                    'shipping_type',
                    'shipping_fee_id',
                    'shipping_fee_type',
                    'marketing_id',
                    'point_exchange_type',
                    'point_exchange',
                    'give_point',
                    'min_buy',
                    'max_buy',
                    'view',
                    'collect_num',
                    'star',
                    'comment_num',
                    'transmit_num',
                    'province_id',
                    'city_id',
                    'area_id',
                    'is_stock_visible',
                    'is_hot',
                    'is_recommend',
                    'is_new',
                    'is_bill',
                    'is_virtual',
                    'is_open_presell',
                    'presell_delivery_type',
                    'presell_day',
                    'status',
                    'created_at',
                    'updated_at',
                ],
                'integer',
            ],
            [['intro', 'posters', 'is_package', 'is_attribute'], 'string'],
            [
                [
                    'price',
                    'market_price',
                    'cost_price',
                    'wholesale_price',
                    'shipping_fee',
                    'product_weight',
                    'product_volume',
                    'marketing_price',
                    'max_use_point',
                    'presell_price',
                ],
                'number',
                'min' => 0,
            ],
            [
                [
                    'shelf_life',
                    'total_sales',
                    'sales',
                    'real_sales',
                ],
                'integer',
                'min' => 0,
            ],
            [['covers', 'presell_time', 'production_date', 'cate_id'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['sketch', 'keywords', 'address_name'], 'string', 'max' => 200],
            [['marque', 'barcode', 'picture', 'video_url', 'marketing_type'], 'string', 'max' => 100],
            [['unit'], 'string', 'max' => 20],
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
            'name' => '商品名称',
            'cate_id' => '分类',
            'merchant_id' => 'Merchant ID',
            'type_id' => '产品类型',
            'sketch' => '商品促销语',
            'intro' => '商品描述',
            'keywords' => '关键字',
            'tags' => '标签',
            'marque' => '商家编码',
            'barcode' => '仓库条码',
            'brand_id' => '品牌',
            'price' => '销售价',
            'market_price' => '市场价',
            'cost_price' => '成本价',
            'wholesale_price' => '拼团价',
            'stock' => '总库存',
            'warning_stock' => '库存报警',
            'covers' => '幻灯片',
            'picture' => '主图',
            'posters' => '海报',
            'video_url' => '展示视频',
            'base_attribute_id' => '商品类型',
            'state' => '审核状态',
            'is_package' => '是否套餐',
            'is_attribute' => '商品规格',
            'sort' => '排序',
            'product_status' => '商品状态',
            'production_date' => '生产日期',
            'shelf_life' => '保质期',
            'shipping_type' => '运费设置',
            'shipping_fee' => '运费',
            'shipping_fee_id' => '物流公司',
            'shipping_fee_type' => '计价方式',
            'product_weight' => '商品重量',
            'product_volume' => '商品体积',
            'marketing_type' => '促销类型',
            'marketing_id' => '促销活动id',
            'marketing_price' => '商品促销价格',
            'point_exchange_type' => '积分兑换设置',
            'point_exchange' => '兑换所需积分',
            'max_use_point' => '最大可使用积分',
            'integral_give_type' => '积分赠送类型',
            'give_point' => '赠送积分',
            'min_buy' => '最少购买数',
            'max_buy' => '每人限购',
            'view' => '基础点击',
            'sales' => '基础销量',
            'real_sales' => '销量',
            'collect_num' => '基础收藏',
            'star' => '好评星级',
            'transmit_num' => '基础分享',
            'comment_num' => '评价数',
            'province_id' => '所在省',
            'city_id' => '所在市',
            'area_id' => '所在区',
            'is_open_presell' => '是否支持预售',
            'presell_time' => '预售发货时间',
            'presell_day' => '预售发货天数',
            'presell_delivery_type' => '预售发货方式',
            'presell_price' => '预售金额',
            'unit' => '商品单位',
            'is_stock_visible' => '库存显示',
            'is_hot' => '热门',
            'is_recommend' => '推荐',
            'is_new' => '新品',
            'is_bill' => '是否开票',
            'is_virtual' => '是否虚拟商品',
            'status' => '状态',
            'supplier_id' => '供货商',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * 关联属性
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAttributeValue()
    {
        return $this->hasMany(AttributeValue::class, ['product_id' => 'id'])
            ->select(['id', 'product_id', 'base_attribute_value_id', 'title', 'value'])
            ->where(['status' => StatusEnum::ENABLED])
            ->orderBy('sort asc');
    }

    /**
     * 关联规格和规格值
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSpecWithSpecValue($product_id)
    {
        return $this->hasMany(Spec::class, ['product_id' => 'id'])
            ->with([
                'value' => function (ActiveQuery $query) use ($product_id) {
                    $query->andWhere(['product_id' => $product_id]);
                },
            ])
            ->where(['status' => StatusEnum::ENABLED]);
    }

    /**
     * 关联规格
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSpec()
    {
        return $this->hasMany(Spec::class, ['product_id' => 'id']);
    }

    /**
     * 关联规格值
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSpecValue()
    {
        return $this->hasMany(SpecValue::class, ['product_id' => 'id']);
    }

    /**
     * 关联sku
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSku()
    {
        return $this->hasMany(Sku::class, ['product_id' => 'id'])
            ->where(['status' => StatusEnum::ENABLED])
            ->select(['id', 'product_id', 'picture', 'price', 'market_price', 'cost_price', 'wholesale_price', 'name', 'stock', 'code', 'status', 'data']);
    }

    /**
     * 关联第一个sku
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFirstSku()
    {
        return $this->hasOne(Sku::class, ['product_id' => 'id'])
            ->where(['status' => StatusEnum::ENABLED])
            ->select(['id', 'product_id', 'name', 'picture', 'price', 'market_price', 'cost_price', 'wholesale_price', 'stock', 'status', 'code', 'data'])
            ->orderBy('id asc');
    }

    /**
     * 关最小sku
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMinPriceSku()
    {
        return $this->hasOne(Sku::class, ['product_id' => 'id'])
            ->where(['status' => StatusEnum::ENABLED])
            ->select(['id', 'product_id', 'price', 'market_price', 'cost_price', 'wholesale_price', 'stock', 'code'])
            ->orderBy('price');
    }

    /**
     * 关联分类
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCate()
    {
        return $this->hasOne(Cate::class, ['id' => 'cate_id'])
            ->select(['id', 'title']);
    }

    /**
     * 关联品牌
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBrand()
    {
        return $this->hasOne(Brand::class, ['id' => 'brand_id'])->select(['id', 'title']);
    }

    /**
     * 关联评价标签
     *
     * @return \yii\db\ActiveQuery
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
            ->select([
                'id',
                'product_id',
                'sku_name',
                'content',
                'covers',
                'explain_first',
                'member_id',
                'member_nickname',
                'member_head_portrait',
                'is_anonymous',
                'scores',
                'again_content',
                'again_covers',
                'again_explain',
                'again_addtime',
                'explain_type',
                'created_at',
            ])
            ->limit(10)
            ->orderBy('id desc')
            ->asArray();
    }

    /**
     * 我的收藏
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMyCollect()
    {
        return $this->hasOne(Collect::class, ['topic_id' => 'id'])
            ->where(['topic_type' => CommonTypeEnum::PRODUCT])
            ->andWhere(['status' => StatusEnum::ENABLED]);
    }

    /**
     * 我的购买总数量
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMyGet()
    {
        return $this->hasOne(OrderProduct::class, ['product_id' => 'id'])
            ->select(['sum(num) as all_num', 'product_id'])
            ->where(['status' => StatusEnum::ENABLED])
            ->andWhere(['in', 'order_status', OrderStatusEnum::haveBought()])
            ->andWhere(['merchant_id' => Yii::$app->services->merchant->getId()])
            ->groupBy('product_id');
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        $this->presell_time = StringHelper::dateToInt($this->presell_time);
        $this->production_date = StringHelper::dateToInt($this->production_date);
        empty($this->supplier_id) && $this->supplier_id = 0;
        empty($this->brand_id) && $this->brand_id = 0;
        empty($this->province_id) && $this->province_id = 0;
        empty($this->city_id) && $this->city_id = 0;
        empty($this->area_id) && $this->area_id = 0;

        // 让sku失效
        if (in_array($this->status, [StatusEnum::DELETE, StatusEnum::DISABLED]) || $this->product_status == StatusEnum::DISABLED) {
            Yii::$app->tinyShopService->memberCartItem->loseByProductIds([$this->id]);
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