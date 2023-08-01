<?php

namespace addons\TinyShop\services\common;

use Yii;
use common\components\Service;
use common\helpers\EchantsHelper;
use addons\TinyShop\common\models\common\SearchHistory;

/**
 * Class SearchHistoryService
 * @package addons\TinyShop\services\common
 * @author jianyan74 <751393839@qq.com>
 */
class SearchHistoryService extends Service
{
    /**
     * @param $keyword
     */
    public function create($keyword, $member_id)
    {
        $model = SearchHistory::find()
            ->where([
                'keyword' => $keyword,
                'member_id' => $member_id,
                'search_date' => date('Y-m-d')
            ])
            ->one();

        if (!$model) {
            $model = new SearchHistory();
            $model = $model->loadDefaultValues();
            $model->search_date = date('Y-m-d');
            $model->keyword = $keyword;
            $model->member_id = $member_id;
            $model->ip = Yii::$app->request->userIP;
        }

        $model->num += 1;
        $model->save();
    }

    /**
     * @param $type
     * @return array
     */
    public function getBetweenCountStat($type)
    {
        // 获取时间和格式化
        list($time, $format) = EchantsHelper::getFormatTime($type);

        // 获取数据
        return EchantsHelper::wordCloud(function ($start_time, $end_time) {
            return SearchHistory::find()
                ->select([
                    'sum(num) as value',
                    'keyword as name',
                ])
                ->andWhere(['between', 'created_at', $start_time, $end_time])
                ->groupBy(['keyword'])
                ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
                ->asArray()
                ->all();
        }, $time);
    }
}
