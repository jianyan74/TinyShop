<?php

namespace addons\TinyShop\api\modules\v1\controllers\product;

use Yii;
use yii\helpers\Json;
use yii\data\ActiveDataProvider;
use api\controllers\OnAuthController;
use common\enums\StatusEnum;
use addons\TinyShop\common\models\product\Evaluate;

/**
 * 商品评价
 *
 * Class EvaluateController
 * @package addons\TinyShop\api\modules\v1\controllers\product
 * @author jianyan74 <751393839@qq.com>
 */
class EvaluateController extends OnAuthController
{
    /**
     * @var Evaluate
     */
    public $modelClass = Evaluate::class;

    /**
     * 不用进行登录验证的方法
     * 例如： ['index', 'update', 'create', 'view', 'delete']
     * 默认全部需要验证
     *
     * @var array
     */
    protected $authOptional = ['index'];

    /**
     * @return ActiveDataProvider
     */
    public function actionIndex()
    {
        $product_id = Yii::$app->request->get('product_id');
        $explain_type = Yii::$app->request->get('explain_type');
        $has_again = Yii::$app->request->get('has_again');
        $has_content = Yii::$app->request->get('has_content');
        $has_cover = Yii::$app->request->get('has_cover');
        $has_video = Yii::$app->request->get('has_video');

        $model = new ActiveDataProvider([
            'query' => $this->modelClass::find()
                ->where(['status' => StatusEnum::ENABLED])
                ->andWhere(['product_id' => $product_id])
                ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
                ->andFilterWhere(['has_again' => $has_again])
                ->andFilterWhere(['has_content' => $has_content])
                ->andFilterWhere(['has_cover' => $has_cover])
                ->andFilterWhere(['has_video' => $has_video])
                ->andFilterWhere(['explain_type' => $explain_type])
                ->orderBy('id desc')
                ->asArray(),
            'pagination' => [
                'pageSize' => $this->pageSize,
                'validatePage' => false,// 超出分页不返回data
            ],
        ]);

        $data = $model->getModels();
        foreach ($data as &$datum) {
            empty($datum['again_covers']) && $datum['again_covers'] = [];
            !is_array($datum['again_covers']) && $datum['again_covers'] = Json::decode($datum['again_covers']);
            empty($datum['covers']) && $datum['covers'] = [];
            !is_array($datum['covers']) && $datum['covers'] = Json::decode($datum['covers']);
            // 匿名
            if ($datum['is_anonymous'] == StatusEnum::ENABLED) {
                $datum['member_id'] = '';
                $datum['member_nickname'] = '';
                $datum['member_head_portrait'] = '';
            }
        }

        $model->setModels($data);

        return $model;
    }

    /**
     * 权限验证
     *
     * @param string $action 当前的方法
     * @param null $model 当前的模型类
     * @param array $params $_GET变量
     * @throws \yii\web\BadRequestHttpException
     */
    public function checkAccess($action, $model = null, $params = [])
    {
        // 方法名称
        if (in_array($action, ['view', 'delete', 'update', 'create'])) {
            throw new \yii\web\BadRequestHttpException('权限不足');
        }
    }
}