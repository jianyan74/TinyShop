<?php

namespace addons\TinyShop\common\traits;

use addons\TinyShop\common\models\member\Footprint;
use common\enums\StatusEnum;
use common\models\base\SearchModel;
use Yii;

/**
 * Trait MemberInfo
 * @package addons\TinyShop\common\traits
 * @author jianyan74 <751393839@qq.com>
 */
trait MemberInfo
{
    /**
     * @param $member_id
     * @return mixed
     */
    public function actionView($member_id)
    {
        $this->layout = '@backend/views/layouts/default';

        $member = Yii::$app->services->member->findById($member_id);
        return $this->render('@addons/TinyShop/backend/views/member/view', [
            'member' => $member,
            'member_id' => $member_id,
        ]);
    }

    /**
     * 首页
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionFootprint($member_id)
    {
        $this->layout = '@backend/views/layouts/default';

        $searchModel = new SearchModel([
            'model' => Footprint::class,
            'scenario' => 'default',
            'partialMatchAttributes' => ['num'], // 模糊查询
            'defaultOrder' => [
                'updated_at' => SORT_DESC,
            ],
            'pageSize' => $this->pageSize,
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams);
        $dataProvider->query
            ->andWhere(['member_id' => $member_id])
            ->andWhere(['>=', 'status', StatusEnum::DISABLED])
            ->with('product');

        return $this->render('@addons/TinyShop/backend/views/member/footprint', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'member_id' => $member_id,
        ]);
    }
}