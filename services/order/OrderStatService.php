<?php

namespace addons\TinyShop\services\order;

use Yii;
use common\enums\PayStatusEnum;
use common\helpers\ArrayHelper;
use common\helpers\EchantsHelper;
use common\components\Service;
use common\enums\StatusEnum;
use common\helpers\BcHelper;
use addons\TinyShop\common\models\order\Order;
use addons\TinyShop\common\enums\AccessTokenGroupEnum;
use addons\TinyShop\common\enums\OrderTypeEnum;

/**
 * Class OrderStatService
 * @package addons\TinyShop\services\order
 * @author jianyan74 <751393839@qq.com>
 */
class OrderStatService extends Service
{
    /**
     * 获取每天订单数量、总金额、产品数量
     *
     * @return array|\yii\db\ActiveRecord|null
     */
    public function getDayStatByTime($time)
    {
        return Order::find()
            ->select([
                'sum(product_count) as product_count',
                'count(id) as count',
                'sum(pay_money) as pay_money',
                "from_unixtime(created_at, '%Y-%c-%d') as day"
            ])
            ->where(['pay_status' => PayStatusEnum::YES])
            ->andWhere(['>', 'pay_time', $time])
            ->groupBy(['day'])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->andFilterWhere(['store_id' => Yii::$app->params['store_id']])
            ->asArray()
            ->all();
    }

    /**
     * 获取订单数量、总金额、产品数量
     *
     * @return array|\yii\db\ActiveRecord|null
     */
    public function getStatByTime($time, $select = [])
    {
        $select = ArrayHelper::merge([
            'sum(product_count) as product_count',
            'count(id) as count',
            'sum(pay_money) as pay_money'
        ], $select);
        return Order::find()
            ->select($select)
            ->where(['pay_status' => PayStatusEnum::YES])
            ->andWhere(['>', 'pay_time', $time])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->andFilterWhere(['store_id' => Yii::$app->params['store_id']])
            ->asArray()
            ->one();
    }

    /**
     * @param string $type
     * @param string $count_sql
     * @return array
     */
    public function getBetweenProductMoneyAndCountStatToEchant($type)
    {
        $fields = [
            'pay_money' => '下单金额',
        ];

        // 获取时间和格式化
        list($time, $format) = EchantsHelper::getFormatTime($type);
        // 获取数据
        return EchantsHelper::lineOrBarInTime(function ($start_time, $end_time, $formatting) {
            return Order::find()
                ->select([
                    'sum(pay_money) as pay_money',
                    "from_unixtime(created_at, '$formatting') as time"
                ])
                ->where(['pay_status' => PayStatusEnum::YES])
                ->andWhere(['between', 'pay_time', $start_time, $end_time])
                ->groupBy(['time'])
                ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
                ->andFilterWhere(['store_id' => Yii::$app->params['store_id']])
                ->asArray()
                ->all();
        }, $fields, $time, $format);
    }

    /**
     * @param string $type
     * @param string $count_sql
     * @return array
     */
    public function getBetweenCountStatToEchant($type)
    {
        $fields = [
            'count' => '订单笔数',
            'product_count' => '订单量',
        ];

        // 获取时间和格式化
        list($time, $format) = EchantsHelper::getFormatTime($type);
        // 获取数据
        return EchantsHelper::lineOrBarInTime(function ($start_time, $end_time, $formatting) {
            return Order::find()
                ->select([
                    'count(id) as count',
                    'sum(product_count) as product_count',
                    "from_unixtime(created_at, '$formatting') as time"
                ])
                ->where(['pay_status' => PayStatusEnum::YES])
                ->andWhere(['between', 'pay_time', $start_time, $end_time])
                ->groupBy(['time'])
                ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
                ->andFilterWhere(['store_id' => Yii::$app->params['store_id']])
                ->asArray()
                ->all();
        }, $fields, $time, $format);
    }

    /**
     * @param string $type
     * @param string $count_sql
     * @return array
     */
    public function getBetweenProductCountAndCountStatToEchant($type)
    {
        $fields = [
            'product_count' => '商品售出数',
        ];

        // 获取时间和格式化
        list($time, $format) = EchantsHelper::getFormatTime($type);
        // 获取数据
        return EchantsHelper::lineOrBarInTime(function ($start_time, $end_time, $formatting) {
            return Order::find()
                ->select([
                    'sum(product_count) as product_count',
                    "from_unixtime(created_at, '$formatting') as time"
                ])
                ->where(['pay_status' => PayStatusEnum::YES])
                ->andWhere(['between', 'pay_time', $start_time, $end_time])
                ->groupBy(['time'])
                ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
                ->asArray()
                ->all();
        }, $fields, $time, $format);
    }

    /**
     * @param string $type
     * @param string $count_sql
     * @return array
     */
    public function getOrderCreateCountStat($type)
    {
        $fields = [
            [
                'name' => '下单数量',
                'type' => 'bar',
                'field' => 'count',
            ],
            [
                'name' => '支付数量',
                'type' => 'bar',
                'field' => 'pay_count',
            ],
            [
                'name' => '下单支付转化率',
                'type' => 'line',
                'field' => 'pay_rate',
            ],
        ];

        // 获取时间和格式化
        list($time, $format) = EchantsHelper::getFormatTime($type);
        // 获取数据
        return EchantsHelper::lineOrBarInTime(function ($start_time, $end_time, $formatting) {
            $data = Order::find()
                ->select([
                    'count(id) as count',
                    'sum(pay_status) as pay_count',
                    "from_unixtime(created_at, '$formatting') as time"
                ])
                ->andWhere(['between', 'created_at', $start_time, $end_time])
                ->groupBy(['time'])
                ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
                ->asArray()
                ->all();

            foreach ($data as &$datum) {
                $datum['pay_rate'] = BcHelper::mul(BcHelper::div($datum['pay_count'], $datum['count']), 100);
            }

            return $data;
        }, $fields, $time, $format);
    }

    /**
     * 订单来源统计
     *
     * @return array
     */
    public function getFormStat($type)
    {
        $fields = array_values(AccessTokenGroupEnum::getMap());

        // 获取时间和格式化
        list($time, $format) = EchantsHelper::getFormatTime($type);
        // 获取数据
        return EchantsHelper::pie(function ($start_time, $end_time) use ($fields) {
            $data = Order::find()
                ->select(['count(id) as value', 'order_from'])
                ->where(['status' => StatusEnum::ENABLED])
                ->andFilterWhere(['between', 'created_at', $start_time, $end_time])
                ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
                ->groupBy(['order_from'])
                ->asArray()
                ->all();

            foreach ($data as &$datum) {
                $datum['name'] = AccessTokenGroupEnum::getValue($datum['order_from']);
            }

            return [$data, $fields];
        }, $time);
    }

    /**
     * 订单类型统计
     *
     * @return array
     */
    public function getOrderTypeStat($type)
    {
        $fields = array_values(OrderTypeEnum::getMap());

        // 获取时间和格式化
        list($time, $format) = EchantsHelper::getFormatTime($type);
        // 获取数据
        return EchantsHelper::pie(function ($start_time, $end_time) use ($fields) {
            $data = Order::find()
                ->select(['count(id) as value', 'order_type'])
                ->where(['status' => StatusEnum::ENABLED])
                ->andFilterWhere(['between', 'created_at', $start_time, $end_time])
                ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
                ->groupBy(['order_type'])
                ->asArray()
                ->all();

            foreach ($data as &$datum) {
                $datum['name'] = OrderTypeEnum::getValue($datum['order_type']);
            }

            return [$data, $fields];
        }, $time);
    }
}
