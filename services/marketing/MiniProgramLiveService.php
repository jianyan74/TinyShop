<?php

namespace addons\TinyShop\services\marketing;

use common\enums\StatusEnum;
use common\helpers\UploadHelper;
use Yii;
use common\components\Service;
use addons\TinyShop\common\models\marketing\MiniProgramLive;
use addons\TinyShop\common\models\marketing\MiniProgramLiveGoods;
use addons\TinyShop\common\models\marketing\MiniProgramLiveReplay;
use yii\web\UnprocessableEntityHttpException;

/**
 * Class MiniProgramLiveService
 * @package addons\TinyShop\services\marketing
 * @author jianyan74 <751393839@qq.com>
 */
class MiniProgramLiveService extends Service
{
    /**
     * 同步房间
     *
     * @param $offset
     * @param $count
     * @return array|bool
     * @throws \EasyWeChat\Kernel\Exceptions\HttpException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function sync($offset, $count)
    {
        $lists = Yii::$app->services->miniProgramLive->syncRoom($offset, $count);
        if (empty($lists)) {
            return true;
        }

        $total = $lists['total'];
        // 房间列表
        $roomInfo = $lists['room_info'];
        foreach ($roomInfo as $vo) {
            $live = new MiniProgramLive();
            $live = $live->loadDefaultValues();
            $live->room_id = $vo['roomid'];
            $live->cover = $this->downloadUrl($vo['cover_img']);
            $live->name = $vo['name'];
            $live->start_time = $vo['start_time'];
            $live->end_time = $vo['end_time'];
            $live->anchor_name = $vo['anchor_name'];
            $live->live_status = $vo['live_status'];
            $live->share_img = $this->downloadUrl($vo['share_img']);
            if (!$live->save()) {
                throw new UnprocessableEntityHttpException($this->getError($live));
            }

            // 插入产品
            foreach ($vo['goods'] as $key => $goods) {
                $liveGoods = new MiniProgramLiveGoods();
                $liveGoods->live_id = $live->id;
                $liveGoods->name = $goods['name'];
                $liveGoods->cover = $this->downloadUrl($goods['cover_img']);
                $liveGoods->url =$goods['url'];
                $liveGoods->price = $goods['price'];
                $liveGoods->save();
            }
        }

        if ($total - ($offset + $count) > 0) {
            return [
                'offset' => ($offset + $count),
                'count' => $count
            ];
        }

        return true;
    }

    /**
     * @param $room_ids
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findRoomIds($room_ids)
    {
        return MiniProgramLive::find()
            ->where(['in', 'room_id', $room_ids])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->with(['goods'])
            ->all();
    }

    /**
     * 下载图片
     *
     * @param $url
     * @throws \League\Flysystem\FileExistsException
     * @throws \League\Flysystem\FileNotFoundException
     * @throws \yii\web\NotFoundHttpException
     */
    protected function downloadUrl($url)
    {
        $upload = new UploadHelper(['writeTable' => StatusEnum::DISABLED], 'images');
        $imgData = $upload->verifyUrl($url);
        $upload->save($imgData);
        $baseInfo = $upload->getBaseInfo();

        return $baseInfo['url'];
    }
}