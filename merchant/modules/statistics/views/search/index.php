<?php

use common\helpers\Url;
use yii\grid\GridView;

$this->title = '搜索分析';
$this->params['breadcrumbs'][] = ['label' => '搜索分析'];
$this->params['breadcrumbs'][] = ['label' => $this->title];

?>

<div class="row">
    <div class="col-sm-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="<?= Url::to(['index']) ?>"> 综合搜索统计</a></li>
                <li><a href="<?= Url::to(['record']) ?>"> 每日搜索记录</a></li>
            </ul>
            <div class="tab-content">
                <div class="active tab-pane">
                    <?= \common\widgets\echarts\Echarts::widget([
                        'theme' => 'wordcloud',
                        'config' => [
                            'server' => Url::to(['data']),
                            'height' => '500px'
                        ]
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>
