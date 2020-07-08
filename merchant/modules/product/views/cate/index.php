<?php

use common\helpers\Html;
use common\helpers\Url;
use jianyan\treegrid\TreeGrid;

$this->title = '分类管理';
$this->params['breadcrumbs'][] = ['label' => $this->title];

?>

<?= \common\widgets\jstree\JsTreeTable::widget([
    'title' => '分类管理',
    'name' => "userTree",
    'defaultData' => $models,
    'editUrl' => Url::to(['edit']),
    'deleteUrl' => Url::to(['delete']),
    'moveUrl' => Url::to(['move']),
]) ?>