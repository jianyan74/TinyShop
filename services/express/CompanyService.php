<?php

namespace addons\TinyShop\services\express;

use Yii;
use common\enums\LogisticsTypeEnum;
use common\enums\StatusEnum;
use common\components\Service;
use common\helpers\ArrayHelper;
use addons\TinyShop\common\models\express\Company;

/**
 * Class CompanyService
 * @package addons\TinyShop\services\express
 * @author jianyan74 <751393839@qq.com>
 */
class CompanyService extends Service
{
    /**
     * 获取物流状态
     *
     * @param $express_no
     * @param $express_company
     * @return array
     */
    public function getTrace($express_no, $express_company)
    {
        if (empty($express_no)) {
            return [];
        }

        // 配置信息
        $setting = Yii::$app->tinyShopService->config->setting();

        try {
            // aliyun(阿里云)、juhe(聚合)、kdniao(快递鸟)、kd100(快递100)
            switch ($setting->logistics_type) {
                case LogisticsTypeEnum::JUHE :
                    $logistics = Yii::$app->logistics->juhe($express_no, $express_company, true);
                    break;
                case LogisticsTypeEnum::KD100 :
                    $logistics = Yii::$app->logistics->kd100($express_no, $express_company, true);
                    break;
                case LogisticsTypeEnum::KDNIAO :
                    $logistics = Yii::$app->logistics->kdniao($express_no, $express_company, true);
                    break;
                default :
                    $logistics = Yii::$app->logistics->aliyun($express_no, null, true);
                    break;
            }

            return $logistics->getList();
        } catch (\Exception $e) {
            Yii::debug($e->getMessage());
        }

        return [];
    }

    /**
     * @param $id
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findById($id)
    {
        return Company::find()
            ->where(['id' => $id, 'status' => StatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->one();
    }

    /**
     * @return array
     */
    public function getMapList()
    {
        return ArrayHelper::map($this->getList(), 'id', 'title');
    }

    /**
     * @param $id
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findByTitles($titles)
    {
        return Company::find()
            ->where(['status' => StatusEnum::ENABLED])
            ->andWhere(['in', 'title', $titles])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->asArray()
            ->all();
    }

    /**
     * 获取默认物流
     *
     * @return array|null|\yii\db\ActiveRecord
     */
    public function getDefault()
    {
        return Company::find()
            ->where(['is_default' => StatusEnum::ENABLED, 'status' => StatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->asArray()
            ->one();
    }

    /**
     * 获取列表
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getList()
    {
        return Company::find()
            ->where(['status' => StatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->orderBy('is_default desc, sort asc, id desc')
            ->asArray()
            ->all();
    }
}