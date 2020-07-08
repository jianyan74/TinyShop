<?php

namespace addons\TinyShop\api\modules\v1\controllers\common;

use Yii;
use yii\data\Pagination;
use common\enums\StatusEnum;
use api\controllers\OnAuthController;
use common\helpers\ArrayHelper;
use addons\TinyShop\common\models\pickup\Point;
use addons\TinyShop\api\modules\v1\forms\PickupPointSearchForm;

/**
 * Class PickupPointController
 * @package addons\TinyShop\api\modules\v1\controllers\common
 * @author jianyan74 <751393839@qq.com>
 */
class PickupPointController extends OnAuthController
{
    /**
     * @var string
     */
    public $modelClass = '';

    /**
     * @return array|\yii\data\ActiveDataProvider|\yii\db\ActiveRecord[]
     */
    public function actionIndex()
    {
        $search = new PickupPointSearchForm();
        $search->attributes = Yii::$app->request->get();
        $orderBy = ArrayHelper::merge($search->getOrderBy(), ['sort asc', 'id desc']);

        $data = Point::find()
            ->select($search->getSelect())
            ->where(['status' => StatusEnum::ENABLED])
            ->andFilterWhere(['like', 'title', $search->keyword])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()]);
        $pages = new Pagination(['totalCount' => $data->count(), 'pageSize' => $this->pageSize, 'validatePage' => false]);
        $models = $data->offset($pages->offset)
            ->orderBy(implode(',', $orderBy))
            ->asArray()
            ->limit($pages->limit)
            ->all();

        foreach ($models as &$model) {
            !isset($model['distance']) && $model['distance'] = 0;
            $model['distance'] = round($model['distance'], 2);
        }

        return $models;
    }
}