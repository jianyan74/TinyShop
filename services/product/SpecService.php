<?php

namespace addons\TinyShop\services\product;

use Yii;
use common\components\Service;
use addons\TinyShop\common\models\product\Spec;

/**
 * Class SpecService
 * @package addons\TinyShop\services\product
 * @author jianyan74 <751393839@qq.com>
 */
class SpecService extends Service
{
    /**
     * 获取编辑的数据
     *
     * @param $product_id
     * @return array
     */
    public function getEditData($product_id)
    {
        $models = $this->getListWithValue($product_id);
        $data = [];
        foreach ($models as $model) {
            foreach ($model['value'] as $item) {
                $data[] = [
                    'id' => $item['base_spec_value_id'],
                    'title' => $item['title'],
                    'pid' => $item['base_spec_id'],
                    'ptitle' => $model['name'],
                    'sort' => $item['sort'],
                ];
            }
        }

        return $data;
    }

    /**
     * 判断规格是否被使用
     *
     * @param $id
     * @return array|null|\yii\db\ActiveRecord
     */
    public function has($id)
    {
        return Spec::find()
            ->where(['base_spec_id' => $id])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->asArray()
            ->one();
    }

    /**
     * 获取列表
     *
     * @param $product_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getList($product_id)
    {
        return Spec::find()
            ->where(['product_id' => $product_id])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->orderBy('sort asc')
            ->asArray()
            ->all();
    }

    /**
     * 获取规格属性列表
     *
     * @param $product_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getListWithValue($product_id)
    {
        return Spec::find()
            ->where(['product_id' => $product_id])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->select(['id', 'base_spec_id', 'title', 'show_type'])
            ->with(['value' => function($query) use ($product_id) {
                return $query->andWhere(['product_id' => $product_id]);
            }])
            ->orderBy('sort asc')
            ->asArray()
            ->all();
    }
}