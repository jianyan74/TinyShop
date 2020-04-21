<?php

namespace addons\TinyShop\api\modules\v1\controllers\member;

use Yii;
use yii\data\ActiveDataProvider;
use common\enums\StatusEnum;
use addons\TinyShop\common\enums\CommonTypeEnum;
use addons\TinyShop\common\models\common\Collect;
use api\controllers\UserAuthController;

/**
 * 我的收藏
 *
 * Class CollectController
 * @package addons\TinyShop\api\modules\v1\controllers\member
 * @author jianyan74 <751393839@qq.com>
 */
class CollectController extends UserAuthController
{
    /**
     * @var Collect
     */
    public $modelClass = Collect::class;

    /**
     * 首页
     *
     * @return ActiveDataProvider
     */
    public function actionIndex()
    {
        $topic_type = Yii::$app->request->get('topic_type', CommonTypeEnum::PRODUCT);

        return new ActiveDataProvider([
            'query' => $this->modelClass::find()
                ->where(['status' => StatusEnum::ENABLED, 'member_id' => Yii::$app->user->identity->member_id])
                ->andWhere(['topic_type' => $topic_type])
                ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
                ->with(['product', 'merchant'])
                ->orderBy('updated_at desc')
                ->asArray(),
            'pagination' => [
                'pageSize' => $this->pageSize,
                'validatePage' => false,// 超出分页不返回data
            ],
        ]);
    }
}