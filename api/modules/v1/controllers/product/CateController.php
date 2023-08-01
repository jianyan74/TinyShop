<?php

namespace addons\TinyShop\api\modules\v1\controllers\product;

use Yii;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use common\helpers\TreeHelper;
use api\controllers\OnAuthController;
use addons\TinyShop\common\models\product\Cate;

/**
 * 商品分类
 *
 * Class CateController
 * @package addons\TinyShop\api\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class CateController extends OnAuthController
{
    /**
     * @var Cate
     */
    public $modelClass = Cate::class;

    /**
     * 不用进行登录验证的方法
     * 例如： ['index', 'update', 'create', 'view', 'delete']
     * 默认全部需要验证
     *
     * @var array
     */
    protected $authOptional = ['index', 'child'];

    /**
     * 首页
     *
     * @return array
     */
    public function actionIndex()
    {
        $list = Yii::$app->tinyShopService->productCate->getList();
        $cate = ArrayHelper::itemsMerge($list, 0, 'id', 'pid', 'child');

        return [
            'list' => $cate,
        ];
    }

    /**
     * 根据上级ID获取下级分类数据
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionChild()
    {
        $pid = Yii::$app->request->get('pid');
        $is_recommend = Yii::$app->request->get('is_recommend');
        $model = Yii::$app->tinyShopService->productCate->findById($pid);

        $list = Cate::find()
            ->select(['id', 'title', 'pid', 'cover', 'level'])
            ->where(['status' => StatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->andFilterWhere(['like', 'tree', $model['tree'] . TreeHelper::prefixTreeKey($model['id']) . '%', false])
            ->andFilterWhere(['is_recommend' => $is_recommend])
            ->orderBy('sort asc, id desc')
            ->asArray()
            ->all();

        return ArrayHelper::itemsMerge($list, $pid, 'id', 'pid', 'child');
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
        if (in_array($action, ['delete', 'update', 'view', 'create'])) {
            throw new \yii\web\BadRequestHttpException('权限不足');
        }
    }
}
