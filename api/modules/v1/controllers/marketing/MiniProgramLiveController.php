<?php

namespace addons\TinyShop\api\modules\v1\controllers\marketing;

use Yii;
use common\enums\StatusEnum;
use common\enums\MiniProgramLiveStatusEnum;
use yii\data\ActiveDataProvider;
use api\controllers\OnAuthController;
use addons\TinyShop\common\models\marketing\MiniProgramLive;

/**
 * Class MiniProgramLiveController
 * @package addons\TinyShop\api\modules\v1\controllers\marketing
 * @author jianyan74 <751393839@qq.com>
 */
class MiniProgramLiveController extends OnAuthController
{
    /**
     * @var MiniProgramLive
     */
    public $modelClass = MiniProgramLive::class;

    /**
     * 不用进行登录验证的方法
     * 例如： ['index', 'update', 'create', 'view', 'delete']
     * 默认全部需要验证
     *
     * @var array
     */
    protected $authOptional = ['index'];

    /**
     * @return array|ActiveDataProvider|\yii\db\ActiveRecord[]
     */
    public function actionIndex()
    {
        $is_recommend = Yii::$app->request->get('is_recommend');
        $live_status = Yii::$app->request->get('live_status');
        $where = [];
        switch ($live_status) {
            // 进行中
            case MiniProgramLiveStatusEnum::UNDERWAY :
                $where = [
                    'and',
                    ['<', 'start_time', time()],
                    ['>', 'end_time', time()]
                ];
                break;
            // 未开始
            case MiniProgramLiveStatusEnum::NOT_STARTED :
                $where = ['>', 'start_time', time()];
                break;
            // 已结束
            case MiniProgramLiveStatusEnum::END :
                $where = ['<', 'end_time', time()];
                break;
        }

        return new ActiveDataProvider([
            'query' => $this->modelClass::find()
                ->where(['status' => StatusEnum::ENABLED])
                ->andWhere(['in', 'live_status' , [MiniProgramLiveStatusEnum::UNDERWAY, MiniProgramLiveStatusEnum::NOT_STARTED, MiniProgramLiveStatusEnum::END]])
                ->andFilterWhere($where)
                ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
                ->andFilterWhere(['is_recommend' => $is_recommend])
                ->cache(60)
                ->orderBy('is_stick asc, id desc')
                ->asArray(),
            'pagination' => [
                'pageSize' => $this->pageSize,
                'validatePage' => false,// 超出分页不返回data
            ],
        ]);
    }
}