<?php

namespace addons\TinyShop\merchant\controllers;

use Yii;
use common\enums\StatusEnum;
use common\models\member\Member;

/**
 * Class MemberController
 * @package addons\TinyShop\merchant\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class MemberController extends BaseController
{
    /**
     * select2 查询
     *
     * @param null $q
     * @param null $id
     * @return array
     */
    public function actionSelect2($q = null, $id = null)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $out = [
            'results' => [
                'id' => '',
                'text' => ''
            ]
        ];

        if (!is_null($q)) {
            $data = Member::find()
                ->select('id, mobile as text')
                ->where(['like', 'mobile', $q])
                ->andWhere(['status' => StatusEnum::ENABLED])
                ->andWhere(['merchant_id' => $this->getMerchantId()])
                ->limit(10)
                ->asArray()
                ->all();

            $out['results'] = array_values($data);
        } elseif ($id > 0) {
            $out['results'] = ['id' => $id, 'text' => Member::findOne($id)->mobile];
        }

        return $out;
    }
}