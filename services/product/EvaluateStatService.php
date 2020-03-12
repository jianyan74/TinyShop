<?php

namespace addons\TinyShop\services\product;

use common\components\Service;
use addons\TinyShop\api\modules\v1\forms\EvaluateStatForm;
use addons\TinyShop\common\models\product\EvaluateStat;

/**
 * Class EvaluateStatService
 * @package addons\TinyShop\services\product
 * @author jianyan74 <751393839@qq.com>
 */
class EvaluateStatService extends Service
{
    /**
     * 更新评价数量
     *
     * @param EvaluateStatForm $evaluateStatForm
     * @param $product_id
     */
    public function updateNum(EvaluateStatForm $evaluateStatForm, $product_id)
    {
        // TODO 测试用，等正式了移除
        if (!EvaluateStat::findOne(['product_id' => $product_id])) {
            $model = new EvaluateStat();
            $model = $model->loadDefaultValues();
            $model->product_id = $product_id;
            $model->save();
        }

        $updateData = [];
        if ($evaluateStatForm->has_cover == true) {
            $updateData['cover_num'] = 1;
        }

        if ($evaluateStatForm->has_video == true) {
            $updateData['video_num'] = 1;
        }

        if ($evaluateStatForm->has_again == true) {
            $updateData['again_num'] = 1;
        }

        if ($evaluateStatForm->has_good == true) {
            $updateData['good_num'] = 1;
        }

        if ($evaluateStatForm->has_ordinary == true) {
            $updateData['ordinary_num'] = 1;
        }

        if ($evaluateStatForm->has_negative == true) {
            $updateData['negative_num'] = 1;
        }

        // 总数
        $updateData['total_num'] = 1;

        !empty($updateData) && EvaluateStat::updateAllCounters($updateData, ['product_id' => $product_id]);
    }
}