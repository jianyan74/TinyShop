

<?php
use common\helpers\Url;
?>

<ul class="nav nav-tabs">
    <li class="<?php if($type == 'company'){ ?>active<?php } ?>"><a href="<?= Url::to(['express-company/index'])?>">物流配送</a></li>
    <li class="<?php if($type == 'point'){ ?>active<?php } ?>"><a href="<?= Url::to(['pickup-point/index'])?>">门店自提</a></li>
    <li class="<?php if($type == 'local-distribution'){ ?>active<?php } ?>"><a href="<?= Url::to(['local-distribution-member/index'])?>">本地配送</a></li>
    <li class="<?php if($type == 'address'){ ?>active<?php } ?>"><a href="<?= Url::to(['company-address/edit'])?>">商家地址</a></li>
</ul>