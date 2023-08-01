<?php

use common\helpers\Url;

?>

<ul class="nav nav-tabs">
    <li class="<?php if($type == 'company'){ ?>active<?php } ?>"><a href="<?= Url::to(['express-company/index'])?>">物流配送</a></li>
    <li class="<?php if($type == 'local-distribution'){ ?>active<?php } ?>"><a href="<?= Url::to(['local-config/edit'])?>">同城配送</a></li>
    <li class="<?php if($type == 'address'){ ?>active<?php } ?>"><a href="<?= Url::to(['merchant-address/index'])?>">商家地址库</a></li>
</ul>
