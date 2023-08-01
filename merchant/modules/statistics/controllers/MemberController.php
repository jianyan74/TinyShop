<?php

namespace addons\TinyShop\merchant\modules\statistics\controllers;

use common\enums\PayStatusEnum;
use Yii;
use common\enums\StatusEnum;
use common\models\base\SearchModel;
use addons\TinyShop\common\models\order\Order;
use addons\TinyShop\merchant\controllers\BaseController;
use addons\TinyShop\common\enums\MemberActiveEnum;
use addons\TinyShop\merchant\modules\statistics\forms\OrderForm;

/**
 * Class MemberController
 * @package addons\TinyShop\merchant\modules\statistics\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class MemberController extends BaseController
{
    public function actionIndex()
    {
        $start_time = Yii::$app->request->get('start_time', date('Y-m-d', strtotime("-60 day")));
        $end_time = Yii::$app->request->get('end_time', date('Y-m-d', strtotime("+1 day")));

        $searchModel = new SearchModel([
            'model' => Order::class,
            'scenario' => 'default',
            'partialMatchAttributes' => [], // 模糊查询
            'defaultOrder' => [
                'pay_money' => SORT_DESC,
            ],
            'pageSize' => $this->pageSize,
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams);
        $dataProvider->query
            ->select([
                'buyer_id',
                'sum(product_count) as product_count',
                'sum(pay_money) as pay_money',
                'sum(refund_money) as refund_money'
            ])
            ->where(['pay_status' => PayStatusEnum::YES])
            ->andWhere(['>', 'buyer_id', 0])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->andFilterWhere(['between', 'created_at', strtotime($start_time), strtotime($end_time)])
            ->groupBy('buyer_id')
            ->with(['member']);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'start_time' => $start_time,
            'end_time' => $end_time,
        ]);
    }

    /**
     * @return string
     */
    public function actionActive()
    {
        $type = Yii::$app->request->get('type', MemberActiveEnum::ACTIVE);
        $time = MemberActiveEnum::getTime($type);

        $searchModel = new SearchModel([
            'model' => OrderForm::class,
            'scenario' => 'default',
            'partialMatchAttributes' => [], // 模糊查询
            'defaultOrder' => [
                'created_at' => SORT_DESC,
            ],
            'pageSize' => $this->pageSize,
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams);
        $dataProvider->query
            ->select([
                'buyer_id',
                'max(created_at) as created_at',
                'max(id) as id',
                'count(id) as count',
                'sum(product_count) as product_count',
                'sum(pay_money) as pay_money',
                'sum(refund_money) as refund_money'
            ])
            ->where(['pay_status' => PayStatusEnum::YES])
            ->andWhere(['>', 'buyer_id', 0])
            ->andWhere(['>=', 'status', StatusEnum::DISABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->having(['between', 'created_at', $time['start_time'], $time['end_time']])
            ->groupBy('buyer_id')
            ->with(['member', 'order']);

        return $this->render($this->action->id, [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'type' => $type,
            'time' => $time,
        ]);
    }
}
