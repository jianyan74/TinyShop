<?php

namespace addons\TinyShop\services\product;

use Yii;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use common\components\Service;
use common\helpers\TreeHelper;
use addons\TinyShop\common\models\product\Cate;

/**
 * Class Cate
 * @package addons\TinyShop\common\components\product
 * @author jianyan74 <751393839@qq.com>
 */
class CateService extends Service
{
    /**
     * 获取下拉
     *
     * @param string $id
     * @return array
     */
    public function getDropDownForEdit($id = '')
    {
        $list = Cate::find()
            ->where(['>=', 'status', StatusEnum::DISABLED])
            ->andWhere(['merchant_id' => Yii::$app->services->merchant->getNotNullId()])
            ->andFilterWhere(['<>', 'id', $id])
            ->select(['id', 'title', 'pid', 'level'])
            ->orderBy('sort asc')
            ->asArray()
            ->all();

        $models = ArrayHelper::itemsMerge($list);
        $data = ArrayHelper::map(ArrayHelper::itemsMergeDropDown($models), 'id', 'title');

        return ArrayHelper::merge([0 => '顶级分类'], $data);
    }

    /**
     * @return array
     */
    public function getMapList($merchant_id = '')
    {
        $models = ArrayHelper::itemsMerge($this->getList($merchant_id));

        return ArrayHelper::map(ArrayHelper::itemsMergeDropDown($models), 'id', 'title');
    }

    /**
     * @param string $pid
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getList($merchant_id = '')
    {
        $merchant_id === '' && $merchant_id = Yii::$app->services->merchant->getNotNullId();

        return Cate::find()
            ->select(['id', 'title', 'pid', 'cover', 'level'])
            ->where(['status' => StatusEnum::ENABLED])
            ->andWhere(['merchant_id' => $merchant_id])
            ->orderBy('sort asc, id desc')
            ->asArray()
            ->all();
    }

    /**
     * 获取首页推荐
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findByRecommend()
    {
        return Cate::find()
            ->select(['id', 'title', 'subhead', 'cover'])
            ->where(['status' => StatusEnum::ENABLED])
            ->andWhere(['is_recommend' => StatusEnum::ENABLED])
            ->andWhere(['merchant_id' => Yii::$app->services->merchant->getNotNullId()])
            ->orderBy('sort asc, id desc')
            ->cache(30)
            ->asArray()
            ->all();
    }

    /**
     * 自定义分类
     *
     * @param $limit
     * @param array $ids
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findByCustom($limit, $ids = [])
    {
        if (empty($ids)) {
            $condition = ['is_recommend' => StatusEnum::ENABLED];
        } else {
            $condition = ['in', 'id', $ids];
            $limit = count($ids);
        }

        $data = Cate::find()
            ->select(['id', 'title', 'subhead', 'cover'])
            ->where(['status' => StatusEnum::ENABLED])
            ->andWhere($condition)
            ->andWhere(['merchant_id' => Yii::$app->services->merchant->getNotNullId()])
            ->orderBy('sort asc, id desc')
            ->limit($limit)
            ->asArray()
            ->all();

        if (empty($ids)) {
            return $data;
        }

        // 排序后返回
        $newData = [];
        foreach ($ids as $id) {
            foreach ($data as $datum) {
                if ($id == $datum['id']) {
                    $newData[] = $datum;
                }
            }
        }

        return $newData;
    }

    /**
     * 获取所有下级id
     *
     * @param $id
     * @return array
     */
    public function findChildIdsById($id, $merchant_id = '')
    {
        if ($model = $this->findById($id, $merchant_id)) {
            $tree = $model['tree'] .  TreeHelper::prefixTreeKey($id);
            $list = $this->getChilds($tree);

            return ArrayHelper::merge([$id], array_column($list, 'id'));
        }

        return [];
    }

    /**
     * 获取所有下级
     *
     * @param $tree
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getChilds($tree)
    {
        return Cate::find()
            ->where(['like', 'tree', $tree . '%', false])
            ->andWhere(['>=', 'status', StatusEnum::DISABLED])
            ->asArray()
            ->all();
    }

    /**
     * 获取所有下级
     *
     * @param $tree
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getParentIds($ids)
    {
        $allTree = Cate::find()
            ->select(['id', 'tree'])
            ->where(['in', 'id', $ids])
            ->andWhere(['status' => StatusEnum::ENABLED])
            ->asArray()
            ->all();

        $parentIds = [];
        foreach ($allTree as $item) {
            $parentIds[$item['id']] = [];
            $parentIds[$item['id']][] = $item['id'];
            if (!empty(trim($item['tree']))) {
                $parentIds[$item['id']] = array_merge($parentIds[$item['id']], explode('-', trim($item['tree'])));
            }
        }

        return $parentIds;
    }

    /**
     * @param $id
     * @return array|\yii\db\ActiveRecord|null|Cate
     */
    public function findById($id, $merchant_id = '')
    {
        $merchant_id === '' && $merchant_id = Yii::$app->services->merchant->getNotNullId();

        return Cate::find()
            ->where(['id' => $id])
            ->andWhere(['>=', 'status', StatusEnum::DISABLED])
            ->andWhere(['merchant_id' => $merchant_id])
            ->asArray()
            ->one();
    }

    /**
     * @param $id
     * @return array|\yii\db\ActiveRecord|null|Cate
     */
    public function findByIds($ids)
    {
        return Cate::find()
            ->select(['id'])
            ->where(['in', 'id', $ids])
            ->andWhere(['>=', 'status', StatusEnum::DISABLED])
            ->andWhere(['merchant_id' => Yii::$app->services->merchant->getNotNullId()])
            ->asArray()
            ->column();
    }

    /**
     * 根据ID获取所有分类
     *
     * @param $ids
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findAllByIds($ids)
    {
        return Cate::find()
            ->where(['in', 'id', $ids])
            ->andWhere(['>=', 'status', StatusEnum::DISABLED])
            ->asArray()
            ->all();
    }

    /**
     * @param $level
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findByPId($id)
    {
        return Cate::find()
            ->where(['status' => StatusEnum::ENABLED, 'pid' => $id])
            ->asArray()
            ->all();
    }

    /**
     * @param $id
     * @return array|\yii\db\ActiveRecord|null|Cate
     */
    public function findAll($merchant_id = '')
    {
        $merchant_id === '' && $merchant_id = Yii::$app->services->merchant->getNotNullId();

        return Cate::find()
            ->where(['status' => StatusEnum::ENABLED])
            ->andWhere(['merchant_id' => $merchant_id])
            ->asArray()
            ->cache(30)
            ->all();
    }
}
