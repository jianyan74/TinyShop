<?php

namespace addons\TinyShop\merchant\controllers;

use addons\TinyShop\common\traits\MemberInfo;
use common\traits\MemberSelectAction;

/**
 * Class MemberController
 * @package addons\TinyShop\merchant\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class MemberController extends BaseController
{
    use MemberSelectAction, MemberInfo;
}