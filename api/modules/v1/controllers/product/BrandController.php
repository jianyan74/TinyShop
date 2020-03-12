<?php

namespace addons\TinyShop\api\modules\v1\controllers\product;

use yii\data\ActiveDataProvider;
use common\enums\StatusEnum;
use api\controllers\OnAuthController;
use addons\TinyShop\common\models\product\Brand;

/**
 * 品牌
 *
 * Class BrandController
 * @package addons\TinyShop\api\controllers\product
 * @author jianyan74 <751393839@qq.com>
 */
class BrandController extends OnAuthController
{
    /**
     * @var Brand
     */
    public $modelClass = Brand::class;

    /**
     * 不用进行登录验证的方法
     * 例如： ['index', 'update', 'create', 'view', 'delete']
     * 默认全部需要验证
     *
     * @var array
     */
    protected $authOptional = ['index'];

    /**
     * 首页
     *
     * @return ActiveDataProvider
     */
    public function actionIndex()
    {
        return new ActiveDataProvider([
            'query' => $this->modelClass::find()
                ->where(['status' => StatusEnum::ENABLED])
                ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
                ->orderBy('sort asc, id desc')
                ->asArray(),
            'pagination' => [
                'pageSize' => $this->pageSize,
                'validatePage' => false,// 超出分页不返回data
            ],
        ]);
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